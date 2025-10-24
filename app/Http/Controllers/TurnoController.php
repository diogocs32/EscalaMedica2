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

            // Calcular período automaticamente
            $periodo = $this->calcularPeriodo($request->hora_inicio, $request->hora_fim);

            $turno = Turno::create([
                'nome' => $request->nome,
                'hora_inicio' => $request->hora_inicio . ':00',
                'hora_fim' => $request->hora_fim . ':00',
                'duracao_horas' => $duracaoHoras,
                'periodo' => $periodo,
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

            // Calcular período automaticamente
            $periodo = $this->calcularPeriodo($request->hora_inicio, $request->hora_fim);

            $turno->update([
                'nome' => $request->nome,
                'hora_inicio' => $request->hora_inicio . ':00',
                'hora_fim' => $request->hora_fim . ':00',
                'duracao_horas' => $duracaoHoras,
                'periodo' => $periodo,
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
     * Calcula o período (diurno/noturno/misto) baseado nos horários
     * Diurno: 07:00 às 19:00
     * Noturno: 19:00 às 07:00
     * Misto: quando atravessa ambos os períodos
     */
    private function calcularPeriodo($horaInicio, $horaFim)
    {
        $inicio = Carbon::createFromFormat('H:i', $horaInicio);
        $fim = Carbon::createFromFormat('H:i', $horaFim);

        // Horários de referência
        $inicioDiurno = Carbon::createFromTime(7, 0); // 07:00
        $fimDiurno = Carbon::createFromTime(19, 0);    // 19:00

        // Se fim <= início, é um turno que atravessa a meia-noite
        if ($fim->lessThanOrEqualTo($inicio)) {
            $fim->addDay();
        }

        // Verificar se o turno está completamente dentro do período diurno
        if (
            $inicio->greaterThanOrEqualTo($inicioDiurno) &&
            $inicio->lessThan($fimDiurno) &&
            $fim->greaterThan($inicioDiurno) &&
            $fim->lessThanOrEqualTo($fimDiurno)
        ) {
            return 'diurno';
        }

        // Verificar se o turno está completamente dentro do período noturno
        if (($inicio->greaterThanOrEqualTo($fimDiurno) || $inicio->lessThan($inicioDiurno)) &&
            ($fim->greaterThan($fimDiurno) || $fim->lessThanOrEqualTo($inicioDiurno))
        ) {
            return 'noturno';
        }

        // Caso contrário, é misto (atravessa ambos os períodos)
        return 'misto';
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
