<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('criteria_id')->constrained('criteria')->restrictOnDelete();
            $table->decimal('score', 5, 2)->default(0);
            $table->unsignedInteger('votes_count')->default(0);
            $table->timestamps();

            $table->unique(['listing_id', 'criteria_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_criteria');
    }
};
