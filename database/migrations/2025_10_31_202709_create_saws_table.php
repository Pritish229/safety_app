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
        Schema::create('saws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('observation')->nullable();
            $table->string('location')->nullable();
            $table->enum('security_level', [1, 2, 3, 4, 5])->nullable();
            $table->string('work_supervisor')->nullable();
            $table->string('swo_issued_for')->nullable();
            $table->text('recommended_action')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saws');
    }
};
