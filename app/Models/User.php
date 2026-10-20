<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * Field role, is_active, dan password sengaja tidak dimasukkan ke sini
     * untuk mencegah privilege escalation atau penimpaan tidak sengaja.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'profile_photo',
        'community_location',
        'phone',
        'bio',
        'birth_date',
        'birth_place',
        'parents_name',
        'last_education',
        'entry_year',
        'family_data',
        'education_data',
        'formation_data',
        'assignment_data',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'birth_date' => 'date',
            'entry_year' => 'integer',
            'family_data' => 'array',
            'education_data' => 'array',
            'formation_data' => 'array',
            'assignment_data' => 'array',
        ];
    }

    /**
     * Sapaan Provinsial yang dibuat oleh user (Super Admin / Admin).
     */
    public function provincialMessagesCreated(): HasMany
    {
        return $this->hasMany(ProvincialMessage::class, 'created_by');
    }

    /**
     * Jadwal Bimbingan Rohani milik user.
     */
    public function spiritualGuidanceSchedules(): HasMany
    {
        return $this->hasMany(SpiritualGuidanceSchedule::class);
    }

    /**
     * Laporan Bimbingan Rohani milik user.
     */
    public function spiritualGuidanceReports(): HasMany
    {
        return $this->hasMany(SpiritualGuidanceReport::class);
    }

    /**
     * Jadwal Wawanhati milik user.
     */
    public function wawanhatiSchedules(): HasMany
    {
        return $this->hasMany(WawanhatiSchedule::class);
    }

    /**
     * Laporan Wawanhati milik user.
     */
    public function wawanhatiReports(): HasMany
    {
        return $this->hasMany(WawanhatiReport::class);
    }

    /**
     * Agenda pribadi milik user.
     */
    public function personalAgendas(): HasMany
    {
        return $this->hasMany(PersonalAgenda::class);
    }

    /**
     * Jurnal Pengembangan Diri milik user.
     */
    public function selfDevelopmentJournals(): HasMany
    {
        return $this->hasMany(SelfDevelopmentJournal::class);
    }

    /**
     * Catatan pembinaan yang dimiliki oleh user (Bruder Muda).
     */
    public function bruderNotes(): HasMany
    {
        return $this->hasMany(BruderNote::class, 'user_id');
    }

    /**
     * Catatan pembinaan yang ditulis oleh user (Super Admin / Admin).
     */
    public function authoredBruderNotes(): HasMany
    {
        return $this->hasMany(BruderNote::class, 'author_id');
    }

    /**
     * Pesan satu arah yang diterima oleh user.
     */
    public function directMessagesReceived(): HasMany
    {
        return $this->hasMany(DirectMessage::class, 'recipient_id');
    }

    /**
     * Pesan satu arah yang dikirimkan oleh user.
     */
    public function directMessagesSent(): HasMany
    {
        return $this->hasMany(DirectMessage::class, 'sender_id');
    }

    /**
     * Jumlah pesan satu arah yang belum dibaca.
     */
    public function unreadDirectMessagesCount(): int
    {
        return $this->directMessagesReceived()->whereNull('read_at')->count();
    }

    /**
     * Cek apakah user memiliki salah satu dari role yang ditentukan.
     */
    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    /**
     * Cek apakah user adalah Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Cek apakah user adalah Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah User reguler.
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }
}
