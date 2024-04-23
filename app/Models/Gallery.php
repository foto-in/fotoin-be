<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Gallery extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'photographer_id',
        'images',
        'total_images',
        'duration',    
    ];
}
