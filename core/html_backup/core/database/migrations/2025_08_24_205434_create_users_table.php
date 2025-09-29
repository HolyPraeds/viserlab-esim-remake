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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('firstname', 40)->nullable();
            $table->string('lastname', 40)->nullable();
            $table->string('username', 40)->nullable();
            $table->string('email', 40);
            $table->string('dial_code', 40)->nullable();
            $table->string('mobile', 40)->nullable();
            $table->unsignedInteger('ref_by')->default(0);
            $table->decimal('balance', 28, 8)->default(0);
            $table->string('password');
            $table->string('country_name')->nullable();
            $table->string('country_code', 40)->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->text('address')->nullable();
            $table->boolean('status')->default(true)->comment('0: banned, 1: active');
            $table->text('kyc_data')->nullable();
            $table->string('kyc_rejection_reason')->nullable();
            $table->boolean('kv')->default(false)->comment('0: KYC Unverified, 2: KYC pending, 1: KYC verified');
            $table->boolean('ev')->default(false)->comment('0: email unverified, 1: email verified');
            $table->boolean('sv')->default(false)->comment('0: mobile unverified, 1: mobile verified');
            $table->boolean('profile_complete')->default(false);
            $table->string('ver_code', 40)->nullable()->comment('stores verification code');
            $table->dateTime('ver_code_send_at')->nullable()->comment('verification send time');
            $table->boolean('ts')->default(false)->comment('0: 2fa off, 1: 2fa on');
            $table->boolean('tv')->default(true)->comment('0: 2fa unverified, 1: 2fa verified');
            $table->string('tsc')->nullable();
            $table->string('ban_reason')->nullable();
            $table->string('remember_token')->nullable();
            $table->string('provider', 40)->nullable();
            $table->string('provider_id')->nullable();
            $table->timestamps();

            $table->unique(['username', 'email'], 'username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
