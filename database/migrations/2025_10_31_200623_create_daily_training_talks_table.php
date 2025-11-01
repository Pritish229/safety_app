<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_training_talks', function (Blueprint $table) {
            $table->id();

            // 🔹 Link to the user who submitted the report
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // 🔹 Link to the related project
            $table->foreignId('project_id')
                ->nullable()
                ->constrained('projects')
                ->onDelete('set null');

            // 🔹 Form fields
            $table->string('location')->nullable();
            $table->string('contractor_name')->nullable();
            $table->integer('number_of_persons')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->text('topics_discussed')->nullable();

            // 🔹 Optional photo
            $table->string('photo_path')->nullable();

            // 🔹 Workflow status
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'closed'])
                  ->default('submitted');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_training_talks');
    }
};
