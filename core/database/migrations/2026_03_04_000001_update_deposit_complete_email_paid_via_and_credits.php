<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update DEPOSIT_COMPLETE email template: clarify paid via (Bank card) and show Credits mapping.
     */
    public function up(): void
    {
        $body = '<div>
<p>Hello {{fullname}},</p>
<p>We\'re delighted to inform you that your deposit of <strong>{{method_amount}} {{method_currency}}</strong> via <strong>{{paid_via}}</strong> has been completed successfully.</p>
<p><strong>Deposit details:</strong></p>
<ul>
<li><strong>Deposit amount:</strong> {{deposit_amount}} {{deposit_currency}}</li>
<li><strong>Credits added:</strong> {{credits_added}} {{credits_currency}}</li>
<li><strong>Credits &amp; currency equivalence:</strong> {{credits_rate_line}}</li>
<li><strong>Transaction reference:</strong> {{trx}}</li>
<li><strong>Current balance:</strong> {{post_balance}}</li>
</ul>
<p>If you have any questions or need further assistance, our support team is here to help.</p>
<p>Best regards,<br>{{site_name}}</p>
</div>';

        DB::table('notification_templates')
            ->where('act', 'DEPOSIT_COMPLETE')
            ->update([
                'email_body' => $body,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Optional: keep the improved template on rollback
    }
};

