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
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: Um setor pode ter muitas vagas (através de Unidade + Turno)
     * Agora Setor é global e a relação com Unidade é feita através da tabela vagas
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
