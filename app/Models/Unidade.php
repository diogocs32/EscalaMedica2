<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unidade extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'endereco',
        'cidade_id',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: Uma unidade pertence a uma cidade
     */
    public function cidade(): BelongsTo
    {
        return $this->belongsTo(Cidade::class);
    }

    /**
     * Relacionamento: Uma unidade pode ter muitas vagas
     */
    public function vagas(): HasMany
    {
        return $this->hasMany(Vaga::class);
    }

    /**
     * Scope para unidades ativas
     */
    public function scopeAtivo($query)
    {
        return $query->where('status', 'ativo');
    }
}
