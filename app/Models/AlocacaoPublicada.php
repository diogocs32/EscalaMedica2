<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlocacaoPublicada extends Model
{
    use HasFactory;

    protected $table = 'alocacoes_publicadas';

    protected $fillable = [
        'escala_publicada_id',
        'data',
        'turno_id',
        'setor_id',
        'plantonista_id',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'data' => 'date',
    ];

    /**
     * Relacionamentos
     */
    public function escalaPublicada()
    {
        return $this->belongsTo(EscalaPublicada::class);
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }

    public function setor()
    {
        return $this->belongsTo(Setor::class);
    }

    public function plantonista()
    {
        return $this->belongsTo(Plantonista::class);
    }
}
