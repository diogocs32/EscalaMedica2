<?php

namespace App\Http\Controllers;

use App\Models\EscalaPublicada;
use App\Models\AlocacaoPublicada;
use App\Models\Plantonista;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EscalaPublicadaController extends Controller
{
    /**
     * Tela de edição da escala publicada em visão mensal (por dia do mês).
     */
    public function edit(EscalaPublicada $escalaPublicada)
    {
        $escalaPublicada->load(['unidade.cidade', 'escalaPadrao', 'alocacoes.turno', 'alocacoes.setor', 'alocacoes.plantonista']);

        // Dias do mês a partir de ano/mes da escala
        $ano = (int) $escalaPublicada->ano;
        $mes = (int) $escalaPublicada->mes;
        $inicioMes = Carbon::create($ano, $mes, 1);
        $fimMes = (clone $inicioMes)->endOfMonth();
        $daysInMonth = $fimMes->day;

        // Obter data de vigência da escala padrão
        $dataVigencia = $escalaPublicada->escalaPadrao->data_vigencia ?? $inicioMes;

        // Todos os turnos e setores para montar as colunas
        // Ordenar turnos por hora_inicio para seguir sequência cronológica
        $turnos = \App\Models\Turno::orderBy('hora_inicio')->get();
        $setores = \App\Models\Setor::orderBy('id')->get();

        // Criar mapa de alocações: [data][turno_id][setor_id][] => array de alocações
        $mapaAlocacoes = [];
        foreach ($escalaPublicada->alocacoes as $aloc) {
            $key = Carbon::parse($aloc->data)->format('Y-m-d');
            $mapaAlocacoes[$key][$aloc->turno_id][$aloc->setor_id][] = $aloc;
        }

        // Construir linhas: dia do mês + semana do ciclo + slots
        $rows = [];
        for ($dia = 1; $dia <= $daysInMonth; $dia++) {
            $data = Carbon::create($ano, $mes, $dia);
            $key = $data->format('Y-m-d');

            // Calcular semana do ciclo (1-5) baseado no dia do mês
            // Semana 1: dias 1-7, Semana 2: dias 8-14, etc.
            $semana = (int) ceil($dia / 7);

            $row = [
                'dia' => $dia,
                'semana' => $semana,
                'data' => $data,
                'weekday' => $data->locale('pt_BR')->isoFormat('dddd'),
                'slots' => []
            ];

            // Para cada turno×setor, buscar TODAS as alocações (pode haver múltiplas)
            foreach ($turnos as $turno) {
                foreach ($setores as $setor) {
                    $row['slots'][$turno->id][$setor->id] = $mapaAlocacoes[$key][$turno->id][$setor->id] ?? [];
                }
            }

            $rows[] = $row;
        }

        // Plantonistas para atribuição
        $plantonistas = Plantonista::query()
            ->when(method_exists(Plantonista::class, 'scopeAtivo'), fn($q) => $q->ativo())
            ->orderBy('nome')
            ->get();

        // Mapear ocupações por dia e plantonista para desabilitar opções com conflito no select
        // Formato: [YYYY-mm-dd][plantonista_id] => [ ['inicio'=>'HH:MM:SS','fim'=>'HH:MM:SS','alocacao_id'=>int,'turno_id'=>int,'setor_id'=>int], ... ]
        $ocupacaoPorDia = [];
        foreach ($escalaPublicada->alocacoes as $aloc) {
            if (!$aloc->plantonista_id || !$aloc->turno) continue;
            $inicio = optional($aloc->turno->hora_inicio) ? (string) $aloc->turno->hora_inicio : null;
            $fim = optional($aloc->turno->hora_fim) ? (string) $aloc->turno->hora_fim : null;
            if (!$inicio || !$fim) continue;

            $diaKey = Carbon::parse($aloc->data)->format('Y-m-d');
            $ocupacaoPorDia[$diaKey][$aloc->plantonista_id][] = [
                'inicio' => $inicio,
                'fim' => $fim,
                'alocacao_id' => $aloc->id,
                'turno_id' => $aloc->turno_id,
                'setor_id' => $aloc->setor_id,
            ];
        }

        return view('escalas-publicadas.edit', compact(
            'escalaPublicada',
            'rows',
            'turnos',
            'setores',
            'plantonistas',
            'ocupacaoPorDia'
        ));
    }

    /**
     * Atualiza uma alocação publicada (atribuir/remover plantonista, status, observações).
     */
    public function updateAlocacao(Request $request, AlocacaoPublicada $alocacaoPublicada)
    {
        $validated = $request->validate([
            'plantonista_id' => 'nullable|exists:plantonistas,id',
            'status' => 'nullable|string',
            'observacoes' => 'nullable|string',
        ]);

        // Regra: impedir sobreposição de horários para o mesmo plantonista no mesmo dia
        // Aplica apenas quando atribuirmos um plantonista (não ao limpar)
        if (!empty($validated['plantonista_id'])) {
            $plantonistaId = (int) $validated['plantonista_id'];
            $dataDia = Carbon::parse($alocacaoPublicada->data);

            // Turno atual (novo)
            $turnoNovo = \App\Models\Turno::find($alocacaoPublicada->turno_id);
            if ($turnoNovo && $turnoNovo->hora_inicio && $turnoNovo->hora_fim) {
                $inicioNovo = Carbon::parse($dataDia->format('Y-m-d') . ' ' . $turnoNovo->hora_inicio);
                $fimNovo = Carbon::parse($dataDia->format('Y-m-d') . ' ' . $turnoNovo->hora_fim);
                // Corujão (atravessa meia-noite)
                if ($fimNovo->lessThanOrEqualTo($inicioNovo)) {
                    $fimNovo->addDay();
                }

                // Buscar outras alocações do mesmo plantonista neste dia (mesma escala), exceto a própria
                $conflitos = AlocacaoPublicada::query()
                    ->where('escala_publicada_id', $alocacaoPublicada->escala_publicada_id)
                    ->where('data', $dataDia->format('Y-m-d'))
                    ->where('plantonista_id', $plantonistaId)
                    ->where('id', '!=', $alocacaoPublicada->id)
                    ->with(['turno', 'setor'])
                    ->get();

                foreach ($conflitos as $existente) {
                    $turnoExistente = $existente->turno;
                    if (!$turnoExistente || !$turnoExistente->hora_inicio || !$turnoExistente->hora_fim) {
                        continue;
                    }

                    $inicioExist = Carbon::parse($dataDia->format('Y-m-d') . ' ' . $turnoExistente->hora_inicio);
                    $fimExist = Carbon::parse($dataDia->format('Y-m-d') . ' ' . $turnoExistente->hora_fim);
                    if ($fimExist->lessThanOrEqualTo($inicioExist)) {
                        $fimExist->addDay();
                    }

                    // Sobreposição: (inicioNovo < fimExist) && (inicioExist < fimNovo)
                    $haConflito = $inicioNovo->lt($fimExist) && $inicioExist->lt($fimNovo);
                    if ($haConflito) {
                        return back()
                            ->withErrors("Conflito de horário: o plantonista já está alocado no turno {$turnoExistente->nome} ({$turnoExistente->hora_inicio}-{$turnoExistente->hora_fim}) no setor " . ($existente->setor->nome ?? 'N/A'))
                            ->withInput();
                    }
                }
            }
        }

        $alocacaoPublicada->update($validated);

        return back()->with('success', 'Alocação atualizada com sucesso.');
    }
}
