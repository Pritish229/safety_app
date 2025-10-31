<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('near_miss_reports', function (Blueprint $table) {
            $table->id();

            // 🔹 Foreign key: the user who submitted the report
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // 🔹 Report details
            $table->dateTime('date_time')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('person_involved')->nullable();
            $table->string('contractor_name')->nullable();
            $table->string('location_in_charge')->nullable();
            $table->text('worst_case_outcome')->nullable();
            $table->text('action_taken')->nullable();

            // 🔹 Optional photo
            $table->string('photo_path')->nullable();

            // 🔹 Status tracking
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'closed'])
                ->default('submitted');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('near_miss_reports');
    }
};
