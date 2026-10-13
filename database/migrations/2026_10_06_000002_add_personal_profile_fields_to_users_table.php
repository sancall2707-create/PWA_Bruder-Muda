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
            $table->date('birth_date')->nullable()->after('bio');
            $table->string('parents_name')->nullable()->after('birth_date');
            $table->string('last_education')->nullable()->after('parents_name');
            $table->integer('entry_year')->nullable()->after('last_education');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['birth_date', 'parents_name', 'last_education', 'entry_year']);
        });
    }
};
