<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Esim;
use App\Models\Order;
use App\Support\BrandContext;
use Illuminate\Support\Facades\Log;

class OrderEmailService
{
    public function checkoutDeposit(Order $order): ?Deposit
    {
        if ($order->relationLoaded('deposits')) {
            return $order->deposits->sortByDesc('id')->first();
        }

        return Deposit::where('order_id', $order->id)->latest('id')->first();
    }

    public function checkoutEmail(Order $order): ?string
    {
        $email = trim((string) ($this->checkoutDeposit($order)?->detail?->guest_email ?? ''));

        return $email !== '' ? $email : null;
    }

    public function checkoutName(Order $order): string
    {
        $detail = $this->checkoutDeposit($order)?->detail;
        $name = trim((string) (($detail?->guest_first_name ?? '') . ' ' . ($detail?->guest_last_name ?? '')));
        if ($name !== '') {
            return $name;
        }

        return $order->user?->fullname ?: 'Customer';
    }

    /**
     * Unique recipients: account email, checkout form email, optional extra address.
     *
     * @return list<object>
     */
    public function recipients(Order $order, ?string $extraEmail = null): array
    {
        $order->loadMissing('user');
        $map = [];

        $add = function (?string $email, $recipient) use (&$map) {
            $email = strtolower(trim((string) $email));
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || isset($map[$email])) {
                return;
            }
            $map[$email] = $recipient;
        };

        $user = $order->user;
        if ($user && $user->email) {
            $add($user->email, $user);
        }

        $checkoutEmail = $this->checkoutEmail($order);
        if ($checkoutEmail) {
            $guest = new \stdClass();
            $guest->email = $checkoutEmail;
            $guest->fullname = $this->checkoutName($order);
            $guest->username = 'guest';
            $add($checkoutEmail, $guest);
        }

        $extra = trim((string) $extraEmail);
        if ($extra !== '') {
            $other = new \stdClass();
            $other->email = $extra;
            $other->fullname = $this->checkoutName($order);
            $other->username = 'customer';
            $add($extra, $other);
        }

        return array_values($map);
    }

    public function sendPaymentCompleted(Order $order, ?string $extraEmail = null, ?string $trx = null): array
    {
        $order->loadMissing(['user', 'orderItem.plan']);
        $esim = Esim::where('order_item_id', $order->orderItem?->id)->latest('id')->first()
            ?: $order->orderItem?->esim;

        if (!$esim) {
            return [
                'sent' => [],
                'failed' => [],
                'error' => 'No eSIM found for this order. Cannot send the purchase email.',
            ];
        }

        $recipients = $this->recipients($order, $extraEmail);
        if (!$recipients) {
            return [
                'sent' => [],
                'failed' => [],
                'error' => 'No email address found for this order.',
            ];
        }

        return BrandContext::using($order->brand, function () use ($order, $esim, $recipients, $trx) {
        $dashboardUrl = route('user.esim.active');
        $expiryFormatted = $esim->expiry_date ? showDateTime($esim->expiry_date, 'd M Y, h:i A') : '—';
        $qrCodeUrl = (str_starts_with($esim->qr_code ?? '', 'http')) ? stripPngFromUrl($esim->qr_code) : '';
        $qrCodeLink = $qrCodeUrl
            ? '<a href="' . e($qrCodeUrl) . '" target="_blank" rel="noopener">Open your eSIM details and QR code</a>'
            : 'View your eSIM and QR code in your dashboard.';
        $paymentCurrency = strtoupper((string) ($order->payment_currency ?? 'EUR'));

        $sent = [];
        $failed = [];

        foreach ($recipients as $recipient) {
            try {
                notify($recipient, 'PAYMENT_COMPLETED', [
                    'fullname'      => $recipient->fullname ?? $recipient->username ?? 'Customer',
                    'order_number'  => $order->order_number,
                    'plan'          => $order->orderItem?->plan?->name ?? '—',
                    'amount'        => showAmount($order->total_amount, currencyFormat: false) . ' ' . $paymentCurrency,
                    'trx'           => $trx ?: $order->order_number,
                    'serial_number' => $esim->serial_number ?? '—',
                    'phone_number'  => $esim->phone_number ?? '—',
                    'expiry_date'   => $expiryFormatted,
                    'qr_code_url'   => $qrCodeUrl,
                    'qr_code_link'  => $qrCodeLink,
                    'dashboard_url' => $dashboardUrl,
                ], ['email'], isset($recipient->id) && $recipient->id);
                $sent[] = $recipient->email;
            } catch (\Throwable $e) {
                $failed[] = $recipient->email;
                Log::warning('PAYMENT_COMPLETED email failed', [
                    'order_id' => $order->id,
                    'email'    => $recipient->email,
                    'error'    => $e->getMessage(),
                ]);
            }
        }

        return ['sent' => $sent, 'failed' => $failed, 'error' => null];
        });
    }

    public function sendOrderPlaced(Order $order, ?string $extraEmail = null): array
    {
        $order->loadMissing(['user', 'orderItem.plan']);
        $planName = $order->orderItem?->plan?->name ? __($order->orderItem->plan->name) : '—';
        $recipients = $this->recipients($order, $extraEmail);
        $adminEmail = gs('email_from', false);

        return BrandContext::using($order->brand, function () use ($order, $planName, $recipients, $adminEmail) {
        $sent = [];
        $failed = [];

        foreach ($recipients as $recipient) {
            try {
                notify($recipient, 'ORDER_PLACED', [
                    'order_number' => $order->order_number,
                    'plan_name'    => $planName,
                ], ['email'], isset($recipient->id) && $recipient->id);
                $sent[] = $recipient->email;
            } catch (\Throwable $e) {
                $failed[] = $recipient->email;
                Log::warning('ORDER_PLACED email failed', [
                    'order_id' => $order->id,
                    'email'    => $recipient->email,
                    'error'    => $e->getMessage(),
                ]);
            }
        }

        if ($adminEmail) {
            try {
                $adminRecipient = (object) [
                    'email'    => $adminEmail,
                    'fullname' => 'Admin',
                    'username' => 'admin',
                ];
                $customerEmails = $recipients
                    ? implode(', ', array_map(fn ($r) => $r->email, $recipients))
                    : '—';
                notify($adminRecipient, 'ORDER_PLACED_ADMIN', [
                    'order_number'   => $order->order_number,
                    'customer_name'  => $order->user?->fullname ?: $this->checkoutName($order),
                    'customer_email' => $customerEmails,
                    'plan_name'      => $planName,
                ], ['email'], false);
            } catch (\Throwable $e) {
                Log::warning('ORDER_PLACED_ADMIN email failed', [
                    'order_id' => $order->id,
                    'error'    => $e->getMessage(),
                ]);
            }
        }

        $error = (!$recipients && !$adminEmail) ? 'No email address found for this order.' : null;

        return ['sent' => $sent, 'failed' => $failed, 'error' => $error];
        });
    }
}
