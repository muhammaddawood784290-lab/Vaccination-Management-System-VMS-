<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('parent_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('vaccine_id')->constrained('vaccines')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('dose', 50);
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->enum('status', ['pending', 'approved', 'confirmed', 'completed', 'cancelled', 'rejected', 'no_show'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('appointment_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
