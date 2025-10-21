<?php

namespace App\Http\Controllers;

use App\Models\Vaga;
use App\Models\Unidade;
use App\Models\Setor;
use App\Models\Turno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VagaController extends Controller
{
    /**
     * Exibe todas as vagas de uma unidade específica
     */
    public function index(Unidade $unidade, Request $request)
    {
        $dias = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
        $diaSelecionado = $request->get('dia', 'segunda');
        $unidade->load(['cidade', 'vagas.setor', 'vagas.turno']);
        $vagasDia = $unidade->vagas->where('dia_semana', $diaSelecionado);
        return view('vagas.index', compact('unidade', 'dias', 'diaSelecionado', 'vagasDia'));
    }


    /**
     * Formulário para criar nova vaga (configuração turno/setor/quantidade)
     */
    public function create(Unidade $unidade, Request $request)
    {
        // Buscar apenas setores já associados a esta unidade (que já têm vagas cadastradas)
        $setoresIds = $unidade->vagas()->distinct()->pluck('setor_id');
        $setores = Setor::whereIn('id', $setoresIds)
            ->where('status', 'ativo')
            ->orderBy('nome')
            ->get();

        // Usar turnos selecionados via pivot para esta unidade; se nenhum, mostrar todos ativos
        $turnos = $unidade->turnos()->exists()
            ? $unidade->turnos()->orderBy('hora_inicio')->get()
            : Turno::where('status', 'ativo')->orderBy('hora_inicio')->get();
        $dias = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
        $diaSelecionado = $request->get('dia', 'segunda');
        // Buscar combinações já existentes para evitar duplicatas
        $vagasExistentes = $unidade->vagas()
            ->where('dia_semana', $diaSelecionado)
            ->get()
            ->map(function ($vaga) {
                return $vaga->setor_id . '_' . $vaga->turno_id;
            })
            ->toArray();
        return view('vagas.create', compact('unidade', 'setores', 'turnos', 'dias', 'diaSelecionado', 'vagasExistentes'));
    }

    /**
     * Armazena nova vaga
     */
    public function store(Request $request, Unidade $unidade)
    {
        $validated = $request->validate([
            'setor_id' => 'required|exists:setores,id',
            'turno_id' => 'required|exists:turnos,id',
            'dia_semana' => 'required|in:segunda,terca,quarta,quinta,sexta,sabado,domingo',
            'quantidade_necessaria' => 'required|integer|min:1|max:50',
            'observacoes' => 'nullable|string|max:500',
        ], [
            'setor_id.required' => 'Selecione um setor.',
            'setor_id.exists' => 'Setor inválido.',
            'turno_id.required' => 'Selecione um turno.',
            'turno_id.exists' => 'Turno inválido.',
            'quantidade_necessaria.required' => 'Informe a quantidade de médicos necessária.',
            'quantidade_necessaria.integer' => 'A quantidade deve ser um número inteiro.',
            'quantidade_necessaria.min' => 'A quantidade mínima é 1 médico.',
            'quantidade_necessaria.max' => 'A quantidade máxima é 50 médicos.',
        ]);

        // Verificar se já existe essa combinação (unique key)
        $existe = Vaga::where('unidade_id', $unidade->id)
            ->where('setor_id', $validated['setor_id'])
            ->where('turno_id', $validated['turno_id'])
            ->where('dia_semana', $validated['dia_semana'])
            ->exists();

        if ($existe) {
            return back()
                ->withInput()
                ->withErrors(['setor_id' => 'Esta combinação de Setor + Turno já está configurada para esta unidade.']);
        }

        try {
            DB::beginTransaction();
            $vaga = $unidade->vagas()->create([
                'setor_id' => $validated['setor_id'],
                'turno_id' => $validated['turno_id'],
                'dia_semana' => $validated['dia_semana'],
                'quantidade_necessaria' => $validated['quantidade_necessaria'],
                'observacoes' => $validated['observacoes'],
                'status' => 'ativo',
            ]);
            DB::commit();
            return redirect()
                ->route('vagas.index', ['unidade' => $unidade->id, 'dia' => $validated['dia_semana']])
                ->with('success', 'Configuração de vaga criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao criar configuração: ' . $e->getMessage()]);
        }
    }

    /**
     * Clona todas as vagas de um dia para outro
     */
    public function cloneDay(Request $request, Unidade $unidade)
    {
        $origem = $request->input('origem');
        $destino = $request->input('destino');
        $dias = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
        if (!in_array($origem, $dias) || !in_array($destino, $dias)) {
            return back()->withErrors(['error' => 'Dia da semana inválido.']);
        }
        // Remove todas as vagas do dia de destino antes de clonar
        $unidade->vagas()->where('dia_semana', $destino)->delete();
        $vagasOrigem = $unidade->vagas()->where('dia_semana', $origem)->get();
        $clonadas = 0;
        foreach ($vagasOrigem as $vaga) {
            Vaga::create([
                'unidade_id' => $unidade->id,
                'setor_id' => $vaga->setor_id,
                'turno_id' => $vaga->turno_id,
                'dia_semana' => $destino,
                'quantidade_necessaria' => $vaga->quantidade_necessaria,
                'observacoes' => $vaga->observacoes,
                'status' => $vaga->status,
            ]);
            $clonadas++;
        }
        $msg = "Configurações clonadas com sucesso! ($clonadas vagas copiadas)";
        return redirect()->route('vagas.index', ['unidade' => $unidade->id, 'dia' => $destino])
            ->with('success', $msg);
    }

    /**
     * Exibe detalhes de uma vaga específica
     */
    public function show(Unidade $unidade, Vaga $vaga)
    {
        // Verificar se a vaga pertence à unidade
        if ($vaga->unidade_id !== $unidade->id) {
            abort(404);
        }

        $vaga->load(['setor', 'turno', 'alocacoes.plantonista']);

        return view('vagas.show', compact('unidade', 'vaga'));
    }

    /**
     * Formulário para editar vaga
     */
    public function edit(Unidade $unidade, Vaga $vaga)
    {
        // Verificar se a vaga pertence à unidade
        if ($vaga->unidade_id !== $unidade->id) {
            abort(404);
        }

        $setores = Setor::where('status', 'ativo')->orderBy('nome')->get();
        $turnos = Turno::where('status', 'ativo')->orderBy('hora_inicio')->get();

        return view('vagas.edit', compact('unidade', 'vaga', 'setores', 'turnos'));
    }

    /**
     * Atualiza vaga
     */
    public function update(Request $request, Unidade $unidade, Vaga $vaga)
    {
        // Verificar se a vaga pertence à unidade
        if ($vaga->unidade_id !== $unidade->id) {
            abort(404);
        }

        $validated = $request->validate([
            'quantidade_necessaria' => 'required|integer|min:1|max:50',
            'observacoes' => 'nullable|string|max:500',
            'status' => 'required|in:ativo,inativo',
        ], [
            'quantidade_necessaria.required' => 'Informe a quantidade de médicos necessária.',
            'quantidade_necessaria.integer' => 'A quantidade deve ser um número inteiro.',
            'quantidade_necessaria.min' => 'A quantidade mínima é 1 médico.',
            'quantidade_necessaria.max' => 'A quantidade máxima é 50 médicos.',
            'status.required' => 'Selecione o status.',
            'status.in' => 'Status inválido.',
        ]);

        try {
            DB::beginTransaction();

            $vaga->update($validated);

            DB::commit();

            return redirect()
                ->route('vagas.show', [$unidade, $vaga])
                ->with('success', 'Configuração de vaga atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao atualizar configuração: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove vaga
     */
    public function destroy(Unidade $unidade, Vaga $vaga)
    {
        // Verificar se a vaga pertence à unidade
        if ($vaga->unidade_id !== $unidade->id) {
            abort(404);
        }

        // Verificar se existem alocações
        if ($vaga->alocacoes()->count() > 0) {
            return back()->withErrors([
                'error' => 'Não é possível excluir esta configuração pois existem alocações vinculadas a ela.'
            ]);
        }

        try {
            DB::beginTransaction();

            $vaga->delete();

            DB::commit();

            return redirect()
                ->route('vagas.index', $unidade)
                ->with('success', 'Configuração de vaga excluída com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Erro ao excluir configuração: ' . $e->getMessage()
            ]);
        }
    }
}
