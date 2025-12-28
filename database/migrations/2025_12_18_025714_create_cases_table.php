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
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('filing_date');
            $table->date('next_hearing_date')->nullable();
            $table->date('previous_date')->nullable();
            $table->date('decision_date')->nullable();
            $table->string('status');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('lawyer_id')->constrained()->onDelete('cascade');
            $table->foreignId('court_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('court_id')->constrained()->onDelete('cascade');
            $table->foreignId('case_category_id')->constrained('case_categories')->onDelete('cascade');
            $table->foreignId('case_stage_id')->constrained('case_stages')->onDelete('cascade');
            $table->foreignId('case_remedy_id')->constrained('case_remedies')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
