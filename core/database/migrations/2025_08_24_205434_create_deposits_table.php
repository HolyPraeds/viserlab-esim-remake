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
        Schema::create('deposits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('order_id')->default(0);
            $table->unsignedInteger('method_code')->default(0);
            $table->decimal('amount', 28, 8)->default(0);
            $table->string('method_currency', 40)->nullable();
            $table->decimal('charge', 28, 8)->default(0);
            $table->decimal('rate', 28, 8)->default(0);
            $table->decimal('final_amount', 28, 8)->default(0);
            $table->text('detail')->nullable();
            $table->string('btc_amount')->nullable();
            $table->string('btc_wallet')->nullable();
            $table->string('trx', 40)->nullable();
            $table->integer('payment_try')->default(0);
            $table->boolean('status')->default(false)->comment('1=>success, 2=>pending, 3=>cancel');
            $table->boolean('from_api')->default(false);
            $table->boolean('is_web')->default(false)->comment('This will be 1 if the request is from NextJs application');
            $table->string('admin_feedback')->nullable();
            $table->string('success_url')->nullable();
            $table->string('failed_url')->nullable();
            $table->integer('last_cron')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
