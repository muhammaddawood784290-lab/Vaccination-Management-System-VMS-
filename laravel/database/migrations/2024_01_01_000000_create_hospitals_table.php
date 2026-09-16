<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->string('email')->unique();
            $table->string('phone', 20);
            $table->string('address', 500);
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('contact_person');
            $table->string('designation', 100)->nullable();
            $table->integer('total_beds')->default(0);
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending');
            $table->decimal('rating', 2, 1)->nullable();
            $table->timestamps();

            $table->index('city');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospitals');
    }
};
