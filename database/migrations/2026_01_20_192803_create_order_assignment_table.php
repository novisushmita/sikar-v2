<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_assignment', function (Blueprint $table) {
            $table->id('assign_id');
            $table->foreignId('order_id')->constrained(table: 'order', column: 'order_id')->cascadeOnDelete();
            $table->foreignId('sopir_id')->constrained(table: 'sopir', column: 'sopir_id')->cascadeOnDelete();
            $table->string('mobil_id', 20);
            $table->foreign('mobil_id')->references('mobil_id')->on('mobil')->cascadeOnDelete();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_assignment');
    }
};