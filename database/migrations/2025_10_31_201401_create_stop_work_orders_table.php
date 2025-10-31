<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stop_work_orders', function (Blueprint $table) {
            $table->id();

            // 🔹 Link to user who submitted the report
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // 🔹 Core fields
            $table->dateTime('date_time')->nullable();
            $table->text('observation')->nullable();
            $table->string('location')->nullable();

            // 🔹 Security level enum (1–5)
            $table->enum('security_level', ['1', '2', '3', '4', '5'])->nullable();

            $table->string('concerned_supervisor')->nullable();
            $table->string('swo_issued_for')->nullable();
            $table->text('recommended_action')->nullable();

            // 🔹 Optional photo
            $table->string('photo_path')->nullable();

            // 🔹 Status
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'closed'])
                ->default('submitted');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stop_work_orders');
    }
};
