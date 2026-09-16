<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaccine_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('vaccine_id')->constrained('vaccines')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('available')->default(0);
            $table->integer('capacity')->default(0);
            $table->string('batch_number', 100)->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamp('last_updated')->useCurrent();
            $table->timestamps();

            $table->unique(['hospital_id', 'vaccine_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaccine_inventory');
    }
};
