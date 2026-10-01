<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentMail;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sig = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = $secret
                ? Webhook::constructEvent($payload, $sig, $secret)
                : json_decode($payload);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Bad signature'], 400);
        }

        if (($event->type ?? null) === 'payment_intent.succeeded') {
            $intent = $event->data->object;
            $payment = Payment::where('reference', $intent->id)->first();
            // Idempotent: skip if already handled.
            if ($payment && $payment->status !== 'succeeded') {
                $payment->update(['status' => 'succeeded']);
                $a = $payment->appointment;
                $a->update(['payment_status' => 'paid', 'status' => 'confirmed']);
                Mail::to($a->customer->email)->queue(new AppointmentMail($a->fresh(), 'confirmed'));
            }
        } else {
            Log::info('Stripe webhook ignored', ['type' => $event->type ?? 'unknown']);
        }

        return response()->json(['received' => true]);
    }
}
