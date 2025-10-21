<?php

namespace App\Observers;

use App\Models\Alocacao;
use Carbon\Carbon;

class AlocacaoObserver
{
    /**
     * Handle the Alocacao "creating" event.
     * Calcula automaticamente data_hora_inicio e data_hora_fim
     */
    public function creating(Alocacao $alocacao): void
    {
        // Buscar o turno associado à vaga
        $turno = $alocacao->vaga->turno;

        // Calcular data_hora_inicio
        $inicio = Carbon::parse($alocacao->data_plantao)
            ->setTimeFromTimeString($turno->hora_inicio->format('H:i:s'));

        // Calcular data_hora_fim
        $fim = Carbon::parse($alocacao->data_plantao)
            ->setTimeFromTimeString($turno->hora_fim->format('H:i:s'));

        // Tratamento de "Corujão" (turnos que atravessam a meia-noite)
        if ($fim->lessThanOrEqualTo($inicio)) {
            $fim->addDay();
        }

        // Atribuir os valores calculados
        $alocacao->data_hora_inicio = $inicio;
        $alocacao->data_hora_fim = $fim;
    }

    /**
     * Handle the Alocacao "updating" event.
     * Recalcula as datas se data_plantao ou vaga_id mudaram
     */
    public function updating(Alocacao $alocacao): void
    {
        // Verifica se data_plantao ou vaga_id foram alterados
        if ($alocacao->isDirty(['data_plantao', 'vaga_id'])) {
            // Buscar o turno associado à vaga
            $turno = $alocacao->vaga->turno;

            // Recalcular data_hora_inicio
            $inicio = Carbon::parse($alocacao->data_plantao)
                ->setTimeFromTimeString($turno->hora_inicio->format('H:i:s'));

            // Recalcular data_hora_fim
            $fim = Carbon::parse($alocacao->data_plantao)
                ->setTimeFromTimeString($turno->hora_fim->format('H:i:s'));

            // Tratamento de "Corujão"
            if ($fim->lessThanOrEqualTo($inicio)) {
                $fim->addDay();
            }

            // Atribuir os valores recalculados
            $alocacao->data_hora_inicio = $inicio;
            $alocacao->data_hora_fim = $fim;
        }
    }

    /**
     * Handle the Alocacao "created" event.
     */
    public function created(Alocacao $alocacao): void
    {
        // Log ou notificação de criação, se necessário
    }

    /**
     * Handle the Alocacao "updated" event.
     */
    public function updated(Alocacao $alocacao): void
    {
        // Log ou notificação de atualização, se necessário
    }

    /**
     * Handle the Alocacao "deleted" event.
     */
    public function deleted(Alocacao $alocacao): void
    {
        // Log ou notificação de exclusão, se necessário
    }

    /**
     * Handle the Alocacao "restored" event.
     */
    public function restored(Alocacao $alocacao): void
    {
        // Lógica para restauração, se usando soft deletes
    }

    /**
     * Handle the Alocacao "force deleted" event.
     */
    public function forceDeleted(Alocacao $alocacao): void
    {
        // Lógica para exclusão forçada, se necessário
    }
}
