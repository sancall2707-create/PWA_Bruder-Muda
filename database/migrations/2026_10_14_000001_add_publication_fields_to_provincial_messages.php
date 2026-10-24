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
        Schema::table('provincial_messages', function (Blueprint $table) {
            $table->string('kind')->default('sapaan')->after('title')->index();
            $table->string('author_role')->nullable()->after('author_name');
            $table->dateTime('starts_at')->nullable()->after('published_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provincial_messages', function (Blueprint $table) {
            $table->dropIndex(['kind']);
            $table->dropIndex(['starts_at']);
            $table->dropColumn(['kind', 'author_role', 'starts_at']);
        });
    }
};
