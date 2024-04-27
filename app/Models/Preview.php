<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Preview extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'booking_id',
        'photographer_id',
        'photos',
    ];

    protected $casts = [
        'photos' => 'array',
    ];

    public $timestamps = false;

}
