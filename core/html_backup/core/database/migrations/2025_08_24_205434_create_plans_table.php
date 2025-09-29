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
        Schema::create('plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('region_id')->nullable();
            $table->bigInteger('currency_id')->nullable();
            $table->string('slug')->default('0')->unique('slug');
            $table->string('name')->default('0');
            $table->integer('period')->default(0);
            $table->integer('capacity')->default(0);
            $table->string('capacity_unit')->default('0');
            $table->decimal('retail_price', 28, 8)->default(0);
            $table->string('price_currency', 40)->default('0');
            $table->decimal('prepaid_credit', 28, 8)->default(0);
            $table->string('prepaid_currency', 40)->default('0');
            $table->boolean('reloadable')->default(false);
            $table->boolean('phone_number')->default(false);
            $table->string('operator_name')->default('0');
            $table->string('operator_slug')->default('0');
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
