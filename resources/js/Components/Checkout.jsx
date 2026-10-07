import { useState } from 'react';
import { router } from '@inertiajs/react';
import { loadStripe } from '@stripe/stripe-js';
import { Elements, CardElement, useStripe, useElements } from '@stripe/react-stripe-js';

function CardForm({ clientSecret, appointmentId, amount }) {
    const stripe = useStripe();
    const elements = useElements();
    const [error, setError] = useState(null);
    const [busy, setBusy] = useState(false);

    const submit = async (e) => {
        e.preventDefault();
        if (!stripe || !elements) return;
        setBusy(true);
        setError(null);

        // Card details go directly to Stripe. Our server never sees them.
        const { error: stripeError, paymentIntent } = await stripe.confirmCardPayment(clientSecret, {
            payment_method: { card: elements.getElement(CardElement) },
        });

        if (stripeError) {
            setError(stripeError.message);
            setBusy(false);
            return;
        }

        // We send only the intent ID; the server re-verifies with Stripe.
        router.post(`/appointments/${appointmentId}/confirm`, {
            payment_intent_id: paymentIntent.id,
        }, { onFinish: () => setBusy(false) });
    };

    return (
        <form onSubmit={submit} className="mt-4 rounded-xl border border-stone-200 bg-stone-50 p-4">
            <p className="text-sm font-medium">Pay ${amount} by card</p>
<p className="mb-3 text-xs text-stone-500">Secured by Stripe. Card details never touch our server. Use test card 4242 4242 4242 4242.</p>
            <div className="rounded-lg border border-stone-300 bg-white px-3 py-2.5">
                <CardElement options={{ style: { base: { fontSize: '15px' } } }} />
            </div>
            {error && <p className="mt-2 text-sm text-red-600">{error}</p>}
            <button disabled={!stripe || busy} className="mt-3 w-full rounded-lg bg-amber-500 py-2.5 text-sm font-semibold text-stone-900 hover:bg-amber-400 disabled:opacity-50">
                {busy ? 'Processing…' : `Pay $${amount}`}
            </button>
        </form>
    );
}

export default function Checkout({ stripeKey, clientSecret, appointmentId, amount }) {
    const [promise] = useState(() => loadStripe(stripeKey));
    return (
        <Elements stripe={promise}>
            <CardForm clientSecret={clientSecret} appointmentId={appointmentId} amount={amount} />
        </Elements>
    );
}
