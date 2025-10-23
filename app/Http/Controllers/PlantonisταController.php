<?php

namespace App\Http\Controllers;

use App\Models\Plantonista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlantonisταController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plantonistas = Plantonista::orderBy('nome')->paginate(15);

        return view('plantonistas.index', compact('plantonistas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('plantonistas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'crm' => 'required|string|max:20|unique:plantonistas,crm',
            'email' => 'required|email|max:255|unique:plantonistas,email',
            'telefone' => 'required|string|max:20',
            'status' => 'sometimes|in:ativo,inativo'
        ]);

        try {
            DB::beginTransaction();

            $plantonista = Plantonista::create([
                'nome' => $request->nome,
                'crm' => $request->crm,
                'email' => $request->email,
                'telefone' => $request->telefone,
                'status' => $request->status ?? 'ativo'
            ]);

            DB::commit();

            return redirect()
                ->route('plantonistas.index')
                ->with('success', 'Plantonista cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao cadastrar plantonista: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Plantonista $plantonista)
    {
        $plantonista->load(['alocacoes.vaga.unidade', 'alocacoes.vaga.setor', 'alocacoes.vaga.turno']);

        return view('plantonistas.show', compact('plantonista'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Plantonista $plantonista)
    {
        return view('plantonistas.edit', compact('plantonista'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Plantonista $plantonista)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'crm' => 'required|string|max:20|unique:plantonistas,crm,' . $plantonista->id,
            'email' => 'required|email|max:255|unique:plantonistas,email,' . $plantonista->id,
            'telefone' => 'required|string|max:20',
            'status' => 'sometimes|in:ativo,inativo'
        ]);

        try {
            DB::beginTransaction();

            $plantonista->update([
                'nome' => $request->nome,
                'crm' => $request->crm,
                'email' => $request->email,
                'telefone' => $request->telefone,
                'status' => $request->status ?? $plantonista->status
            ]);

            DB::commit();

            return redirect()
                ->route('plantonistas.show', $plantonista)
                ->with('success', 'Plantonista atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao atualizar plantonista: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plantonista $plantonista)
    {
        try {
            // Verificar se existem alocações associadas
            if ($plantonista->alocacoes()->exists()) {
                return back()
                    ->withErrors(['error' => 'Não é possível excluir este plantonista pois existem alocações associadas.']);
            }

            DB::beginTransaction();

            // Antes de excluir, transformar todos os slots da escala padrão em buraco
            $buracos = DB::table('alocacoes_template')
                ->where('plantonista_id', $plantonista->id)
                ->update(['plantonista_id' => null]);

            $plantonista->delete();

            DB::commit();

            return redirect()
                ->route('plantonistas.index')
                ->with('success', 'Plantonista removido com sucesso! Todos os slots da escala padrão que ele cobria viraram buraco.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'Erro ao remover plantonista: ' . $e->getMessage()]);
        }
    }

    /**
     * API: Retorna plantonistas ativos para atribuição rápida
     */
    public function apiAtivos()
    {
        $plantonistas = Plantonista::where('status', 'ativo')
            ->orderBy('nome')
            ->get(['id', 'nome', 'crm', 'especialidade']);

        return response()->json($plantonistas);
    }
}
