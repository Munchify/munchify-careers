<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('application_number')->unique();
            $table->foreignId('job_listing_id')->constrained();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('location')->nullable();
            $table->foreignId('current_stage_id')->nullable()->constrained('job_pipeline_stages');
            $table->enum('status', ['active', 'hired', 'rejected', 'withdrawn'])->default('active');
            $table->enum('source', ['direct', 'referral', 'social', 'other'])->default('direct');
            $table->string('referral_name')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('video_path')->nullable();
            $table->json('screening_answers')->nullable();
            $table->decimal('overall_score', 3, 1)->nullable();
            $table->boolean('is_starred')->default(false);
            $table->boolean('is_knockout')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->text('cover_letter')->nullable();
            $table->string('current_role')->nullable();
            $table->string('experience_years')->nullable();
            $table->text('motivation')->nullable();
            $table->string('skills')->nullable();
            $table->timestamp('hired_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
