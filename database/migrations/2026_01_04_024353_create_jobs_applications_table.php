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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')
                ->constrained('available_jobs')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->text('cover_letter')->nullable();
            $table->enum('status', ['submitted', 'viewed', 'shortlisted', 'rejected', 'hired', 'cancelled'])->default('submitted');

            $table->timestamp('applied_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();

            // Prevent duplicate applications
            $table->unique(['job_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs_applications');
    }
};
