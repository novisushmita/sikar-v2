<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sopir', function (Blueprint $table) {
            $table->id('sopir_id');
            $table->string('name', 150);
            $table->smallInteger('order_completed')->default(0);
            $table->tinyInteger('order_ongoing',false, true)->default(0);
            $table->tinyInteger('masuk_kerja', false, true)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sopir');
    }
};
