<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plantonista extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'crm',
        'email',
        'telefone',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: Um plantonista pode ter muitas alocações
     */
    public function alocacoes(): HasMany
    {
        return $this->hasMany(Alocacao::class);
    }

    /**
     * Scope para plantonistas ativos
     */
    public function scopeAtivo($query)
    {
        return $query->where('status', 'ativo');
    }

    /**
     * Accessor para formatar o nome do plantonista
     */
    public function getNomeCompletoAttribute()
    {
        return $this->nome . ($this->crm ? " (CRM: {$this->crm})" : '');
    }
}
