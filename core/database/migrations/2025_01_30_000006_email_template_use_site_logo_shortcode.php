<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update global email template to use {{site_logo}} so the logo loads in emails (full URL).
     */
    public function up(): void
    {
        $current = DB::table('general_settings')->value('email_template');
        if ($current === null) {
            return;
        }
        // Replace broken img src="#" with {{site_logo}} so logo loads (full URL from site)
        $new = str_replace('src="#"', 'src="{{site_logo}}"', $current);
        // If no src="#", try first img tag: set src to {{site_logo}}
        if ($new === $current && strpos($current, '<img') !== false) {
            $new = preg_replace('/<img(\s[^>]*?)src="[^"]*"/', '<img$1src="{{site_logo}}"', $current, 1);
        }
        DB::table('general_settings')->update(['email_template' => $new]);
    }

    public function down(): void
    {
        //
    }
};
