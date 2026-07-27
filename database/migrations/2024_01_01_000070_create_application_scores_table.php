<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('stage_id')->constrained('job_pipeline_stages');
            $table->tinyInteger('score');
            $table->text('notes')->nullable();
            $table->enum('recommendation', ['strong_yes', 'yes', 'maybe', 'no', 'strong_no'])->default('maybe');
            $table->timestamps();

            $table->unique(['application_id', 'user_id', 'stage_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_scores');
    }
};
