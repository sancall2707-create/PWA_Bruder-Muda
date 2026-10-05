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
