<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('special_technical_trainings', function (Blueprint $table) {
            $table->id();

            // 🔹 Link to user who submitted the training
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // 🔹 Form fields
            $table->string('location')->nullable();
            $table->string('contractor_name')->nullable();
            $table->integer('number_of_persons')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->text('topics_name')->nullable();

            // 🔹 Optional photo upload
            $table->string('photo_path')->nullable();

            // 🔹 Status
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'closed'])
                ->default('submitted');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('special_technical_trainings');
    }
};
