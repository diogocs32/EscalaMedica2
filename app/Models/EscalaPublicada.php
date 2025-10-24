<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EscalaPublicada extends Model
{
    use HasFactory;

    protected $table = 'escalas_publicadas';

    protected $fillable = [
        'unidade_id',
        'escala_padrao_id',
        'ano',
        'mes',
        'status',
    ];

    protected $casts = [
        'ano' => 'integer',
        'mes' => 'string',
    ];

    /**
     * Relacionamentos
     */
    public function unidade()
    {
        return $this->belongsTo(Unidade::class);
    }

    public function escalaPadrao()
    {
        return $this->belongsTo(EscalaPadrao::class);
    }

    public function alocacoes()
    {
        return $this->hasMany(AlocacaoPublicada::class);
    }

    /**
     * Métricas da escala
     */
    public function getTotalSlotsAttribute()
    {
        return $this->alocacoes()->count();
    }

    public function getPreenchidosAttribute()
    {
        return $this->alocacoes()->whereNotNull('plantonista_id')->count();
    }

    public function getBuracosAttribute()
    {
        return $this->total_slots - $this->preenchidos;
    }

    public function getTaxaAttribute()
    {
        if ($this->total_slots == 0) return 0;
        return round(($this->preenchidos / $this->total_slots) * 100, 1);
    }

    /**
     * Recalcula as métricas da escala.
     * Como as métricas são computed attributes, este método apenas
     * recarrega o relacionamento para garantir dados atualizados.
     */
    public function recalcularMetricas(): void
    {
        $this->load('alocacoes');
    }
}
