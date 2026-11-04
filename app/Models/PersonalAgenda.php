<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalAgenda extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'personal_agendas';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'start_at',
        'end_at',
        'location',
        'description',
        'status',
        'is_provincial',
        'broadcast_key',
    ];

    /**
     * Agenda terdekat untuk beranda Admin / Super Admin.
     * Agenda Provinsi (siaran ke semua bruder) hanya tampil satu kali.
     * Agenda pribadi bruder tetap tampil per bruder.
     */
    public static function upcomingForOverview(\Carbon\Carbon $now, int $limit = 5): \Illuminate\Support\Collection
    {
        $rows = static::with('user')
            ->where('start_at', '>=', $now)
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_at')
            ->limit(200)
            ->get();

        $provinsi = $rows->where('is_provincial', true)
            ->unique(fn ($p) => $p->broadcast_key ?: md5($p->title.'|'.$p->start_at.'|'.$p->location))
            ->take($limit)
            ->map(fn ($p) => (object) [
                'bruder'   => 'Seluruh Bruder Muda',
                'type'     => 'Agenda Provinsi',
                'title'    => $p->title,
                'datetime' => $p->start_at ? $p->start_at->translatedFormat('d M Y, H:i').' WIB' : '-',
                'location' => $p->location ?: 'Komunitas',
                'color'    => '#0E3366',
                'raw_date' => $p->start_at,
            ]);

        $pribadi = $rows->where('is_provincial', false)
            ->take($limit)
            ->map(fn ($p) => (object) [
                'bruder'   => $p->user->name ?? 'Bruder Muda',
                'type'     => 'Agenda Pribadi',
                'title'    => $p->title,
                'datetime' => $p->start_at ? $p->start_at->translatedFormat('d M Y, H:i').' WIB' : '-',
                'location' => $p->location ?: 'Komunitas',
                'color'    => '#B57900',
                'raw_date' => $p->start_at,
            ]);

        return $provinsi->concat($pribadi)->values();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'is_provincial' => 'boolean',
        ];
    }

    /**
     * Pemilik agenda pribadi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
