<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Setor extends Model
{
    use HasFactory;

    protected $table = 'setores';

    protected $fillable = [
        'nome',
        'descricao',
        'unidade_id',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: Um setor pode ter muitas vagas
     */
    public function vagas(): HasMany
    {
        return $this->hasMany(Vaga::class);
    }

    /**
     * Scope para setores ativos
     */
    public function scopeAtivo($query)
    {
        return $query->where('status', 'ativo');
    }
}
