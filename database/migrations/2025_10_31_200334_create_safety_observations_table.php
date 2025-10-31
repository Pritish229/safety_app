<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('safety_observations', function (Blueprint $table) {
            $table->id();

            // 🔹 Foreign key linking to users table
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // 🔹 Observation fields
            $table->text('observation')->nullable();
            $table->string('location')->nullable();

            // 🔹 ENUM Security Level (1–5)
            $table->enum('security_level', [
                '1 - Low',
                '2 - Moderate',
                '3 - Significant',
                '4 - High',
                '5 - Critical',
            ])->nullable();

            $table->string('responsible_person')->nullable();
            $table->text('recommended_action')->nullable();

            // 🔹 Optional photo path
            $table->string('photo_path')->nullable();

            // 🔹 Status (optional workflow)
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'closed'])
                  ->default('submitted');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('safety_observations');
    }
};
