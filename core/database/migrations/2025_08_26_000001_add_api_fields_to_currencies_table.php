<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            if (!Schema::hasColumn('currencies', 'api_currency')) {
                $table->string('api_currency')->nullable()->after('currency_symbol');
            }
            if (!Schema::hasColumn('currencies', 'conversion_rate')) {
                $table->decimal('conversion_rate', 18, 8)->nullable()->after('api_currency');
            }
        });
    }

    public function down(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            if (Schema::hasColumn('currencies', 'conversion_rate')) {
                $table->dropColumn('conversion_rate');
            }
            if (Schema::hasColumn('currencies', 'api_currency')) {
                $table->dropColumn('api_currency');
            }
        });
    }
};






