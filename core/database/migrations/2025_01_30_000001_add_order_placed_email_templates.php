<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add ORDER_PLACED (to customer) and ORDER_PLACED_ADMIN (to admin inbox) email templates.
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

        $adminBody = '<div>
<p>A new order has been placed.</p>
<p><strong>Customer:</strong> {{customer_name}}</p>
<p><strong>Email:</strong> {{customer_email}}</p>
<p><strong>Order number:</strong> #{{order_number}}</p>
<p>Please process this order and send the eSIM to the customer within 24 hours.</p>
</div>';

        $now = now()->format('Y-m-d H:i:s');

        // ORDER_PLACED — to customer: order received, eSIM within 24h
        DB::table('notification_templates')->updateOrInsert(
            ['act' => 'ORDER_PLACED'],
            [
                'name' => 'Order - Placed (Customer)',
                'subject' => 'Your order is being processed – eSIM within 24 hours',
                'push_title' => null,
                'email_body' => $customerBody,
                'sms_body' => null,
                'push_body' => null,
                'shortcodes' => json_encode(['order_number' => 'Order number']),
                'email_status' => 1,
                'email_sent_from_name' => null,
                'email_sent_from_address' => null,
                'sms_status' => 0,
                'sms_sent_from' => null,
                'push_status' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        // ORDER_PLACED_ADMIN — to admin inbox: new order, name, email
        DB::table('notification_templates')->updateOrInsert(
            ['act' => 'ORDER_PLACED_ADMIN'],
            [
                'name' => 'Order - Placed (Admin notification)',
                'subject' => 'New order #{{order_number}} – {{customer_name}}',
                'push_title' => null,
                'email_body' => $adminBody,
                'sms_body' => null,
                'push_body' => null,
                'shortcodes' => json_encode([
                    'order_number' => 'Order number',
                    'customer_name' => 'Customer full name',
                    'customer_email' => 'Customer email',
                ]),
                'email_status' => 1,
                'email_sent_from_name' => null,
                'email_sent_from_address' => null,
                'sms_status' => 0,
                'sms_sent_from' => null,
                'push_status' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        DB::table('notification_templates')->whereIn('act', ['ORDER_PLACED', 'ORDER_PLACED_ADMIN'])->delete();
    }
};
