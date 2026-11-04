<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Penanda Agenda Provinsi (ditulis admin/super admin).
 * Hanya MENAMBAH kolom; tidak ada data yang dihapus atau diubah isinya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personal_agendas', function (Blueprint $table) {
            $table->boolean('is_provincial')->default(false)->after('status');
            $table->string('broadcast_key', 64)->nullable()->after('is_provincial')->index();
        });

        // Tandai agenda siaran lama: kelompok judul+waktu+lokasi yang sama
        // dan jumlah salinannya sebanyak akun Bruder Muda aktif.
        $akunAktif = DB::table('users')
            ->where('role', 'user')->where('is_active', true)->whereNull('deleted_at')
            ->count();

        if ($akunAktif < 2) {
            return;
        }

        $kelompok = DB::table('personal_agendas')
            ->select('title', 'start_at', 'location', DB::raw('COUNT(*) as jumlah'))
            ->whereNull('deleted_at')
            ->groupBy('title', 'start_at', 'location')
            ->havingRaw('COUNT(*) >= ?', [$akunAktif])
            ->get();

        foreach ($kelompok as $k) {
            $query = DB::table('personal_agendas')
                ->whereNull('deleted_at')
                ->where('title', $k->title)
                ->where('start_at', $k->start_at);

            $k->location === null ? $query->whereNull('location') : $query->where('location', $k->location);

            $query->update([
                'is_provincial' => true,
                'broadcast_key' => md5($k->title.'|'.$k->start_at.'|'.$k->location),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('personal_agendas', function (Blueprint $table) {
            $table->dropIndex(['broadcast_key']);
            $table->dropColumn(['is_provincial', 'broadcast_key']);
        });
    }
};
