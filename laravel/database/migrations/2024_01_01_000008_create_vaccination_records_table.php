<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaccination_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('vaccine_id')->constrained('vaccines')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete()->cascadeOnUpdate();
            $table->integer('dose_number');
            $table->string('batch_number', 100)->nullable();
            $table->string('administered_by')->nullable();
            $table->timestamp('administered_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('administered_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaccination_records');
    }
};
