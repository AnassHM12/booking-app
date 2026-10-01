<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentMail;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Staff;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $q = Appointment::with(['service', 'staff', 'customer'])->orderBy('starts_at');
        if (!$user->isStaff()) $q->where('customer_id', $user->id);
        if ($request->filled('status')) $q->where('status', $request->status);
        $p = $q->paginate(10)->withQueryString();

        return Inertia::render('Dashboard', [
            'appointments' => collect($p->items())->map(fn ($a) => [
                'id' => $a->id,
                'service' => $a->service->name,
                'staff' => $a->staff->name,
                'when' => $a->starts_at->format('D d M, H:i') . '–' . $a->ends_at->format('H:i'),
                'status' => $a->status,
                'payment_status' => $a->payment_status,
            ]),
            'meta' => ['current_page' => $p->currentPage(), 'last_page' => $p->lastPage()],
            'filters' => ['status' => $request->status],
        ]);
    }

    public function create()
    {
        return Inertia::render('BookingForm', [
            'services' => Service::orderBy('name')->get(),
            'staff' => Staff::orderBy('name')->get()->map(fn ($s) => [
                'id' => $s->id, 'name' => $s->name,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
            'starts_at' => 'required|date|after:now',
        ]);

        $service = Service::findOrFail($data['service_id']);
        $start = Carbon::parse($data['starts_at']);
        $end = $start->copy()->addMinutes($service->duration_minutes);

        if (Appointment::staffConflict($data['staff_id'], $start->toDateTimeString(), $end->toDateTimeString())) {
            return back()->withErrors(['conflict' => 'That stylist is already booked then — try another time.'])->withInput();
        }

        $a = Appointment::create([
            'service_id' => $service->id,
            'staff_id' => $data['staff_id'],
            'customer_id' => $request->user()->id,
            'starts_at' => $start,
            'ends_at' => $end,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        try {
            PaymentService::createFor($a, $service->price_cents);
        } catch (\Exception $e) {
            return back()->withErrors(['payment' => 'Could not start payment: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('appointments.show', $a)->with('success', 'Slot held. Pay to confirm.');
    }

    public function show(Appointment $appointment)
    {
        Gate::authorize('view', $appointment);
        $appointment->load(['service', 'staff', 'customer', 'payments']);
        $stripePayment = $appointment->payments->firstWhere('provider', 'stripe');

        return Inertia::render('BookingDetail', [
            'appointment' => [
                'id' => $appointment->id,
                'service' => $appointment->service->name,
                'staff' => $appointment->staff->name,
                'customer' => $appointment->customer->name,
                'when' => $appointment->starts_at->format('l d M Y, H:i') . ' – ' . $appointment->ends_at->format('H:i'),
                'status' => $appointment->status,
                'payment_status' => $appointment->payment_status,
                'amount_cents' => $appointment->payments->sum('amount_cents'),
                'provider' => $stripePayment ? 'stripe' : 'mock',
                'stripe_key' => $stripePayment ? config('services.stripe.key') : null,
                'client_secret' => $stripePayment ? PaymentService::clientSecret($stripePayment) : null,
                'card' => $stripePayment ? PaymentService::cardSummary($stripePayment) : null,
                'img' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=900&q=60',
                'payments' => $appointment->payments->map(fn ($p) => $p->only('id', 'provider', 'amount_cents', 'status', 'reference')),
            ],
        ]);
    }

    /** Mock checkout only. Stripe bookings must go through confirm(). */
    public function pay(Appointment $appointment)
    {
        Gate::authorize('view', $appointment);
        if ($appointment->payment_status === 'paid') {
            return back()->with('success', 'Already paid.');
        }
        if ($appointment->payments()->where('provider', 'stripe')->exists()) {
            return back()->withErrors(['payment' => 'This booking uses card payment — use the card form below.']);
        }
        $payment = $appointment->payments()->where('status', 'pending')->latest()->first();
        if ($payment) $payment->update(['status' => 'succeeded']);
        $appointment->update(['payment_status' => 'paid', 'status' => 'confirmed']);
        Mail::to($appointment->customer->email)->queue(new AppointmentMail($appointment->fresh(), 'confirmed'));
        return back()->with('success', 'Payment taken (test). Confirmation email queued.');
    }

    /**
     * Stripe return path. The client sends ONLY the PaymentIntent ID —
     * card data went straight to Stripe via Elements. We re-fetch the
     * intent server-side and trust nothing from the browser.
     */
    public function confirm(Request $request, Appointment $appointment)
    {
        Gate::authorize('view', $appointment);
        $data = $request->validate(['payment_intent_id' => 'required|string|starts_with:pi_']);

        try {
            $ok = PaymentService::confirmStripe($appointment, $data['payment_intent_id']);
        } catch (\Exception $e) {
            return back()->withErrors(['payment' => 'Payment verification failed: ' . $e->getMessage()]);
        }

        if (!$ok) {
            return back()->withErrors(['payment' => 'Payment not confirmed by Stripe. No charge was applied to this booking.']);
        }

        Mail::to($appointment->customer->email)->queue(new AppointmentMail($appointment->fresh(), 'confirmed'));
        return back()->with('success', 'Card payment confirmed. Email queued.');
    }

    public function cancel(Appointment $appointment)
    {
        Gate::authorize('cancel', $appointment);
        $appointment->update(['status' => 'cancelled']);
        Mail::to($appointment->customer->email)->queue(new AppointmentMail($appointment->fresh(), 'cancelled'));
        return redirect()->route('appointments.index')->with('success', 'Booking cancelled. Email queued.');
    }
}
