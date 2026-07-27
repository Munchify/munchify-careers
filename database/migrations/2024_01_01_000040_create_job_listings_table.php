<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('title');
            $table->foreignId('department_id')->constrained();
            $table->enum('type', ['full_time', 'part_time', 'contract', 'internship'])->default('full_time');
            $table->enum('location', ['on_site', 'remote', 'hybrid'])->default('on_site');
            $table->string('location_detail')->nullable();
            $table->longText('description');
            $table->longText('requirements');
            $table->longText('responsibilities');
            $table->string('salary_range')->nullable();
            $table->integer('slots')->default(1);
            $table->enum('status', ['draft', 'published', 'closed', 'archived'])->default('draft');
            $table->date('application_deadline')->nullable();
            $table->foreignId('pipeline_template_id')->nullable()->constrained();
            $table->json('screening_questions')->nullable();
            $table->boolean('requires_cv')->default(true);
            $table->boolean('requires_video')->default(false);
            $table->text('video_prompt')->nullable();
            $table->foreignId('hiring_manager_id')->nullable()->constrained('users');
            $table->integer('applications_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
