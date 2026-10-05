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
        Schema::create('spiritual_guidance_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained('spiritual_guidance_schedules')->nullOnDelete();
            $table->string('mentor_name');
            $table->dateTime('meeting_at');
            $table->string('location')->nullable();
            $table->longText('report_content');
            $table->string('proof_file')->nullable();
            $table->string('status')->default('draft')->index();
            $table->dateTime('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spiritual_guidance_reports');
    }
};
