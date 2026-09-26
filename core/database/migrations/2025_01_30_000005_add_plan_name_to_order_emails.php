<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add plan name to ORDER_PLACED and ORDER_PLACED_ADMIN email templates.
     */
    public function up(): void
    {
        $customerBody = '<div>
<p>Hello {{fullname}},</p>
<p>Thank you for your purchase.</p>
<p>Your order <strong>#{{order_number}}</strong> for <strong>{{plan_name}}</strong> has been received and is being processed. We will send your eSIM to this email address within <strong>24 hours</strong>.</p>
<p>If you don\'t see our email right away, please check your spam folder.</p>
<p>If you have any questions, please contact our support.</p>
<p>Best regards,<br>{{site_name}}</p>
</div>';

        $adminBody = '<div>
<p>A new order has been placed.</p>
<p><strong>Customer:</strong> {{customer_name}}</p>
<p><strong>Email:</strong> {{customer_email}}</p>
<p><strong>Plan:</strong> {{plan_name}}</p>
<p><strong>Order number:</strong> #{{order_number}}</p>
<p>Please process this order and send the eSIM to the customer within 24 hours.</p>
</div>';

        DB::table('notification_templates')->where('act', 'ORDER_PLACED')->update([
            'email_body' => $customerBody,
            'shortcodes' => json_encode(['order_number' => 'Order number', 'plan_name' => 'Plan name']),
            'updated_at' => now(),
        ]);

        DB::table('notification_templates')->where('act', 'ORDER_PLACED_ADMIN')->update([
            'email_body' => $adminBody,
            'shortcodes' => json_encode([
                'order_number' => 'Order number',
                'customer_name' => 'Customer full name',
                'customer_email' => 'Customer email',
                'plan_name' => 'Plan name',
            ]),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        //
    }
};
