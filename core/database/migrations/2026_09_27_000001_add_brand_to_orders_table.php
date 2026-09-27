<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('orders', 'brand')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('brand', 32)->default('travelsim')->after('order_number');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'brand')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('brand');
            });
        }
    }
};
