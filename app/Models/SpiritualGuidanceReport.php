<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpiritualGuidanceReport extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'spiritual_guidance_reports';

    /**
     * The attributes that are mass assignable.
     * Tidak menyertakan approval/review fields karena belum ditentukan.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'schedule_id',
        'mentor_name',
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
     * Pemilik laporan bimbingan rohani.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Jadwal yang terkait dengan laporan ini (opsional).
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(SpiritualGuidanceSchedule::class, 'schedule_id');
    }
}
