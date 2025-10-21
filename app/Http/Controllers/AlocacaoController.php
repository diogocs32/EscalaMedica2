<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlocacaoRequest;
use App\Models\Alocacao;
use App\Models\Plantonista;
use App\Models\Vaga;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlocacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $alocacoes = Alocacao::with(['plantonista', 'vaga.unidade', 'vaga.setor', 'vaga.turno'])
            ->orderBy('data_plantao', 'desc')
            ->paginate(15);

        return view('alocacoes.index', compact('alocacoes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $plantonistas = Plantonista::ativo()->orderBy('nome')->get();
        $vagas = Vaga::with(['unidade', 'setor', 'turno'])
            ->ativo()
            ->get();

        return view('alocacoes.create', compact('plantonistas', 'vagas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAlocacaoRequest $request)
    {
        try {
            DB::beginTransaction();

            $alocacao = Alocacao::create([
                'plantonista_id' => $request->plantonista_id,
                'vaga_id' => $request->vaga_id,
                'data_plantao' => $request->data_plantao,
                'observacoes' => $request->observacoes,
                'status' => $request->status ?? 'agendado'
            ]);

            DB::commit();

            return redirect()
                ->route('alocacoes.show', $alocacao)
                ->with('success', 'Alocação criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao criar alocação: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Alocacao $alocacao)
    {
        $alocacao->load(['plantonista', 'vaga.unidade', 'vaga.setor', 'vaga.turno']);

        return view('alocacoes.show', compact('alocacao'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Alocacao $alocacao)
    {
        $plantonistas = Plantonista::ativo()->orderBy('nome')->get();
        $vagas = Vaga::with(['unidade', 'setor', 'turno'])
            ->ativo()
            ->get()
            ->groupBy('unidade.nome');

        return view('alocacoes.edit', compact('alocacao', 'plantonistas', 'vagas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreAlocacaoRequest $request, Alocacao $alocacao)
    {
        try {
            DB::beginTransaction();

            $alocacao->update([
                'plantonista_id' => $request->plantonista_id,
                'vaga_id' => $request->vaga_id,
                'data_plantao' => $request->data_plantao,
                'observacoes' => $request->observacoes,
                'status' => $request->status ?? $alocacao->status
            ]);

            DB::commit();

            return redirect()
                ->route('alocacoes.show', $alocacao)
                ->with('success', 'Alocação atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao atualizar alocação: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Alocacao $alocacao)
    {
        try {
            $alocacao->delete();

            return redirect()
                ->route('alocacoes.index')
                ->with('success', 'Alocação removida com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao remover alocação: ' . $e->getMessage()]);
        }
    }

    /**
     * Endpoint API para verificar conflitos de horário
     */
    public function verificarConflitos(Request $request)
    {
        $request->validate([
            'plantonista_id' => 'required|exists:plantonistas,id',
            'vaga_id' => 'required|exists:vagas,id',
            'data_plantao' => 'required|date',
            'alocacao_id' => 'nullable|exists:alocacoes,id'
        ]);

        $vaga = Vaga::with('turno')->findOrFail($request->vaga_id);
        $turno = $vaga->turno;

        // Calcular horários
        $novoInicio = Carbon::parse($request->data_plantao)
            ->setTimeFromTimeString($turno->hora_inicio->format('H:i:s'));

        $novoFim = Carbon::parse($request->data_plantao)
            ->setTimeFromTimeString($turno->hora_fim->format('H:i:s'));

        if ($novoFim->lessThanOrEqualTo($novoInicio)) {
            $novoFim->addDay();
        }

        // Verificar conflitos
        $conflitosQuery = Alocacao::where('plantonista_id', $request->plantonista_id)
            ->whereIn('status', ['agendado', 'em_andamento'])
            ->where('data_hora_inicio', '<', $novoFim)
            ->where('data_hora_fim', '>', $novoInicio);

        if ($request->alocacao_id) {
            $conflitosQuery->where('id', '!=', $request->alocacao_id);
        }

        $conflitos = $conflitosQuery->with(['vaga.unidade', 'vaga.setor', 'vaga.turno'])->get();

        return response()->json([
            'tem_conflitos' => $conflitos->isNotEmpty(),
            'conflitos' => $conflitos,
            'horario_calculado' => [
                'inicio' => $novoInicio->format('Y-m-d H:i:s'),
                'fim' => $novoFim->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
