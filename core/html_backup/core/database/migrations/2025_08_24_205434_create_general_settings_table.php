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
        Schema::create('general_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('site_name', 40)->nullable();
            $table->string('cur_text', 40)->nullable()->comment('currency text');
            $table->string('cur_sym', 40)->nullable()->comment('currency symbol');
            $table->string('payment_cur_text', 40)->nullable()->comment('payment currency text');
            $table->string('email_from', 40)->nullable();
            $table->string('email_from_name')->nullable();
            $table->text('email_template')->nullable();
            $table->string('sms_template')->nullable();
            $table->string('sms_from')->nullable();
            $table->string('push_title')->nullable();
            $table->string('push_template')->nullable();
            $table->text('plan_api')->nullable();
            $table->string('currency_api_key')->nullable();
            $table->string('base_color', 40)->nullable();
            $table->string('secondary_color', 40)->nullable();
            $table->text('mail_config')->nullable()->comment('email configuration');
            $table->text('sms_config')->nullable();
            $table->text('firebase_config')->nullable();
            $table->text('global_shortcodes')->nullable();
            $table->boolean('kv')->default(false);
            $table->boolean('ev')->default(false)->comment('email verification, 0 - dont check, 1 - check');
            $table->boolean('en')->default(false)->comment('email notification, 0 - dont send, 1 - send');
            $table->boolean('sv')->default(false)->comment('mobile verication, 0 - dont check, 1 - check');
            $table->boolean('sn')->default(false)->comment('sms notification, 0 - dont send, 1 - send');
            $table->boolean('pn')->default(true);
            $table->boolean('force_ssl')->default(false);
            $table->boolean('in_app_payment')->default(true);
            $table->boolean('maintenance_mode')->default(false);
            $table->boolean('secure_password')->default(false);
            $table->boolean('agree')->default(false);
            $table->boolean('multi_language')->default(true);
            $table->boolean('registration')->default(false)->comment('0: Off	, 1: On');
            $table->decimal('referral_amount', 28, 8)->nullable();
            $table->string('active_template', 40)->nullable();
            $table->text('socialite_credentials')->nullable();
            $table->dateTime('last_cron')->nullable();
            $table->string('available_version', 40)->nullable();
            $table->boolean('system_customized')->default(false);
            $table->integer('paginate_number')->default(0);
            $table->boolean('currency_format')->default(false)->comment('1=>Both
2=>Text Only
3=>Symbol Only');
            $table->text('config_progress')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
