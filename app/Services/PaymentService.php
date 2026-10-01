<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Payment;
use Stripe\PaymentIntent;
use Stripe\Stripe;

/**
 * Creates and verifies payments.
 *
 * Card data NEVER touches this server: the browser sends it straight to
 * Stripe via Stripe.js Elements, and we only handle intent IDs. Amounts
 * always come from the Service price server-side, never from the client.
 */
class PaymentService
{
    public static function stripeConfigured(): bool
    {
        return filled(config('services.stripe.key')) && filled(config('services.stripe.secret'));
    }

    public static function provider(): string
    {
        return self::stripeConfigured() ? 'stripe' : 'mock';
    }

    /** Create a pending payment + (for Stripe) a PaymentIntent. Returns [Payment, ?clientSecret]. */
    public static function createFor(Appointment $a, int $amountCents): array
    {
        if (!self::stripeConfigured()) {
            $payment = Payment::create([
                'appointment_id' => $a->id,
                'provider' => 'mock',
                'amount_cents' => $amountCents,
                'status' => 'pending',
                'reference' => 'MOCK-' . $a->id . '-' . now()->timestamp,
            ]);
            return [$payment, null];
        }

        Stripe::setApiKey(config('services.stripe.secret'));
        $intent = PaymentIntent::create([
            'amount' => $amountCents,
            'currency' => config('services.stripe.currency', 'usd'),
            'metadata' => ['appointment_id' => (string) $a->id],
        ]);

        $payment = Payment::create([
            'appointment_id' => $a->id,
            'provider' => 'stripe',
            'amount_cents' => $amountCents,
            'status' => 'pending',
            'reference' => $intent->id,
        ]);

        return [$payment, $intent->client_secret];
    }

    public static function clientSecret(Payment $payment): ?string
    {
        if ($payment->provider !== 'stripe' || !self::stripeConfigured()) return null;
        Stripe::setApiKey(config('services.stripe.secret'));
        return PaymentIntent::retrieve($payment->reference)->client_secret;
    }

    /**
     * Safe-to-display card summary (brand + last4 only — never the full
     * number). Fetched live from Stripe; nothing card-related is stored locally.
     */
    public static function cardSummary(Payment $payment): ?array
    {
        if ($payment->provider !== 'stripe' || !self::stripeConfigured()) return null;
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $intent = PaymentIntent::retrieve($payment->reference);
            $pm = $intent->payment_method;
            if (is_string($pm)) {
                $pm = \Stripe\PaymentMethod::retrieve($pm);
            }
            if (!isset($pm->card)) return null;
            return [
                'brand' => ucfirst($pm->card->brand ?? 'Card'),
                'last4' => $pm->card->last4 ?? '••••',
                'exp' => ($pm->card->exp_month ?? '?') . '/' . ($pm->card->exp_year ?? '?'),
            ];
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Verify a PaymentIntent server-side and mark the appointment paid.
     * Returns true only when Stripe says succeeded AND amount matches.
     */
    public static function confirmStripe(Appointment $a, string $intentId): bool
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $intent = PaymentIntent::retrieve($intentId);

        if ($intent->status !== 'succeeded') return false;
        $expected = (int) $a->service->price_cents;
        if ((int) $intent->amount !== $expected) return false;
        if (($intent->metadata->appointment_id ?? null) !== (string) $a->id) return false;

        $a->payments()->where('reference', $intent->id)->update(['status' => 'succeeded']);
        $a->update(['payment_status' => 'paid', 'status' => 'confirmed']);
        return true;
    }
}
