<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();
            $table->string('case_title');
            
            // First party details
            $table->string('first_party_name');
            $table->string('first_party_contact')->nullable();
            $table->string('first_party_cnic')->nullable();
            $table->text('first_party_address')->nullable();

            // Second party details (optional)
            $table->string('second_party_name')->nullable();
            $table->string('second_party_contact')->nullable();
            $table->string('second_party_cnic')->nullable();
            $table->text('second_party_address')->nullable();

            // Case details
            $table->enum('case_type', ['criminal', 'civil', 'family', 'commercial', 'constitutional', 'corporate']);
            $table->string('court_name');
            $table->enum('case_status', ['open', 'pending', 'closed', 'hearing'])->default('open');
            $table->date('filing_date');
            $table->date('hearing_date')->nullable();
            $table->text('case_notes')->nullable();

            // Conditional fields (stored as JSON)
            $table->json('case_details')->nullable();

            // Document upload (store as JSON array)
            $table->json('documents')->nullable();

            // Foreign key to user
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};