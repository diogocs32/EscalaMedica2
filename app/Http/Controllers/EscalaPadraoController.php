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
        $query = Unidade::with(['cidade'])
            ->orderBy('nome');

        // filtros simples (opcionais): status e busca por nome
        if ($request->filled('status') && in_array($request->status, ['ativo', 'inativo'])) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where('nome', 'like', "%$q%");
        }

        $unidades = $query->get();

        // Montar métricas por unidade
        $cards = [];
        foreach ($unidades as $unidade) {
            // Escala padrão ativa
            $escalaAtiva = $unidade->escalaPadrao()->where('status', 'ativo')->first();

            // Total de Slots: soma das quantidades configuradas no template (slots criados na escala padrão)
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

            // Preenchidos: 0 por enquanto (será quando alocarmos plantonistas nas vagas)
            $preenchidos = 0;

            // Buracos: enquanto não tiver médico alocado, buracos = total de slots
            $buracos = $totalSlots - $preenchidos;
            $taxa = $totalSlots > 0 ? round(($preenchidos / $totalSlots) * 100, 1) : 0;

            $cards[] = [
                'unidade' => $unidade,
                'escala' => $escalaAtiva,
                'total_slots' => $totalSlots,
                'preenchidos' => $preenchidos,
                'buracos' => $buracos,
                'taxa' => $taxa,
                'slots_configurados' => $totalSlots, // total de slots criados na escala padrão
                'configs_linhas' => $configCount, // número de linhas de configuração
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
        $escala = $unidade->escalaPadrao()->where('status', 'ativo')
            ->with(['semanas.dias.configuracoes.turno', 'semanas.dias.configuracoes.setor'])
            ->firstOrFail();

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

        $preenchidos = 0; // ainda não há alocações de plantonistas
        $buracos = $totalSlots - $preenchidos; // enquanto não tiver médico alocado, buracos = total de slots
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
        $dias = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];

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
            'semana_destino' => 'required|integer|min:1|max:5',
            'dias_destino' => 'required|array',
            'dias_destino.*' => 'required|in:segunda,terca,quarta,quinta,sexta,sabado,domingo',
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

            $semanaDestino = $escala->semanas()->where('numero_semana', $validated['semana_destino'])->firstOrFail();
            $copiadosCount = 0;

            foreach ($validated['dias_destino'] as $diaDestinoNome) {
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
}
