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
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('plan_id')->default(0);
            $table->integer('order_id');
            $table->string('purchase_id')->default('0');
            $table->decimal('price', 28, 8)->default(0)->comment('user pay for plan');
            $table->decimal('paid_price', 28, 8)->default(0)->comment('admin pay to dataplan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
