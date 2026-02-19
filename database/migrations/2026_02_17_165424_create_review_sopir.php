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
        Schema::create('review_sopir', function (Blueprint $table) {
            $table->id('review_id');
            $table->date('tanggal');
            $table->integer('review');

            $table->unsignedBigInteger('pengguna_id');
            $table->unsignedBigInteger('sopir_id');
            $table->unsignedBigInteger('order_id');

            $table->timestamps();

            // Foreign key constraint
            $table->foreign('sopir_id')
                ->references('sopir_id')
                ->on('sopir')
                ->onDelete('cascade');

            $table->foreign('pengguna_id')
                ->references('pengguna_id')
                ->on('pengguna')
                ->onDelete('cascade');

            $table->foreign('order_id') 
                ->references('order_id')
                ->on('order') 
                ->onDelete('cascade');

            // Index
            $table->index(['sopir_id', 'tanggal']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_sopir');
    }
};
