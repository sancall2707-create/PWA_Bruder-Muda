<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class ProvincialMessage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'provincial_messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'kind',
        'content',
        'image',
        'author_name',
        'author_role',
        'published_at',
        'starts_at',
        'is_active',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'starts_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Sapaan rohani yang sedang tayang. Hanya satu yang aktif.
     */
    public static function currentSapaan(): ?self
    {
        return static::query()
            ->where('kind', 'sapaan')
            ->where('is_active', true)
            ->latest('published_at')
            ->first();
    }

    /**
     * Pengumuman yang sedang tayang untuk seluruh bruder.
     */
    public static function currentAnnouncements(): Collection
    {
        return static::query()
            ->where('kind', 'pengumuman')
            ->where('is_active', true)
            ->orderByRaw('CASE WHEN starts_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('starts_at')
            ->latest('published_at')
            ->get();
    }

    /**
     * Sapaan baru menggantikan sapaan aktif sebelumnya, tanpa menarik pengumuman.
     */
    public function retireOtherSapaan(): void
    {
        if ($this->kind !== 'sapaan' || ! $this->is_active) {
            return;
        }

        static::query()
            ->where('kind', 'sapaan')
            ->where('id', '!=', $this->id)
            ->update(['is_active' => false]);
    }

    public function publishedLabel(): string
    {
        $moment = $this->published_at ?? $this->created_at ?? now();

        return $moment->copy()->locale('id')->translatedFormat('j F Y');
    }

    public function scheduleLabel(): ?string
    {
        if (! $this->starts_at instanceof Carbon) {
            return null;
        }

        return $this->starts_at->copy()->locale('id')->translatedFormat('j F Y').' • '.$this->starts_at->format('H:i').' WIB';
    }

    /**
     * @param  Builder<ProvincialMessage>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Creator atau pembuat Sapaan Provinsial.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
