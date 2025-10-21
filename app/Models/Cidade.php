<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cidade extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: Uma cidade pode ter muitas unidades
     */
    public function unidades(): HasMany
    {
        return $this->hasMany(Unidade::class);
    }
}
