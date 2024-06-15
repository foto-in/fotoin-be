<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\User;
use App\Models\Payment;



class Booking extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'photographer_id',
        'nama_pemesan',
        'nama_photographer',
        'acara',
        'lokasi',
        'sesi_foto',
        'tanggal_booking',
        'durasi',
        'konsep',
        'total_dp',
        'total_harga',
        'status',
        'status_paid',
        'waktu_mulai',
        'alasan_ditolak',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
    public $timestamps = false;
}
