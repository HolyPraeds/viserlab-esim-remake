<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update ORDER_PLACED email: thank you for purchase, 24h, check spam.
     */
    public function up(): void
    {
        $customerBody = '<div>
<p>Hello {{fullname}},</p>
<p>Thank you for your purchase.</p>
<p>Your order <strong>#{{order_number}}</strong> has been received and is being processed. We will send your eSIM to this email address within <strong>24 hours</strong>.</p>
<p>If you don\'t see our email right away, please check your spam folder.</p>
<p>If you have any questions, please contact our support.</p>
<p>Best regards,<br>{{site_name}}</p>
</div>';

        DB::table('notification_templates')->where('act', 'ORDER_PLACED')->update([
            'email_body' => $customerBody,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        //
    }
};
