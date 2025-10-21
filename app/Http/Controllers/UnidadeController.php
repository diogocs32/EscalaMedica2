<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnidadeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $unidades = Unidade::with('cidade')->orderBy('nome')->paginate(15);

        return view('unidades.index', compact('unidades'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cidades = Cidade::orderBy('nome')->get();

        return view('unidades.create', compact('cidades'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'endereco' => 'required|string|max:500',
            'cidade_id' => 'required|exists:cidades,id',
            'status' => 'sometimes|in:ativo,inativo'
        ]);

        try {
            DB::beginTransaction();

            $unidade = Unidade::create([
                'nome' => $request->nome,
                'endereco' => $request->endereco,
                'cidade_id' => $request->cidade_id,
                'status' => $request->status ?? 'ativo'
            ]);

            DB::commit();

            return redirect()
                ->route('unidades.index')
                ->with('success', 'Unidade cadastrada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao cadastrar unidade: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Unidade $unidade)
    {
        $unidade->load(['cidade', 'vagas.setor', 'vagas.turno']);

        return view('unidades.show', compact('unidade'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unidade $unidade)
    {
        $cidades = Cidade::orderBy('nome')->get();

        return view('unidades.edit', compact('unidade', 'cidades'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unidade $unidade)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'endereco' => 'required|string|max:500',
            'cidade_id' => 'required|exists:cidades,id',
            'status' => 'sometimes|in:ativo,inativo'
        ]);

        try {
            DB::beginTransaction();

            $unidade->update([
                'nome' => $request->nome,
                'endereco' => $request->endereco,
                'cidade_id' => $request->cidade_id,
                'status' => $request->status ?? $unidade->status
            ]);

            DB::commit();

            return redirect()
                ->route('unidades.show', $unidade)
                ->with('success', 'Unidade atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao atualizar unidade: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unidade $unidade)
    {
        try {
            // Verificar se existem vagas associadas
            if ($unidade->vagas()->exists()) {
                return back()
                    ->withErrors(['error' => 'Não é possível excluir esta unidade pois existem vagas associadas.']);
            }

            $unidade->delete();

            return redirect()
                ->route('unidades.index')
                ->with('success', 'Unidade removida com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao remover unidade: ' . $e->getMessage()]);
        }
    }
}
