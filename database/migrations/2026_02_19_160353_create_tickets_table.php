<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ticket_type_id')->constrained()->cascadeOnDelete();
            $table->string('reference_number')->unique();
            $table->string('external_reference')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('draft');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->foreignId('filed_with_contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->date('filed_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('closed_date')->nullable();
            $table->json('custom_fields')->nullable();
            $table->foreignId('parent_ticket_id')->nullable()->constrained('tickets')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
