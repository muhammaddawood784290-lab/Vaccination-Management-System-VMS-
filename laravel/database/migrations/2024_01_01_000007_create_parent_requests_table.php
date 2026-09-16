<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parent_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('type', ['vaccination_request', 'general_inquiry', 'complaint', 'feedback']);
            $table->string('subject');
            $table->text('details');
            $table->foreignId('hospital_id')->nullable()->constrained('hospitals')->nullOnDelete()->cascadeOnUpdate();
            $table->enum('status', ['pending', 'in_review', 'resolved', 'closed'])->default('pending');
            $table->text('admin_response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_requests');
    }
};
