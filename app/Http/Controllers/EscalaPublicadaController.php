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

        return view('escalas-publicadas.edit', compact(
            'escalaPublicada',
            'rows',
            'turnos',
            'setores',
            'plantonistas'
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

        $alocacaoPublicada->update($validated);

        return back()->with('success', 'Alocação atualizada com sucesso.');
    }
}
