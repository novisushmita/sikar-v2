<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order', function (Blueprint $table) {
            $table->id('order_id');
            $table->foreignId('pengguna_id')->constrained(table: 'pengguna', column: 'pengguna_id')->cascadeOnDelete();
            $table->string('tempat_penjemputan');
            $table->string('tempat_tujuan');
            $table->timestamp('waktu_penjemputan');
            $table->string('keterangan');
            $table->enum('status', ['pending', 'assigned', 'on-process', 'confirmed', 'completed', 'canceled', 'rejected']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order');
    }
};
