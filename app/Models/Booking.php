<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'photographer_id',
        'acara',
        'lokasi',
        'sesi_foto',
        'tanggal_booking',
        'durasi',
        'konsep',
        'total_harga',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
