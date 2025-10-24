<?php

namespace App\Http\Controllers;

use App\Models\EscalaPadrao;
use App\Models\Unidade;
use App\Models\SemanaTemplate;
use App\Models\DiaTemplate;
use App\Models\ConfiguracaoTurnoSetor;
use App\Models\Turno;
use App\Models\Setor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EscalaPadraoController extends Controller
{
    /**
     * Resumo geral dos padrões de escala de todas as unidades
     * Total de Slots: soma das quantidades configuradas na Escala Padrão (quantidade_necessaria)
     * Preenchidos: 0 por enquanto (será preenchido quando alocarmos plantonistas)
     * Buracos: Total de Slots - Preenchidos
     */
    public function resumoGeral(Request $request)
    {
        $query = Unidade::with(['cidade'])->orderBy('nome');

        // filtros simples (opcionais): status e busca por nome
        if ($request->filled('status') && in_array($request->status, ['ativo', 'inativo'])) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where('nome', 'like', "%$q%");
        }

        $unidades = $query->get();

        $cards = [];
        foreach ($unidades as $unidade) {
            $escalaAtiva = $unidade->escalaPadrao()->where('status', 'ativo')->first();

            // Total de Slots: soma das quantidades configuradas na escala padrão
            $totalSlots = 0;
            $configCount = 0;
            if ($escalaAtiva) {
                $totalSlots = (int) ConfiguracaoTurnoSetor::whereHas('diaTemplate.semanaTemplate', function ($q) use ($escalaAtiva) {
                    $q->where('escala_padrao_id', $escalaAtiva->id);
                })->sum('quantidade_necessaria');

                $configCount = (int) ConfiguracaoTurnoSetor::whereHas('diaTemplate.semanaTemplate', function ($q) use ($escalaAtiva) {
                    $q->where('escala_padrao_id', $escalaAtiva->id);
                })->count();
            }

            // Preenchidos (cap): soma, por grupo, o mínimo entre esperado e alocado
            $preenchidos = 0;
            if ($escalaAtiva && $totalSlots > 0) {
                $diasMap = ['domingo' => 1, 'segunda' => 2, 'terca' => 3, 'quarta' => 4, 'quinta' => 5, 'sexta' => 6, 'sabado' => 7];
                $semanas = $escalaAtiva->semanas()->with(['dias.configuracoes'])->get();
                foreach ($semanas as $sem) {
                    $s = (int) $sem->numero_semana;
                    foreach ($sem->dias as $dia) {
                        $diaNum = $diasMap[$dia->dia_semana] ?? null;
                        if ($diaNum === null) continue;
                        foreach ($dia->configuracoes as $cfg) {
                            $qtd = (int) $cfg->quantidade_necessaria;
                            $filled = \App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaAtiva->id)
                                ->where('semana', $s)
                                ->where('dia', $diaNum)
                                ->where('turno_id', $cfg->turno_id)
                                ->where('setor_id', $cfg->setor_id)
                                ->whereNotNull('plantonista_id')
                                ->count();
                            $preenchidos += min($qtd, $filled);
                        }
                    }
                }
            }

            $buracos = max(0, $totalSlots - $preenchidos);
            $taxa = $totalSlots > 0 ? round(($preenchidos / $totalSlots) * 100, 1) : 0;

            $cards[] = [
                'unidade' => $unidade,
                'escala' => $escalaAtiva,
                'total_slots' => $totalSlots,
                'preenchidos' => $preenchidos,
                'buracos' => $buracos,
                'taxa' => $taxa,
                'slots_configurados' => $totalSlots,
                'configs_linhas' => $configCount,
                'status' => $escalaAtiva?->status ?? 'inexistente',
            ];
        }

        return view('escalas-padrao.resumo', [
            'cards' => $cards,
            'filtros' => [
                'status' => $request->status,
                'q' => $request->q,
            ],
        ]);
    }

    /**
     * Planilha 5x7 por unidade (Semana 1..5 x Dias) com colunas por Turno > Setor
     * Total de Slots: soma das quantidades configuradas (quantidade_necessaria)
     * Preenchidos: 0 por enquanto (será quando alocarmos plantonistas)
     * Buracos: Total de Slots - Preenchidos
     */
    public function planilha(Unidade $unidade)
    {
        // Força refresh para evitar cache do Eloquent
        $escala = $unidade->escalaPadrao()->where('status', 'ativo')
            ->with(['semanas.dias.configuracoes.turno', 'semanas.dias.configuracoes.setor'])
            ->first();

        if (!$escala) {
            return redirect()->route('escalas-padrao.index', $unidade)
                ->with('warning', 'Esta unidade ainda não possui escala padrão.');
        }

        // Refresh para garantir dados atualizados
        $escala->load(['semanas.dias.configuracoes.turno', 'semanas.dias.configuracoes.setor']);

        // Listar turnos presentes nas configurações e seus setores únicos
        $turnosMapa = []; // turno_id => ['turno'=>$turno, 'setores'=>[setor_id=>Setor]]

        foreach ($escala->semanas as $semana) {
            foreach ($semana->dias as $dia) {
                foreach ($dia->configuracoes as $cfg) {
                    $tId = $cfg->turno->id;
                    $sId = $cfg->setor->id;
                    if (!isset($turnosMapa[$tId])) {
                        $turnosMapa[$tId] = [
                            'turno' => $cfg->turno,
                            'setores' => [],
                        ];
                    }
                    $turnosMapa[$tId]['setores'][$sId] = $cfg->setor; // mantém objeto para nome
                }
            }
        }

        // Ordenar turnos por hora_inicio e setores por nome
        uasort($turnosMapa, function ($a, $b) {
            return strcmp($a['turno']->hora_inicio, $b['turno']->hora_inicio);
        });
        foreach ($turnosMapa as $tId => $info) {
            uasort($turnosMapa[$tId]['setores'], function ($a, $b) {
                return strcmp($a->nome, $b->nome);
            });
        }

        // Construir grid: semana -> dia -> turno_id -> setor_id => quantidade
        $diasOrdem = ['domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];
        $grid = [];
        foreach ($escala->semanas as $semana) {
            $semNumber = (int)$semana->numero_semana;
            $grid[$semNumber] = [];
            // inicializa dias
            foreach ($diasOrdem as $d) {
                $grid[$semNumber][$d] = [];
            }

            foreach ($semana->dias as $dia) {
                $dKey = $dia->dia_semana; // já em snake (segunda, terca, ...)
                foreach ($dia->configuracoes as $cfg) {
                    $grid[$semNumber][$dKey][$cfg->turno->id][$cfg->setor->id] = (int)$cfg->quantidade_necessaria;
                }
            }
        }

        // Métricas do topo: baseadas nos slots configurados na escala padrão
        $totalSlots = (int) ConfiguracaoTurnoSetor::whereHas('diaTemplate.semanaTemplate', function ($q) use ($escala) {
            $q->where('escala_padrao_id', $escala->id);
        })->sum('quantidade_necessaria');

        // Preenchidos (com "cap"): somar, por grupo (semana, dia, turno, setor), o mínimo entre
        // a quantidade esperada e a quantidade realmente alocada no template. Isso evita que
        // "excessos" em um grupo compensem buracos de outro grupo.
        $diasMap = ['domingo' => 1, 'segunda' => 2, 'terca' => 3, 'quarta' => 4, 'quinta' => 5, 'sexta' => 6, 'sabado' => 7];
        $preenchidosCap = 0;
        foreach ($grid as $semNumber => $diasGrid) {
            foreach ($diasGrid as $dKey => $turnosGrid) {
                $diaNum = $diasMap[$dKey] ?? null;
                if ($diaNum === null) continue;
                foreach ($turnosGrid as $tId => $setoresGrid) {
                    foreach ($setoresGrid as $sId => $qtdEsperada) {
                        $filledCount = \App\Models\AlocacaoTemplate::where('escala_padrao_id', $escala->id)
                            ->where('semana', (int) $semNumber)
                            ->where('dia', (int) $diaNum)
                            ->where('turno_id', (int) $tId)
                            ->where('setor_id', (int) $sId)
                            ->whereNotNull('plantonista_id')
                            ->count();
                        $preenchidosCap += min((int) $qtdEsperada, (int) $filledCount);
                    }
                }
            }
        }

        $preenchidos = $preenchidosCap;
        $buracos = max(0, $totalSlots - $preenchidos);
        $taxa = $totalSlots > 0 ? round(($preenchidos / $totalSlots) * 100, 1) : 0;

        return view('escalas-padrao.planilha', [
            'unidade' => $unidade,
            'escala' => $escala,
            'turnosMapa' => $turnosMapa,
            'grid' => $grid,
            'diasOrdem' => $diasOrdem,
            'metricas' => compact('totalSlots', 'preenchidos', 'buracos', 'taxa')
        ]);
    }
    /**
     * Exibe a escala padrão de uma unidade
     */
    public function index(Unidade $unidade)
    {
        $escala = $unidade->escalaPadrao()
            ->where('status', 'ativo')
            ->with(['semanas.dias.configuracoes.turno', 'semanas.dias.configuracoes.setor'])
            ->first();

        if (!$escala) {
            return redirect()->route('escalas-padrao.create', $unidade)
                ->with('info', 'Esta unidade ainda não possui escala padrão. Crie uma agora!');
        }

        $semanaAtual = $escala->getSemanaAtual();
        $dias = ['domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];

        return view('escalas-padrao.index', compact('unidade', 'escala', 'semanaAtual', 'dias'));
    }

    /**
     * Formulário para criar nova escala padrão
     */
    public function create(Unidade $unidade)
    {
        // Verificar se já existe escala ativa
        if ($unidade->escalaPadrao()->where('status', 'ativo')->exists()) {
            return redirect()->route('escalas-padrao.index', $unidade)
                ->with('warning', 'Esta unidade já possui uma escala padrão ativa.');
        }

        return view('escalas-padrao.create', compact('unidade'));
    }

    /**
     * Criar nova escala padrão
     */
    public function store(Request $request, Unidade $unidade)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
            'vigencia_inicio' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            // Criar escala padrão
            $escala = $unidade->escalaPadrao()->create([
                'nome' => $validated['nome'],
                'descricao' => $validated['descricao'],
                'vigencia_inicio' => $validated['vigencia_inicio'],
                'status' => 'ativo',
            ]);

            // Criar estrutura automática: 5 semanas x 7 dias
            $escala->criarEstruturaPadrao();

            DB::commit();

            return redirect()->route('escalas-padrao.index', $unidade)
                ->with('success', 'Escala padrão criada com sucesso! Agora configure os turnos e setores para cada dia.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao criar escala padrão: ' . $e->getMessage()]);
        }
    }

    /**
     * Editar configurações de um dia específico
     */
    public function editDia(Unidade $unidade, $semana, $dia)
    {
        $escala = $unidade->escalaPadrao()->where('status', 'ativo')->firstOrFail();

        $semanaTemplate = $escala->semanas()
            ->where('numero_semana', $semana)
            ->firstOrFail();

        $diaTemplate = $semanaTemplate->dias()
            ->where('dia_semana', $dia)
            ->with(['configuracoes.turno', 'configuracoes.setor'])
            ->firstOrFail();

        // Buscar TODOS os turnos e setores ativos (não filtrar por unidade)
        $turnos = Turno::where('status', 'ativo')->orderBy('hora_inicio')->get();
        $setores = Setor::where('status', 'ativo')->orderBy('nome')->get();

        // Ordenar configurações por hora_inicio do turno
        $diaTemplate->setRelation(
            'configuracoes',
            $diaTemplate->configuracoes->sortBy(function ($config) {
                return $config->turno->hora_inicio;
            })
        );

        // Combos já configurados
        $configsExistentes = $diaTemplate->configuracoes
            ->map(fn($c) => $c->turno_id . '_' . $c->setor_id)
            ->toArray();

        return view('escalas-padrao.edit-dia', compact(
            'unidade',
            'escala',
            'semanaTemplate',
            'diaTemplate',
            'turnos',
            'setores',
            'configsExistentes'
        ));
    }

    /**
     * Adicionar configuração a um dia
     */
    public function storeConfiguracao(Request $request, Unidade $unidade, $semana, $dia)
    {
        $validated = $request->validate([
            'turno_id' => 'required|exists:turnos,id',
            'setor_id' => 'required|exists:setores,id',
            'quantidade_necessaria' => 'required|integer|min:1|max:50',
            'observacoes' => 'nullable|string|max:500',
        ]);

        $escala = $unidade->escalaPadrao()->where('status', 'ativo')->firstOrFail();
        $semanaTemplate = $escala->semanas()->where('numero_semana', $semana)->firstOrFail();
        $diaTemplate = $semanaTemplate->dias()->where('dia_semana', $dia)->firstOrFail();

        // Verificar duplicata
        $existe = $diaTemplate->configuracoes()
            ->where('turno_id', $validated['turno_id'])
            ->where('setor_id', $validated['setor_id'])
            ->exists();

        if ($existe) {
            return back()->withErrors(['error' => 'Esta combinação de Turno + Setor já está configurada para este dia.']);
        }

        try {
            DB::beginTransaction();

            $diaTemplate->configuracoes()->create([
                'turno_id' => $validated['turno_id'],
                'setor_id' => $validated['setor_id'],
                'quantidade_necessaria' => $validated['quantidade_necessaria'],
                'observacoes' => $validated['observacoes'],
                'status' => 'ativo',
            ]);

            DB::commit();

            return back()->with('success', 'Configuração adicionada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao adicionar configuração: ' . $e->getMessage()]);
        }
    }

    /**
     * Remover configuração
     */
    public function destroyConfiguracao(Unidade $unidade, ConfiguracaoTurnoSetor $configuracao)
    {
        try {
            DB::beginTransaction();
            $configuracao->delete();
            DB::commit();

            return back()->with('success', 'Configuração removida com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao remover configuração: ' . $e->getMessage()]);
        }
    }

    /**
     * Copiar configurações de um dia para outro(s)
     */
    public function copiarDia(Request $request, Unidade $unidade, $semana, $dia)
    {
        $validated = $request->validate([
            'dias_destino' => 'required|array',
            'dias_destino.*' => ['required', 'regex:/^\d+_(segunda|terca|quarta|quinta|sexta|sabado|domingo)$/'],
            'sobrescrever' => 'nullable|boolean',
        ]);

        $escala = $unidade->escalaPadrao()->where('status', 'ativo')->firstOrFail();

        $semanaOrigem = $escala->semanas()->where('numero_semana', $semana)->firstOrFail();
        $diaOrigem = $semanaOrigem->dias()->where('dia_semana', $dia)->firstOrFail();
        $configsOrigem = $diaOrigem->configuracoes;

        if ($configsOrigem->isEmpty()) {
            return back()->withErrors(['error' => 'Não há configurações para copiar neste dia.']);
        }

        try {
            DB::beginTransaction();

            $copiadosCount = 0;

            foreach ($validated['dias_destino'] as $destino) {
                // Parsear formato "semana_dia" ex: "1_segunda", "2_terca"
                [$semanaNum, $diaDestinoNome] = explode('_', $destino);

                $semanaDestino = $escala->semanas()->where('numero_semana', $semanaNum)->firstOrFail();
                $diaDestino = $semanaDestino->dias()->where('dia_semana', $diaDestinoNome)->firstOrFail();

                // Se sobrescrever, remover configs existentes
                if ($validated['sobrescrever'] ?? false) {
                    $diaDestino->configuracoes()->delete();
                }

                // Copiar cada configuração
                foreach ($configsOrigem as $config) {
                    // Verificar se já existe
                    $existe = $diaDestino->configuracoes()
                        ->where('turno_id', $config->turno_id)
                        ->where('setor_id', $config->setor_id)
                        ->exists();

                    if (!$existe) {
                        $diaDestino->configuracoes()->create([
                            'turno_id' => $config->turno_id,
                            'setor_id' => $config->setor_id,
                            'quantidade_necessaria' => $config->quantidade_necessaria,
                            'observacoes' => $config->observacoes,
                            'status' => $config->status,
                        ]);
                        $copiadosCount++;
                    }
                }
            }

            DB::commit();

            return back()->with('success', "$copiadosCount configurações copiadas com sucesso!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao copiar configurações: ' . $e->getMessage()]);
        }
    }

    /**
     * Atualizar quantidade de uma configuração
     */
    public function updateConfiguracao(Request $request, Unidade $unidade, ConfiguracaoTurnoSetor $configuracao)
    {
        $validated = $request->validate([
            'quantidade_necessaria' => 'required|integer|min:1|max:50',
            'observacoes' => 'nullable|string|max:500',
            'status' => 'required|in:ativo,inativo',
        ]);

        try {
            DB::beginTransaction();
            $configuracao->update($validated);
            DB::commit();

            return back()->with('success', 'Configuração atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao atualizar: ' . $e->getMessage()]);
        }
    }

    /**
     * API: Obter todas as alocações de uma escala padrão
     */
    public function getAlocacoes(Unidade $unidade)
    {
        $escala = $unidade->escalaPadrao()->where('status', 'ativo')->firstOrFail();

        $alocacoes = \App\Models\AlocacaoTemplate::where('escala_padrao_id', $escala->id)
            ->with(['plantonista', 'turno', 'setor'])
            ->get();

        // Retornar como objeto {key: plantonista_id}
        $resultado = [];
        foreach ($alocacoes as $aloc) {
            $key = "{$aloc->semana}-{$aloc->dia}-{$aloc->turno_id}-{$aloc->setor_id}-{$aloc->slot}";
            $resultado[$key] = [
                'id' => $aloc->id,
                'plantonista_id' => $aloc->plantonista_id,
                'plantonista_nome' => $aloc->plantonista?->nome,
            ];
        }

        return response()->json($resultado);
    }

    /**
     * API: Salvar ou remover uma alocação
     */
    public function storeAlocacao(Request $request, Unidade $unidade)
    {
        try {
            $validated = $request->validate([
                'semana' => 'required|integer|min:1|max:5',
                'dia' => 'required|integer|min:1|max:7',
                'turno_id' => 'required|exists:turnos,id',
                'setor_id' => 'required|exists:setores,id',
                'slot' => 'required|integer|min:1',
                'plantonista_id' => 'nullable|exists:plantonistas,id', // null = remover
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $e->errors(),
            ], 422);
        }

        $escala = $unidade->escalaPadrao()->where('status', 'ativo')->first();

        if (!$escala) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma escala padrão ativa encontrada para esta unidade',
            ], 404);
        }

        try {
            DB::beginTransaction();

            $conditions = [
                'escala_padrao_id' => $escala->id,
                'semana' => $validated['semana'],
                'dia' => $validated['dia'],
                'turno_id' => $validated['turno_id'],
                'setor_id' => $validated['setor_id'],
                'slot' => $validated['slot'],
            ];

            if ($validated['plantonista_id']) {
                // VALIDAÇÃO: Verificar conflito de horário antes de alocar
                $turnoNovo = \App\Models\Turno::find($validated['turno_id']);

                if (!$turnoNovo) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Turno não encontrado',
                    ], 404);
                }

                // Buscar todas as alocações do plantonista na mesma semana e dia
                $alocacoesExistentes = \App\Models\AlocacaoTemplate::where('escala_padrao_id', $escala->id)
                    ->where('semana', $validated['semana'])
                    ->where('dia', $validated['dia'])
                    ->where('plantonista_id', $validated['plantonista_id'])
                    ->with('turno')
                    ->get();

                // Verificar sobreposição de horários
                foreach ($alocacoesExistentes as $alocExistente) {
                    // Ignorar se for o mesmo slot que estamos atualizando
                    if (
                        $alocExistente->turno_id == $validated['turno_id'] &&
                        $alocExistente->setor_id == $validated['setor_id'] &&
                        $alocExistente->slot == $validated['slot']
                    ) {
                        continue;
                    }

                    $turnoExistente = $alocExistente->turno;

                    if (!$turnoExistente || !$turnoExistente->hora_inicio || !$turnoExistente->hora_fim) {
                        continue;
                    }

                    // Verificar sobreposição de horários
                    $inicioNovo = strtotime($turnoNovo->hora_inicio);
                    $fimNovo = strtotime($turnoNovo->hora_fim);
                    $inicioExistente = strtotime($turnoExistente->hora_inicio);
                    $fimExistente = strtotime($turnoExistente->hora_fim);

                    // Ajustar para turnos que cruzam meia-noite (Corujão)
                    if ($fimNovo < $inicioNovo) {
                        $fimNovo += 86400; // +24 horas
                    }
                    if ($fimExistente < $inicioExistente) {
                        $fimExistente += 86400; // +24 horas
                    }

                    // Lógica de sobreposição: (início1 < fim2) E (início2 < fim1)
                    $haConflito = ($inicioNovo < $fimExistente) && ($inicioExistente < $fimNovo);

                    if ($haConflito) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Conflito de horário: o plantonista já está alocado no turno {$turnoExistente->nome} ({$turnoExistente->hora_inicio}-{$turnoExistente->hora_fim}) no setor {$alocExistente->setor->nome}",
                        ], 422);
                    }
                }

                // Alocar ou atualizar
                \App\Models\AlocacaoTemplate::updateOrCreate(
                    $conditions,
                    ['plantonista_id' => $validated['plantonista_id']]
                );
                $action = 'alocado';
            } else {
                // Remover
                \App\Models\AlocacaoTemplate::where($conditions)->delete();
                $action = 'removido';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Plantonista {$action} com sucesso!",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Clonar configurações (Turno+Setor+Quantidade) de um dia de referência
     * para múltiplos dias destino (possivelmente em semanas diferentes).
     *
     * Payload esperado (JSON):
     * {
     *   "referencia": { "semana": 1-5, "dia": "segunda|terca|...|domingo" },
     *   "destinos": [ { "semana": 1-5, "dia": "segunda|...|domingo" }, ... ],
     *   "sobrescrever": true|false (opcional, default=false)
     * }
     */
    public function clonarDiaLote(Request $request, Unidade $unidade)
    {
        try {
            $validated = $request->validate([
                'referencia' => 'required|array',
                'referencia.semana' => 'required|integer|min:1|max:5',
                'referencia.dia' => 'required|in:segunda,terca,quarta,quinta,sexta,sabado,domingo',
                'destinos' => 'required|array|min:1',
                'destinos.*.semana' => 'required|integer|min:1|max:5',
                'destinos.*.dia' => 'required|in:segunda,terca,quarta,quinta,sexta,sabado,domingo',
                'sobrescrever' => 'nullable|boolean',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $e->errors(),
            ], 422);
        }

        $escala = $unidade->escalaPadrao()->where('status', 'ativo')->first();

        if (!$escala) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma escala padrão ativa encontrada para esta unidade',
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Origem
            $semanaOrigem = $escala->semanas()->where('numero_semana', $validated['referencia']['semana'])->firstOrFail();
            $diaOrigem = $semanaOrigem->dias()->where('dia_semana', $validated['referencia']['dia'])->firstOrFail();
            $configsOrigem = $diaOrigem->configuracoes;

            if ($configsOrigem->isEmpty()) {
                DB::rollBack();

                // Mapear nomes dos dias para exibir mensagem amigável
                $diasNomes = [
                    'segunda' => 'Segunda-feira',
                    'terca' => 'Terça-feira',
                    'quarta' => 'Quarta-feira',
                    'quinta' => 'Quinta-feira',
                    'sexta' => 'Sexta-feira',
                    'sabado' => 'Sábado',
                    'domingo' => 'Domingo'
                ];
                $diaLabel = $diasNomes[$validated['referencia']['dia']] ?? $validated['referencia']['dia'];

                return response()->json([
                    'success' => false,
                    'message' => "O dia de referência (Semana {$validated['referencia']['semana']} - {$diaLabel}) não possui configurações de Turno+Setor para clonar. Configure primeiro esse dia antes de clonar.",
                ], 422);
            }

            $sobrescrever = (bool)($validated['sobrescrever'] ?? false);
            $alocacoesCopiadas = 0;
            $alocacoesApagadas = 0;

            // Buscar alocações do dia de referência
            $diaNumRef = $this->mapearDiaParaNumero($validated['referencia']['dia']);
            $alocacoesOrigem = \App\Models\AlocacaoTemplate::where('escala_padrao_id', $escala->id)
                ->where('semana', $validated['referencia']['semana'])
                ->where('dia', $diaNumRef)
                ->get();

            if ($alocacoesOrigem->isEmpty()) {
                DB::rollBack();

                $diasNomes = [
                    'segunda' => 'Segunda-feira',
                    'terca' => 'Terça-feira',
                    'quarta' => 'Quarta-feira',
                    'quinta' => 'Quinta-feira',
                    'sexta' => 'Sexta-feira',
                    'sabado' => 'Sábado',
                    'domingo' => 'Domingo'
                ];
                $diaLabel = $diasNomes[$validated['referencia']['dia']] ?? $validated['referencia']['dia'];

                return response()->json([
                    'success' => false,
                    'message' => "O dia de referência (Semana {$validated['referencia']['semana']} - {$diaLabel}) não possui plantonistas alocados para clonar. Aloque plantonistas primeiro nesse dia.",
                ], 422);
            }

            foreach ($validated['destinos'] as $dest) {
                // Ignorar destino igual à referência
                if ((int)$dest['semana'] === (int)$validated['referencia']['semana'] && $dest['dia'] === $validated['referencia']['dia']) {
                    continue;
                }

                $diaNumDest = $this->mapearDiaParaNumero($dest['dia']);

                if ($sobrescrever) {
                    // Apagar alocações do dia destino
                    $apagados = \App\Models\AlocacaoTemplate::where('escala_padrao_id', $escala->id)
                        ->where('semana', $dest['semana'])
                        ->where('dia', $diaNumDest)
                        ->delete();
                    $alocacoesApagadas += $apagados;
                }

                // Copiar alocações de plantonistas
                foreach ($alocacoesOrigem as $aloc) {
                    // Verificar se já existe alocação no mesmo slot
                    $existeAloc = \App\Models\AlocacaoTemplate::where('escala_padrao_id', $escala->id)
                        ->where('semana', $dest['semana'])
                        ->where('dia', $diaNumDest)
                        ->where('turno_id', $aloc->turno_id)
                        ->where('setor_id', $aloc->setor_id)
                        ->where('slot', $aloc->slot)
                        ->exists();

                    if (!$existeAloc) {
                        \App\Models\AlocacaoTemplate::create([
                            'escala_padrao_id' => $escala->id,
                            'semana' => $dest['semana'],
                            'dia' => $diaNumDest,
                            'turno_id' => $aloc->turno_id,
                            'setor_id' => $aloc->setor_id,
                            'slot' => $aloc->slot,
                            'plantonista_id' => $aloc->plantonista_id,
                        ]);
                        $alocacoesCopiadas++;
                    }
                }
            }
            DB::commit();

            $mensagem = "Clonagem concluída: {$alocacoesCopiadas} plantonista" . ($alocacoesCopiadas != 1 ? 's' : '') . " copiado" . ($alocacoesCopiadas != 1 ? 's' : '');
            if ($sobrescrever && $alocacoesApagadas > 0) {
                $mensagem .= " ({$alocacoesApagadas} alocações removidas antes)";
            }

            return response()->json([
                'success' => true,
                'message' => $mensagem,
                'alocacoesCopiadas' => $alocacoesCopiadas,
                'alocacoesApagadas' => $alocacoesApagadas,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao clonar: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clonar SEMANA inteira em lote (todas as alocações de uma semana para outras semanas)
     * POST /api/escalas-padrao/{unidade}/clonar-semana
     * Payload: {referencia_semana: int, destinos: [int, ...], sobrescrever: bool}
     */
    public function clonarSemanaLote(Request $request, Unidade $unidade)
    {
        $request->validate([
            'referencia_semana' => 'required|integer|min:1|max:5',
            'destinos' => 'required|array|min:1',
            'destinos.*' => 'required|integer|min:1|max:5',
            'sobrescrever' => 'boolean',
        ]);

        $semanaRef = $request->referencia_semana;
        $destinos = $request->destinos;
        $sobrescrever = $request->sobrescrever ?? false;

        // Obter escala ativa
        $escala = $unidade->escalaPadrao()->where('status', 'ativo')->first();
        if (!$escala) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma escala padrão ativa encontrada para esta unidade.',
            ], 404);
        }

        // Buscar todas as alocações da semana de referência
        $alocacoesOrigem = \App\Models\AlocacaoTemplate::where('escala_padrao_id', $escala->id)
            ->where('semana', $semanaRef)
            ->get();

        if ($alocacoesOrigem->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => "A semana {$semanaRef} não possui plantonistas alocados. Aloque plantonistas primeiro.",
            ], 422);
        }

        try {
            DB::beginTransaction();

            $alocacoesCopiadas = 0;
            $alocacoesApagadas = 0;

            // Para cada semana de destino
            foreach ($destinos as $semanaDestino) {
                // Não clonar para si mesmo
                if ($semanaDestino == $semanaRef) {
                    continue;
                }

                // Se sobrescrever, apagar alocações existentes na semana de destino
                if ($sobrescrever) {
                    $deleted = \App\Models\AlocacaoTemplate::where('escala_padrao_id', $escala->id)
                        ->where('semana', $semanaDestino)
                        ->delete();
                    $alocacoesApagadas += $deleted;
                }

                // Copiar cada alocação da semana de referência para a semana de destino
                foreach ($alocacoesOrigem as $aloc) {
                    // Verificar se já existe (para evitar duplicados se não estiver sobrescrevendo)
                    $existeAloc = \App\Models\AlocacaoTemplate::where('escala_padrao_id', $escala->id)
                        ->where('semana', $semanaDestino)
                        ->where('dia', $aloc->dia)
                        ->where('turno_id', $aloc->turno_id)
                        ->where('setor_id', $aloc->setor_id)
                        ->where('slot', $aloc->slot)
                        ->exists();

                    if (!$existeAloc) {
                        \App\Models\AlocacaoTemplate::create([
                            'escala_padrao_id' => $escala->id,
                            'semana' => $semanaDestino,
                            'dia' => $aloc->dia,
                            'turno_id' => $aloc->turno_id,
                            'setor_id' => $aloc->setor_id,
                            'slot' => $aloc->slot,
                            'plantonista_id' => $aloc->plantonista_id,
                        ]);
                        $alocacoesCopiadas++;
                    }
                }
            }
            DB::commit();

            $mensagem = "Clonagem de semana concluída: {$alocacoesCopiadas} plantonista" . ($alocacoesCopiadas != 1 ? 's' : '') . " copiado" . ($alocacoesCopiadas != 1 ? 's' : '');
            if ($sobrescrever && $alocacoesApagadas > 0) {
                $mensagem .= " ({$alocacoesApagadas} alocações removidas antes)";
            }

            return response()->json([
                'success' => true,
                'message' => $mensagem,
                'alocacoesCopiadas' => $alocacoesCopiadas,
                'alocacoesApagadas' => $alocacoesApagadas,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao clonar semana: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Publicar escala: gera escala mensal com base no padrão de 5 semanas
     * Mapeia cada semana do mês para uma das 5 semanas do ciclo
     */
    public function publicar(Request $request, Unidade $unidade)
    {
        $request->validate([
            'periodo' => 'required|date_format:Y-m',
            'substituir' => 'sometimes|boolean',
        ]);

        [$ano, $mes] = explode('-', $request->periodo);

        // Buscar escala padrão ativa
        $escalaPadrao = $unidade->escalaPadrao()->where('status', 'ativo')->first();

        if (!$escalaPadrao) {
            return redirect()->back()->with('error', 'Esta unidade não possui escala padrão ativa.');
        }

        // Verificar se já existe publicação para este período
        $publicacaoExistente = \App\Models\EscalaPublicada::where('unidade_id', $unidade->id)
            ->where('ano', $ano)
            ->where('mes', $mes)
            ->first();

        if ($publicacaoExistente && !$request->input('substituir', false)) {
            return redirect()->back()
                ->with('warning', "Já existe uma escala publicada para {$mes}/{$ano}. Deseja substituí-la?")
                ->with('confirm_substituir', true)
                ->with('periodo', $request->periodo)
                ->with('unidade_id', $unidade->id);
        }

        try {
            DB::beginTransaction();

            // Se existe e usuário confirmou substituição, deletar a antiga
            if ($publicacaoExistente) {
                // Deletar alocações antigas (cascade)
                $publicacaoExistente->alocacoes()->delete();
                $publicacaoExistente->delete();
            }

            // Criar registro de escala publicada
            $escalaPublicada = \App\Models\EscalaPublicada::create([
                'unidade_id' => $unidade->id,
                'escala_padrao_id' => $escalaPadrao->id,
                'ano' => $ano,
                'mes' => $mes,
                'status' => 'em_edicao',
            ]);

            // Processar cada dia do mês (1 a 31)
            $diasNoMes = cal_days_in_month(CAL_GREGORIAN, (int)$mes, (int)$ano);
            $vigenciaInicio = \Carbon\Carbon::parse($escalaPadrao->vigencia_inicio)->startOfDay();

            for ($dia = 1; $dia <= $diasNoMes; $dia++) {
                $dataAtual = \Carbon\Carbon::create($ano, $mes, $dia)->startOfDay();

                // Calcular semana do mês: blocos de 7 dias (1-7, 8-14, 15-21, 22-28, 29-31)
                $semanaMes = (int) ceil($dia / 7);

                // Calcular qual semana do ciclo de 5 semanas (1-5)
                $numeroDaSemana = (($semanaMes - 1) % 5) + 1;

                // Nome do dia da semana (segunda, terca, etc)
                $nomeDiaSemana = $this->getNomeDiaSemana($dataAtual->dayOfWeek);

                // Número do dia da semana (1=domingo, 2=segunda, ..., 7=sábado)
                $numeroDiaSemana = $dataAtual->dayOfWeek + 1; // Carbon: 0=domingo, 6=sábado -> Converter para 1-7

                // Buscar configurações do dia correspondente no template
                $diaTemplate = DiaTemplate::whereHas('semanaTemplate', function ($q) use ($escalaPadrao, $numeroDaSemana) {
                    $q->where('escala_padrao_id', $escalaPadrao->id)
                        ->where('numero_semana', $numeroDaSemana);
                })
                    ->where('dia_semana', $nomeDiaSemana)
                    ->with('configuracoes.turno', 'configuracoes.setor')
                    ->first();

                if (!$diaTemplate) continue;

                // Buscar alocações template (médicos já preenchidos no padrão)
                // IMPORTANTE: Campo 'dia' na tabela alocacoes_template é INTEGER (1-7)
                $alocacoesTemplate = \App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)
                    ->where('semana', $numeroDaSemana)
                    ->where('dia', $numeroDiaSemana)
                    ->get()
                    ->keyBy(function ($item) {
                        // Criar chave única: turno-setor-slot
                        return "{$item->turno_id}-{$item->setor_id}-{$item->slot}";
                    });

                // SEMPRE processar baseado em ConfiguracaoTurnoSetor (define QUANTOS slots devem existir)
                foreach ($diaTemplate->configuracoes as $config) {
                    // Para cada slot necessário neste turno+setor
                    for ($slot = 1; $slot <= $config->quantidade_necessaria; $slot++) {
                        // Buscar se existe alocação template para este slot específico
                        $chave = "{$config->turno_id}-{$config->setor_id}-{$slot}";
                        $alocTemplate = $alocacoesTemplate->get($chave);

                        // Criar alocação publicada
                        \App\Models\AlocacaoPublicada::create([
                            'escala_publicada_id' => $escalaPublicada->id,
                            'data' => $dataAtual->format('Y-m-d'),
                            'turno_id' => $config->turno_id,
                            'setor_id' => $config->setor_id,
                            'plantonista_id' => $alocTemplate ? $alocTemplate->plantonista_id : null,
                            'status' => ($alocTemplate && $alocTemplate->plantonista_id) ? 'preenchido' : 'vago',
                            'observacoes' => $config->observacoes,
                        ]);
                    }
                }
            }

            DB::commit();

            $acao = $publicacaoExistente ? 'substituída' : 'publicada';
            return redirect()
                ->route('alocacoes.index')
                ->with('success', "Escala {$acao} com sucesso para {$mes}/{$ano}!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro ao publicar escala: ' . $e->getMessage());
        }
    }

    /**
     * Mapear nome do dia para número (domingo=1, segunda=2, ..., sabado=7)
     */
    private function mapearDiaParaNumero($dia)
    {
        $mapa = [
            'domingo' => 1,
            'segunda' => 2,
            'terca' => 3,
            'quarta' => 4,
            'quinta' => 5,
            'sexta' => 6,
            'sabado' => 7,
        ];
        return $mapa[$dia] ?? 1;
    }

    /**
     * Obter nome do dia da semana (0=domingo, 6=sábado)
     */
    private function getNomeDiaSemana($dayOfWeek)
    {
        $dias = [
            0 => 'domingo',
            1 => 'segunda',
            2 => 'terca',
            3 => 'quarta',
            4 => 'quinta',
            5 => 'sexta',
            6 => 'sabado',
        ];
        return $dias[$dayOfWeek] ?? 'segunda';
    }
}
