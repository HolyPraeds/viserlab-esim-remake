<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update PAYMENT_COMPLETED email: QR as link ({{qr_code_url}}), no attachment; {{fullname}} in shortCodes.
     */
    public function up(): void
    {
        $body = '<div>
<p>Hello {{fullname}},</p>
<p>Your payment for order <strong>#{{order_number}}</strong> (plan: <strong>{{plan}}</strong>, amount: <strong>{{amount}}</strong>) has been completed successfully.</p>
<p><strong>Your eSIM details:</strong></p>
<ul>
<li><strong>Serial number:</strong> {{serial_number}}</li>
<li><strong>Phone number:</strong> {{phone_number}}</li>
<li><strong>Valid until:</strong> {{expiry_date}}</li>
</ul>
<p><strong>QR code:</strong> {{qr_code_link}}</p>
<p><a href="{{dashboard_url}}" style="display:inline-block;padding:10px 20px;background:#007bff;color:#fff;text-decoration:none;border-radius:5px;">Open My eSIMs</a></p>
<p>Transaction reference: <strong>{{trx}}</strong></p>
<p>Thank you for your purchase.<br>Best regards,<br>{{site_name}}</p>
</div>';

        DB::table('notification_templates')->where('act', 'PAYMENT_COMPLETED')->update([
            'email_body' => $body,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        //
    }
};
