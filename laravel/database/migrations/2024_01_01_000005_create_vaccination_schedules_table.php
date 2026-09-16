<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaccination_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('vaccine_id')->constrained('vaccines')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('dose_number');
            $table->string('target_age', 100);
            $table->date('due_date');
            $table->enum('status', ['due', 'completed', 'overdue', 'skipped'])->default('due');
            $table->timestamps();

            $table->index('due_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaccination_schedules');
    }
};
