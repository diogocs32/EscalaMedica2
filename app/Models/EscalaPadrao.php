<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EscalaPadrao extends Model
{
    protected $table = 'escalas_padrao';

    protected $fillable = [
        'unidade_id',
        'nome',
        'descricao',
        'status',
        'vigencia_inicio',
    ];

    protected $casts = [
        'vigencia_inicio' => 'date',
    ];

    /**
     * Unidade que possui esta escala padrão
     */
    public function unidade(): BelongsTo
    {
        return $this->belongsTo(Unidade::class);
    }

    /**
     * 5 semanas da escala (template rotativo)
     */
    public function semanas(): HasMany
    {
        return $this->hasMany(SemanaTemplate::class)->orderBy('numero_semana');
    }

    /**
     * Cria automaticamente as 5 semanas com 7 dias cada
     */
    public function criarEstruturaPadrao(): void
    {
        for ($semana = 1; $semana <= 5; $semana++) {
            $semanaTemplate = $this->semanas()->create([
                'numero_semana' => $semana,
                'nome' => "Semana $semana",
            ]);

            // Criar 7 dias para cada semana
            $dias = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
            foreach ($dias as $dia) {
                $semanaTemplate->dias()->create([
                    'dia_semana' => $dia,
                ]);
            }
        }
    }

    /**
     * Retorna o número da semana atual baseado na data de vigência
     * Sistema rotativo: semana 1 -> 2 -> 3 -> 4 -> 5 -> 1 -> ...
     */
    public function getSemanaAtual(\DateTime $data = null): int
    {
        $data = $data ?? new \DateTime();
        $inicio = $this->vigencia_inicio ?? new \DateTime();

        $diff = $data->diff($inicio);
        $semanasPassadas = floor($diff->days / 7);

        // Ciclo de 5 semanas (0-4) + 1 para ficar 1-5
        return ($semanasPassadas % 5) + 1;
    }
}
