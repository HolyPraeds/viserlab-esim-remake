<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Set a default global email template: logo area, {{message}}, signature.
     * Used for ALL emails (customer + admin). Edit in Admin → Notification → Global Template.
     */
    public function up(): void
    {
        // Wrapper for ALL emails (customer + admin). {{message}} = text from Notification Template.
        $template = <<<'HTML'
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
  <div style="text-align: center; padding: 20px 0;">
    <img src="#" alt="{{site_name}}" style="max-height: 60px; max-width: 200px;" />
  </div>
  <div style="padding: 20px; background: #f9f9f9; border-radius: 8px;">
    {{message}}
  </div>
  <div style="padding: 20px; text-align: center; font-size: 12px; color: #666;">
    <p style="margin: 0;">&copy; {{site_name}}. All rights reserved.</p>
  </div>
</div>
HTML;

        // Only set if current template is empty or very short
        $current = DB::table('general_settings')->value('email_template');
        if ($current === null || $current === '' || strlen($current) < 100) {
            DB::table('general_settings')->update(['email_template' => $template]);
        }
    }

    public function down(): void
    {
        //
    }
};
