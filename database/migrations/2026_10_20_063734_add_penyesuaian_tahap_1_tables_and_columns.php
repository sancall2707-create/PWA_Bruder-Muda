<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom profil lengkap di tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->string('birth_place')->nullable()->after('bio');
            $table->json('family_data')->nullable()->after('birth_place');
            $table->json('education_data')->nullable()->after('family_data');
            $table->json('formation_data')->nullable()->after('education_data');
            $table->json('assignment_data')->nullable()->after('formation_data');
        });

        // 2. Buat tabel pesan satu arah (direct messages) dari Super Admin / Admin ke User
        Schema::create('direct_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->timestamp('read_at')->nullable()->index();
            $table->timestamps();
        });

        // 3. Buat tabel catatan pembinaan bruder (Bruder Notes) sesuai mockup halaman 6
        Schema::create('bruder_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('category');
            $table->longText('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bruder_notes');
        Schema::dropIfExists('direct_messages');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'birth_place',
                'family_data',
                'education_data',
                'formation_data',
                'assignment_data',
            ]);
        });
    }
};
