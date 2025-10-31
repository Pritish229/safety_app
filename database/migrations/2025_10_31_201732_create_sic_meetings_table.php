<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sic_meetings', function (Blueprint $table) {
            $table->id();

            // 🔹 Link to user who created the record
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // 🔹 Fields
            $table->dateTime('date_time')->nullable();
            $table->text('discussed_points')->nullable();

            // 🔹 Optional photo
            $table->string('photo_path')->nullable();

            // 🔹 Status for tracking
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'closed'])
                ->default('submitted');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sic_meetings');
    }
};
