<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update ORDER_PLACED and ORDER_PLACED_ADMIN email body text (English, Gmail-friendly).
     */
    public function up(): void
    {
        $customerBody = '<div>
<p>Hello {{fullname}},</p>
<p>Thank you for your order.</p>
<p>Your order <strong>#{{order_number}}</strong> has been received and is being processed.</p>
<p>We will send your eSIM to this email address within <strong>24 hours</strong>. If you have any questions, please contact our support.</p>
<p>Best regards,<br>{{site_name}}</p>
</div>';

        $adminBody = '<div>
<p>A new order has been placed.</p>
<p><strong>Customer:</strong> {{customer_name}}</p>
<p><strong>Email:</strong> {{customer_email}}</p>
<p><strong>Order number:</strong> #{{order_number}}</p>
<p>Please process this order and send the eSIM to the customer within 24 hours.</p>
</div>';

        DB::table('notification_templates')->where('act', 'ORDER_PLACED')->update([
            'email_body' => $customerBody,
            'updated_at' => now(),
        ]);

        DB::table('notification_templates')->where('act', 'ORDER_PLACED_ADMIN')->update([
            'email_body' => $adminBody,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Revert to previous text; optional, leave empty if not needed
    }
};
