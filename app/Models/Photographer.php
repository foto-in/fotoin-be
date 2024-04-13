<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Photographer extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'type',
        'no_hp',
        'no_telegram',
        'email',
        'camera',
        'specialization',
    ];

    protected $casts = [
        'camera' => 'array',
        'specialization' => 'array',
    ];

    public function user() : BelongsTo
    {
        return $this->BelongsTo(User::class);
    }

    public function portofolios() : HasMany
    {
        return $this->hasMany(Portofolio::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
