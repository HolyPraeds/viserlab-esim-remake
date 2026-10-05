<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $slugs = DB::table('countries')
            ->select('slug')
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->groupBy('slug')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('slug');

        foreach ($slugs as $slug) {
            $rows = DB::table('countries')->where('slug', $slug)->orderBy('id')->get();
            $bestId = null;
            $bestCount = -1;

            foreach ($rows as $row) {
                $count = DB::table('country_plan')->where('country_id', $row->id)->count();
                if ($count > $bestCount || ($count === $bestCount && $row->id > $bestId)) {
                    $bestCount = $count;
                    $bestId = (int) $row->id;
                }
            }

            foreach ($rows as $row) {
                if ((int) $row->id === $bestId) {
                    continue;
                }

                $keptPlanIds = DB::table('country_plan')->where('country_id', $bestId)->pluck('plan_id');
                DB::table('country_plan')
                    ->where('country_id', $row->id)
                    ->when($keptPlanIds->isNotEmpty(), fn ($query) => $query->whereIn('plan_id', $keptPlanIds))
                    ->delete();
                DB::table('country_plan')
                    ->where('country_id', $row->id)
                    ->update(['country_id' => $bestId, 'updated_at' => now()]);

                $replacementSlug = $slug . '-' . strtolower((string) $row->code);
                if (DB::table('countries')->where('slug', $replacementSlug)->exists()) {
                    $replacementSlug .= '-' . $row->id;
                }

                DB::table('countries')->where('id', $row->id)->update([
                    'status' => 0,
                    'slug' => $replacementSlug,
                    'updated_at' => now(),
                ]);
            }
        }

        if (Schema::hasTable('countries')) {
            Schema::table('countries', function ($table) {
                $table->unique('slug');
            });
        }
    }

    public function down(): void
    {
        Schema::table('countries', function ($table) {
            $table->dropUnique(['slug']);
        });
    }
};
