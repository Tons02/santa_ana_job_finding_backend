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
        Schema::create('available_jobs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('title');
            $table->text('description');

            $table->string('location')->nullable();
            $table->boolean('is_remote')->default(false);
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'intership'])->default('full_time')->nullable();
            $table->string('experience_level')->nullable(); 
            $table->unsignedInteger('salary_min')->nullable();
            $table->unsignedInteger('salary_max')->nullable();
            $table->string('salary_currency', 3)->default('PHP');
            $table->enum('salary_period', ['yearly', 'monthly', 'weekly', 'hourly','semi_monthly'])->default('semi_monthly')->nullable();

            $table->enum('hiring_status', ['active', 'closed', 'paused'])->default('active')->nullable();
            $table->timestamp('posted_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('title');
            $table->index('location');
            $table->index('status');
            $table->index('employment_type');
            $table->index('posted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('available_jobs');
    }
};
