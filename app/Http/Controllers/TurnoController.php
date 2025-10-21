<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TurnoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $turnos = Turno::orderBy('hora_inicio')->paginate(15);

        return view('turnos.index', compact('turnos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('turnos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:turnos,nome',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'required|date_format:H:i',
            'periodo' => 'sometimes|in:diurno,noturno,misto',
            'status' => 'sometimes|in:ativo,inativo'
        ]);

        try {
            DB::beginTransaction();

            // Calcular duração em horas
            $inicio = Carbon::createFromFormat('H:i', $request->hora_inicio);
            $fim = Carbon::createFromFormat('H:i', $request->hora_fim);

            // Se fim <= início, é um turno que atravessa a meia-noite
            if ($fim->lessThanOrEqualTo($inicio)) {
                $fim->addDay();
            }

            $duracaoHoras = $inicio->diffInHours($fim);

            $turno = Turno::create([
                'nome' => $request->nome,
                'hora_inicio' => $request->hora_inicio . ':00',
                'hora_fim' => $request->hora_fim . ':00',
                'duracao_horas' => $duracaoHoras,
                'periodo' => $request->periodo ?? 'diurno',
                'status' => $request->status ?? 'ativo'
            ]);

            DB::commit();

            return redirect()
                ->route('turnos.index')
                ->with('success', 'Turno criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao criar turno: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Turno $turno)
    {
        $turno->load(['vagas.unidade', 'vagas.setor']);

        return view('turnos.show', compact('turno'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Turno $turno)
    {
        return view('turnos.edit', compact('turno'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Turno $turno)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:turnos,nome,' . $turno->id,
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'required|date_format:H:i',
            'periodo' => 'sometimes|in:diurno,noturno,misto',
            'status' => 'sometimes|in:ativo,inativo'
        ]);

        try {
            DB::beginTransaction();

            // Recalcular duração em horas
            $inicio = Carbon::createFromFormat('H:i', $request->hora_inicio);
            $fim = Carbon::createFromFormat('H:i', $request->hora_fim);

            if ($fim->lessThanOrEqualTo($inicio)) {
                $fim->addDay();
            }

            $duracaoHoras = $inicio->diffInHours($fim);

            $turno->update([
                'nome' => $request->nome,
                'hora_inicio' => $request->hora_inicio . ':00',
                'hora_fim' => $request->hora_fim . ':00',
                'duracao_horas' => $duracaoHoras,
                'periodo' => $request->periodo ?? $turno->periodo,
                'status' => $request->status ?? $turno->status
            ]);

            DB::commit();

            return redirect()
                ->route('turnos.show', $turno)
                ->with('success', 'Turno atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao atualizar turno: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Turno $turno)
    {
        try {
            // Verificar se existem vagas associadas
            if ($turno->vagas()->exists()) {
                return back()
                    ->withErrors(['error' => 'Não é possível excluir este turno pois existem vagas associadas.']);
            }

            $turno->delete();

            return redirect()
                ->route('turnos.index')
                ->with('success', 'Turno removido com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao remover turno: ' . $e->getMessage()]);
        }
    }
}
