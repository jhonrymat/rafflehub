<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'number_id',
        'user_id',
        'payment_status',
        'total_paid',
        'notes',
    ];

    protected $casts = [
        'total_paid' => 'decimal:2',
    ];

    public function number(): BelongsTo
    {
        return $this->belongsTo(Number::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Calcular saldo pendiente
    public function pendingBalance(): float
    {
        $config = RaffleConfig::current();
        $ticketPrice = $config ? $config->ticket_price : 50000;
        return $ticketPrice - $this->total_paid;
    }

    // Verificar si está completamente pagado
    public function isFullyPaid(): bool
    {
        return $this->payment_status === 'pagado';
    }
}
