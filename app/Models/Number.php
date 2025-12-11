<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Number extends Model
{
    protected $fillable = [
        'number',
        'status',
        'sold_by',
        'sold_at',
        'notes',
    ];

    protected $casts = [
        'sold_at' => 'datetime',
    ];

    public function soldBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sold_by');
    }

    public function cliente(): HasOne
    {
        return $this->hasOne(Cliente::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Verificar si está disponible
    public function isAvailable(): bool
    {
        return $this->status === 'disponible';
    }

    // Verificar si está vendido
    public function isSold(): bool
    {
        return $this->status === 'vendido';
    }
}
