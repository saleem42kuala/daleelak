<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            $table->foreignId('criteria_id')->constrained('criteria')->restrictOnDelete();
            $table->boolean('value');
            $table->timestamps();

            $table->unique(['review_id', 'criteria_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_criteria');
    }
};
