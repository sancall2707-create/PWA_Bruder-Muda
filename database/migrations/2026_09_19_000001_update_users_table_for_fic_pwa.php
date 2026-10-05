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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('name');
            $table->string('email')->nullable()->change();
            $table->string('role')->default('user')->after('password')->index();
            $table->string('profile_photo')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('profile_photo')->index();
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['is_active']);
            $table->dropUnique(['username']);
            $table->dropSoftDeletes();
            $table->dropColumn(['username', 'role', 'profile_photo', 'is_active']);
            $table->string('email')->nullable(false)->change();
        });
    }
};
