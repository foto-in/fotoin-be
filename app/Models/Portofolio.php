<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Portofolio extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'photographer_id',
        'title',
        'description',
        'url_photo',
    ];

    protected $casts = [
        'url_photo' => 'array',
    ];

    public function photographer() : BelongsTo
    {
        return $this->BelongsTo(Photographer::class);
    }
}
