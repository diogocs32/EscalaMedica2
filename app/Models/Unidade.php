<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Turno;

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
     * Relacionamento: Turnos associados a esta unidade (via pivot unidade_turno)
     */
    public function turnos(): BelongsToMany
    {
        return $this->belongsToMany(Turno::class, 'unidade_turno');
    }

    /**
     * Relacionamento: Escala padrão desta unidade (template rotativo de 5 semanas)
     */
    public function escalaPadrao(): HasMany
    {
        return $this->hasMany(EscalaPadrao::class);
    }

    /**
     * Pegar escala padrão ativa
     */
    public function escalaPadraoAtiva()
    {
        return $this->escalaPadrao()->where('status', 'ativo')->first();
    }

    /**
     * Scope para unidades ativas
     */
    public function scopeAtivo($query)
    {
        return $query->where('status', 'ativo');
    }
}
