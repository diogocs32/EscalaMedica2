<?php

namespace App\Http\Controllers;

use App\Models\Setor;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $setores = Setor::orderBy('nome')->paginate(15);

        return view('setores.index', compact('setores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $unidades = Unidade::with('cidade')->ativo()->orderBy('nome')->get();

        return view('setores.create', compact('unidades'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:setores,nome',
            'unidade_id' => 'required|exists:unidades,id',
            'descricao' => 'nullable|string|max:1000',
            'status' => 'sometimes|in:ativo,inativo'
        ]);

        try {
            DB::beginTransaction();

            $setor = Setor::create([
                'nome' => $request->nome,
                'unidade_id' => $request->unidade_id,
                'descricao' => $request->descricao,
                'status' => $request->status ?? 'ativo'
            ]);

            DB::commit();

            return redirect()
                ->route('setores.index')
                ->with('success', 'Setor criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao criar setor: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Setor $setor)
    {
        $setor->load(['vagas.unidade', 'vagas.turno']);

        return view('setores.show', compact('setor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Setor $setor)
    {
        $unidades = Unidade::with('cidade')->ativo()->orderBy('nome')->get();

        return view('setores.edit', compact('setor', 'unidades'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Setor $setor)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:setores,nome,' . $setor->id,
            'descricao' => 'nullable|string|max:1000',
            'status' => 'sometimes|in:ativo,inativo'
        ]);

        try {
            DB::beginTransaction();

            $setor->update([
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'status' => $request->status ?? $setor->status
            ]);

            DB::commit();

            return redirect()
                ->route('setores.show', $setor)
                ->with('success', 'Setor atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao atualizar setor: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setor $setor)
    {
        try {
            // Verificar se existem vagas associadas
            if ($setor->vagas()->exists()) {
                return back()
                    ->withErrors(['error' => 'Não é possível excluir este setor pois existem vagas associadas.']);
            }

            $setor->delete();

            return redirect()
                ->route('setores.index')
                ->with('success', 'Setor removido com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao remover setor: ' . $e->getMessage()]);
        }
    }
}
