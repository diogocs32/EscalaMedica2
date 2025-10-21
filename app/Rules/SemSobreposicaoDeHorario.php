<?php

namespace App\Rules;

use App\Models\Alocacao;
use App\Models\Vaga;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SemSobreposicaoDeHorario implements ValidationRule
{
    protected $plantonistaId;
    protected $vagaId;
    protected $dataPlantao;
    protected $alocacaoId;

    public function __construct($plantonistaId, $vagaId, $dataPlantao, $alocacaoId = null)
    {
        $this->plantonistaId = $plantonistaId;
        $this->vagaId = $vagaId;
        $this->dataPlantao = $dataPlantao;
        $this->alocacaoId = $alocacaoId; // Para casos de atualização
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            // Buscar a vaga e o turno associado
            $vaga = Vaga::with('turno')->findOrFail($this->vagaId);
            $turno = $vaga->turno;

            // Calcular o novo horário de início e fim do plantão
            $novoInicio = Carbon::parse($this->dataPlantao)
                ->setTimeFromTimeString($turno->hora_inicio->format('H:i:s'));

            $novoFim = Carbon::parse($this->dataPlantao)
                ->setTimeFromTimeString($turno->hora_fim->format('H:i:s'));

            // Tratamento de "Corujão" (turnos que atravessam a meia-noite)
            if ($novoFim->lessThanOrEqualTo($novoInicio)) {
                $novoFim->addDay();
            }

            // Construir a query para verificar conflitos
            $conflitosQuery = Alocacao::where('plantonista_id', $this->plantonistaId)
                ->whereIn('status', ['agendado', 'em_andamento'])
                ->where('data_hora_inicio', '<', $novoFim)
                ->where('data_hora_fim', '>', $novoInicio);

            // Se for uma atualização, excluir a própria alocação da verificação
            if ($this->alocacaoId) {
                $conflitosQuery->where('id', '!=', $this->alocacaoId);
            }

            $conflitos = $conflitosQuery->exists();

            if ($conflitos) {
                $fail('Este plantonista já está alocado em um horário conflitante.');
            }
        } catch (\Exception $e) {
            $fail('Erro ao validar sobreposição de horário: ' . $e->getMessage());
        }
    }

    /**
     * Método estático para facilitar o uso da regra
     */
    public static function create($plantonistaId, $vagaId, $dataPlantao, $alocacaoId = null)
    {
        return new static($plantonistaId, $vagaId, $dataPlantao, $alocacaoId);
    }
}
