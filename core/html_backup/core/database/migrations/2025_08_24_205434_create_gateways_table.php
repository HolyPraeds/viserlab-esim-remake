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
        Schema::create('gateways', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('form_id')->default(0);
            $table->integer('code')->nullable()->unique('code');
            $table->string('name', 40)->nullable();
            $table->string('alias', 40)->default('NULL');
            $table->string('image')->nullable();
            $table->boolean('status')->default(true)->comment('1=>enable, 2=>disable');
            $table->text('gateway_parameters')->nullable();
            $table->text('supported_currencies')->nullable();
            $table->boolean('crypto')->default(false)->comment('0: fiat currency, 1: crypto currency');
            $table->text('extra')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gateways');
    }
};
