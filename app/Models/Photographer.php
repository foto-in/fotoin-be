<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photographer extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'no_hp',
        'no_telegram',
        'email',
        'specialization',
    ];

    public function user() : BelongsTo
    {
        return $this->BelongsTo(User::class);
    }
}
