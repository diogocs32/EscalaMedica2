<?php

namespace App\Http\Controllers;

use App\Models\EscalaPublicada;
use App\Models\AlocacaoPublicada;
use App\Models\Plantonista;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        // E também identificar quais combinações turno×setor possuem ao menos 1 slot no mês
        $mapaAlocacoes = [];
        $combAtivas = [];
        foreach ($escalaPublicada->alocacoes as $aloc) {
            if (!$aloc->turno_id || !$aloc->setor_id) continue;
            $key = Carbon::parse($aloc->data)->format('Y-m-d');
            $mapaAlocacoes[$key][$aloc->turno_id][$aloc->setor_id][] = $aloc;
            $combAtivas[$aloc->turno_id][$aloc->setor_id] = true;
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

        // Construir lista de setores ativos por turno e turnos ativos (para esconder colunas vazias no mês)
        $setoresAtivosPorTurno = [];
        foreach ($turnos as $t) {
            foreach ($setores as $s) {
                if (!empty($combAtivas[$t->id][$s->id])) {
                    $setoresAtivosPorTurno[$t->id][] = $s;
                }
            }
        }
        $turnosAtivos = $turnos->filter(fn($t) => !empty($setoresAtivosPorTurno[$t->id] ?? []))->values();

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
            'ocupacaoPorDia',
            'setoresAtivosPorTurno',
            'turnosAtivos'
        ));
    }

    /**
     * Tela de edição rápida com dropdown de plantonistas e validação de conflitos.
     */
    public function editRapido(EscalaPublicada $escalaPublicada)
    {
        // Usar os mesmos dados do método edit()
        $escalaPublicada->load(['unidade.cidade', 'escalaPadrao', 'alocacoes.turno', 'alocacoes.setor', 'alocacoes.plantonista']);

        $ano = (int) $escalaPublicada->ano;
        $mes = (int) $escalaPublicada->mes;
        $inicioMes = Carbon::create($ano, $mes, 1);
        $fimMes = (clone $inicioMes)->endOfMonth();
        $daysInMonth = $fimMes->day;

        $turnos = \App\Models\Turno::orderBy('hora_inicio')->get();
        $setores = \App\Models\Setor::orderBy('id')->get();

        $mapaAlocacoes = [];
        $combAtivas = [];
        foreach ($escalaPublicada->alocacoes as $aloc) {
            if (!$aloc->turno_id || !$aloc->setor_id) continue;
            $key = Carbon::parse($aloc->data)->format('Y-m-d');
            $mapaAlocacoes[$key][$aloc->turno_id][$aloc->setor_id][] = $aloc;
            $combAtivas[$aloc->turno_id][$aloc->setor_id] = true;
        }

        $rows = [];
        for ($dia = 1; $dia <= $daysInMonth; $dia++) {
            $data = Carbon::create($ano, $mes, $dia);
            $key = $data->format('Y-m-d');
            $semana = (int) ceil($dia / 7);

            $row = [
                'dia' => $dia,
                'semana' => $semana,
                'data' => $data,
                'weekday' => $data->locale('pt_BR')->isoFormat('dddd'),
                'slots' => []
            ];

            foreach ($turnos as $turno) {
                foreach ($setores as $setor) {
                    $row['slots'][$turno->id][$setor->id] = $mapaAlocacoes[$key][$turno->id][$setor->id] ?? [];
                }
            }

            $rows[] = $row;
        }

        $plantonistas = Plantonista::query()
            ->when(method_exists(Plantonista::class, 'scopeAtivo'), fn($q) => $q->ativo())
            ->orderBy('nome')
            ->get();

        $setoresAtivosPorTurno = [];
        foreach ($turnos as $t) {
            foreach ($setores as $s) {
                if (!empty($combAtivas[$t->id][$s->id])) {
                    $setoresAtivosPorTurno[$t->id][] = $s;
                }
            }
        }
        $turnosAtivos = $turnos->filter(fn($t) => !empty($setoresAtivosPorTurno[$t->id] ?? []))->values();

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

        return view('escalas-publicadas.edit-rapido', compact(
            'escalaPublicada',
            'rows',
            'turnos',
            'setores',
            'plantonistas',
            'ocupacaoPorDia',
            'setoresAtivosPorTurno',
            'turnosAtivos'
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
                // Normalizar para apenas hora (HH:MM:SS), evitando "dupla data"
                $horaInicioNovo = Carbon::parse($turnoNovo->hora_inicio)->format('H:i:s');
                $horaFimNovo = Carbon::parse($turnoNovo->hora_fim)->format('H:i:s');
                $inicioNovo = Carbon::parse($dataDia->format('Y-m-d') . ' ' . $horaInicioNovo);
                $fimNovo = Carbon::parse($dataDia->format('Y-m-d') . ' ' . $horaFimNovo);
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

                    // Normalizar horas existentes
                    $horaInicioExist = Carbon::parse($turnoExistente->hora_inicio)->format('H:i:s');
                    $horaFimExist = Carbon::parse($turnoExistente->hora_fim)->format('H:i:s');
                    $inicioExist = Carbon::parse($dataDia->format('Y-m-d') . ' ' . $horaInicioExist);
                    $fimExist = Carbon::parse($dataDia->format('Y-m-d') . ' ' . $horaFimExist);
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

    /**
     * Página de calendário (consulta) para visualizar todas as alocações publicadas.
     */
    public function calendar(Request $request)
    {
        // Poderemos futuramente passar filtros (unidade, plantonista). Por ora, somente renderiza a view.
        $unidades = \App\Models\Unidade::orderBy('nome')->get(['id', 'nome']);
        $plantonistas = \App\Models\Plantonista::orderBy('nome')->get(['id', 'nome']);

        return view('escalas-publicadas.calendar', compact('unidades', 'plantonistas'));
    }

    /**
     * Endpoint JSON para FullCalendar: retorna eventos entre start e end.
     */
    public function events(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        if (!$start || !$end) {
            return response()->json([]);
        }

        $inicio = Carbon::parse($start)->startOfDay();
        $fim = Carbon::parse($end)->endOfDay();

        // Filtros opcionais
        $unidadeId = $request->query('unidade_id');
        $plantonistaId = $request->query('plantonista_id');

        $query = AlocacaoPublicada::query()
            ->whereBetween('data', [$inicio->toDateString(), $fim->toDateString()])
            ->with(['escalaPublicada.unidade.cidade', 'turno', 'setor', 'plantonista']);

        if ($unidadeId) {
            $query->whereHas('escalaPublicada', function ($q) use ($unidadeId) {
                $q->where('unidade_id', $unidadeId);
            });
        }
        if ($plantonistaId) {
            $query->where('plantonista_id', $plantonistaId);
        }

        $alocacoes = $query->get();

        $events = $alocacoes->map(function ($a) {
            $turno = $a->turno;
            $setor = $a->setor;
            $unidade = optional($a->escalaPublicada)->unidade;
            $cidade = optional($unidade)->cidade;
            $plantonista = $a->plantonista;

            // Normalizar horários
            $dataBase = Carbon::parse($a->data)->format('Y-m-d');
            $hInicio = $turno && $turno->hora_inicio ? Carbon::parse($turno->hora_inicio)->format('H:i:s') : '00:00:00';
            $hFim = $turno && $turno->hora_fim ? Carbon::parse($turno->hora_fim)->format('H:i:s') : '23:59:59';
            $inicio = Carbon::parse($dataBase . ' ' . $hInicio);
            $fim = Carbon::parse($dataBase . ' ' . $hFim);
            if ($fim->lessThanOrEqualTo($inicio)) {
                $fim->addDay(); // corujão
            }

            // Título amigável
            $titleParts = [];
            $titleParts[] = $plantonista?->nome ?? 'Vago';
            if ($setor?->nome) $titleParts[] = $setor->nome;
            if ($turno?->nome) $titleParts[] = $turno->nome;
            $title = implode(' • ', $titleParts);

            // Cor por período do turno (opcional)
            $periodo = strtolower((string)($turno->periodo ?? ''));
            $bg = match ($periodo) {
                'manhã', 'manha' => '#4dabf7',
                'tarde' => '#51cf66',
                'noite' => '#845ef7',
                default => '#868e96',
            };

            return [
                'id' => $a->id,
                'title' => $title,
                // Enviar horários sem timezone para evitar deslocamento no navegador
                'start' => $inicio->format('Y-m-d\TH:i:s'),
                'end' => $fim->format('Y-m-d\TH:i:s'),
                'allDay' => false,
                'backgroundColor' => $bg,
                'borderColor' => $bg,
                'textColor' => '#fff',
                'extendedProps' => [
                    'unidade' => $unidade?->nome,
                    'cidade' => $cidade?->nome,
                    'estado' => $cidade?->estado,
                    'setor' => $setor?->nome,
                    'turno' => $turno?->nome,
                    'plantonista_id' => $plantonista?->id,
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Adiciona um novo slot vazio em uma célula específica da escala
     * 
     * @param Request $request
     * @param EscalaPublicada $escalaPublicada
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addSlot(Request $request, EscalaPublicada $escalaPublicada)
    {
        // Validação
        $validated = $request->validate([
            'semana' => 'required|integer|min:1|max:5',
            'dia' => 'required|integer|min:1|max:31',
            'turno_id' => 'required|exists:turnos,id',
            'setor_id' => 'required|exists:setores,id',
        ]);

        DB::beginTransaction();

        try {
            // Encontrar o próximo número de slot disponível
            $maxSlot = \App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPublicada->escala_padrao_id)
                ->where('semana', $validated['semana'])
                ->where('dia', $validated['dia'])
                ->where('turno_id', $validated['turno_id'])
                ->where('setor_id', $validated['setor_id'])
                ->max('slot');

            $nextSlot = ($maxSlot ?? 0) + 1;

            // Criar novo slot vazio na tabela template (padrão)
            \App\Models\AlocacaoTemplate::create([
                'escala_padrao_id' => $escalaPublicada->escala_padrao_id,
                'semana' => $validated['semana'],
                'dia' => $validated['dia'],
                'turno_id' => $validated['turno_id'],
                'setor_id' => $validated['setor_id'],
                'slot' => $nextSlot,
                'plantonista_id' => null, // Vago
            ]);

            // Criar novo slot vago na escala publicada (mês atual)
            $data = \Carbon\Carbon::create(
                $escalaPublicada->ano,
                $escalaPublicada->mes,
                $validated['dia']
            );

            \App\Models\AlocacaoPublicada::create([
                'escala_publicada_id' => $escalaPublicada->id,
                'turno_id' => $validated['turno_id'],
                'setor_id' => $validated['setor_id'],
                'data' => $data->format('Y-m-d'),
                'plantonista_id' => null, // Vago
            ]);

            // Atualizar métricas da escala publicada
            $escalaPublicada->refresh();
            $escalaPublicada->recalcularMetricas();

            DB::commit();

            return redirect()
                ->route('escalas-publicadas.edit', $escalaPublicada)
                ->with('success', 'Nova vaga adicionada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Erro ao adicionar vaga: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove todos os slots vazios de uma célula específica
     * 
     * @param Request $request
     * @param EscalaPublicada $escalaPublicada
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeEmptySlots(Request $request, EscalaPublicada $escalaPublicada)
    {
        // Validação
        $validated = $request->validate([
            'semana' => 'required|integer|min:1|max:5',
            'dia' => 'required|integer|min:1|max:31',
            'turno_id' => 'required|exists:turnos,id',
            'setor_id' => 'required|exists:setores,id',
        ]);

        DB::beginTransaction();

        try {
            // Excluir slots vazios da tabela template (padrão)
            $deletedTemplate = \App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPublicada->escala_padrao_id)
                ->where('semana', $validated['semana'])
                ->where('dia', $validated['dia'])
                ->where('turno_id', $validated['turno_id'])
                ->where('setor_id', $validated['setor_id'])
                ->whereNull('plantonista_id')
                ->delete();

            // Excluir slots vazios da escala publicada (mês atual)
            $data = \Carbon\Carbon::create(
                $escalaPublicada->ano,
                $escalaPublicada->mes,
                $validated['dia']
            );

            $deletedPublicada = \App\Models\AlocacaoPublicada::where('escala_publicada_id', $escalaPublicada->id)
                ->where('turno_id', $validated['turno_id'])
                ->where('setor_id', $validated['setor_id'])
                ->where('data', $data->format('Y-m-d'))
                ->whereNull('plantonista_id')
                ->delete();

            // Atualizar métricas da escala publicada
            $escalaPublicada->refresh();
            $escalaPublicada->recalcularMetricas();

            DB::commit();

            $message = $deletedTemplate > 0
                ? "Removidos $deletedTemplate slot(s) vazio(s) com sucesso!"
                : "Nenhum slot vazio encontrado para remover.";

            return redirect()
                ->route('escalas-publicadas.edit', $escalaPublicada)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Erro ao remover slots vazios: ' . $e->getMessage()]);
        }
    }

    /**
     * Exclui uma escala publicada e suas alocações (cascade pelo banco).
     */
    public function destroy(EscalaPublicada $escalaPublicada)
    {
        try {
            $resumo = sprintf(
                '%s/%s - %s',
                $escalaPublicada->mes,
                $escalaPublicada->ano,
                optional($escalaPublicada->unidade)->nome ?? 'Unidade'
            );

            $escalaPublicada->delete();

            return redirect()
                ->route('alocacoes.index')
                ->with('success', "Escala publicada ({$resumo}) excluída com sucesso.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Erro ao excluir escala publicada: ' . $e->getMessage());
        }
    }

    /**
     * API: Buscar escala publicada por ano e mês
     */
    public function buscarPorMes(Request $request)
    {
        $ano = $request->input('ano');
        $mes = $request->input('mes');

        if (!$ano || !$mes) {
            return response()->json(['error' => 'Parâmetros ano e mes são obrigatórios'], 400);
        }

        $escala = EscalaPublicada::where('ano', $ano)
            ->where('mes', $mes)
            ->first();

        if (!$escala) {
            return response()->json(['error' => 'Escala não encontrada'], 404);
        }

        return response()->json([
            'id' => $escala->id,
            'ano' => $escala->ano,
            'mes' => $escala->mes,
            'unidade_id' => $escala->unidade_id,
        ]);
    }
}
