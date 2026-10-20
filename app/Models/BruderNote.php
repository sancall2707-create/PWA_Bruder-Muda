<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BruderNote extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'Catatan Hidup Pribadi',
        'Catatan Hidup Rohani/Doa',
        'Catatan Hidup Triprasetia',
        'Catatan Hidup Kerasulan',
    ];

    protected $fillable = [
        'user_id',
        'author_id',
        'category',
        'content',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
