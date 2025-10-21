<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CidadeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cidades = Cidade::withCount('unidades')->orderBy('nome')->paginate(15);

        return view('cidades.index', compact('cidades'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cidades.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'estado' => [
                'required',
                'string',
                'size:2',
                // garantir unicidade combinada (nome + estado)
                Rule::unique('cidades')->where(function ($q) use ($request) {
                    return $q->where('estado', strtoupper($request->estado ?? ''));
                })
            ],
        ]);

        try {
            DB::beginTransaction();

            $cidade = Cidade::create([
                'nome' => $request->nome,
                'estado' => strtoupper($request->estado),
            ]);

            DB::commit();

            return redirect()
                ->route('cidades.index')
                ->with('success', 'Cidade cadastrada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao cadastrar cidade: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Cidade $cidade)
    {
        $cidade->load(['unidades' => function ($query) {
            $query->orderBy('nome');
        }]);

        return view('cidades.show', compact('cidade'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cidade $cidade)
    {
        return view('cidades.edit', compact('cidade'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cidade $cidade)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'estado' => [
                'required',
                'string',
                'size:2',
                Rule::unique('cidades')->ignore($cidade->id)->where(function ($q) use ($request) {
                    return $q->where('estado', strtoupper($request->estado ?? ''));
                })
            ],
        ]);

        try {
            DB::beginTransaction();

            $cidade->update([
                'nome' => $request->nome,
                'estado' => strtoupper($request->estado),
            ]);

            DB::commit();

            return redirect()
                ->route('cidades.show', $cidade)
                ->with('success', 'Cidade atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao atualizar cidade: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cidade $cidade)
    {
        try {
            // Verificar se existem unidades associadas
            if ($cidade->unidades()->exists()) {
                return back()
                    ->withErrors(['error' => 'Não é possível excluir esta cidade pois existem unidades associadas.']);
            }

            $cidade->delete();

            return redirect()
                ->route('cidades.index')
                ->with('success', 'Cidade removida com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao remover cidade: ' . $e->getMessage()]);
        }
    }
}
