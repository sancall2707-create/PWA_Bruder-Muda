<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WawanhatiReport extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     * Ini laporan wawanhati, BUKAN chat/messaging.
     *
     * @var string
     */
    protected $table = 'wawanhati_reports';

    /**
     * The attributes that are mass assignable.
     * Tidak menyertakan approval/review fields karena belum ditentukan.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'schedule_id',
        'local_leader_name',
        'meeting_at',
        'location',
        'report_content',
        'proof_file',
        'status',
        'submitted_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meeting_at' => 'datetime',
            'submitted_at' => 'datetime',
        ];
    }

    /**
     * Pemilik laporan wawanhati.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Jadwal wawanhati yang terkait dengan laporan ini (opsional).
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(WawanhatiSchedule::class, 'schedule_id');
    }
}
