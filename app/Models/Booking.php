<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    public const ROOMS = ['Аудитория', 'Коворкинг', 'Кинозал'];
    public const PAYMENTS = ['При очном посещении', 'Перевод по СБП'];
    public const STATUSES = ['Новая', 'Мероприятие назначено', 'Завершено'];

    protected $fillable = [
        'user_id',
        'room',
        'event_date',
        'payment',
        'status',
        'review',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'Завершено';
    }
}
