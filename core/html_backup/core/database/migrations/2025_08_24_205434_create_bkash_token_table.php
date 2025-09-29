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
        Schema::create('bkash_token', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('sandbox_mode');
            $table->bigInteger('id_expiry')->default(0);
            $table->string('id_token', 2048);
            $table->bigInteger('refresh_expiry')->default(0);
            $table->string('refresh_token', 2048);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bkash_token');
    }
};
