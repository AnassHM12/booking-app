import { Link, router } from '@inertiajs/react';
import Layout from '../Components/Layout';
import Checkout from '../Components/Checkout';

export default function BookingDetail({ appointment }) {
    const payMock = () => router.post(`/appointments/${appointment.id}/pay`);
    const cancel = () => { if (confirm('Cancel this booking?')) router.post(`/appointments/${appointment.id}/cancel`); };
    const unpaid = appointment.payment_status !== 'paid' && appointment.status !== 'cancelled';
    const amount = (appointment.amount_cents / 100).toFixed(2);

    return (
        <Layout>
            <div className="overflow-hidden rounded-2xl bg-white shadow ring-1 ring-stone-200">
                <div className="grid md:grid-cols-2">
                    <img src={appointment.img} alt="" className="h-56 w-full object-cover md:h-full" loading="lazy" />
                    <div className="p-6 md:p-8">
                        <p className="text-xs font-semibold uppercase tracking-widest text-amber-600">Booking #{appointment.id}</p>
                        <h2 className="mt-1 font-serif text-3xl">{appointment.service}</h2>
                        <p className="mt-1 text-stone-500">with {appointment.staff} · {appointment.customer}</p>
                        <p className="mt-3 text-lg">{appointment.when}</p>
                        <div className="mt-3 flex gap-2 text-xs">
                            <span className="rounded-full bg-stone-100 px-3 py-1 ring-1 ring-stone-200">{appointment.status}</span>
                            <span className="rounded-full bg-stone-100 px-3 py-1 ring-1 ring-stone-200">{appointment.payment_status}</span>
                        </div>
                        <div className="mt-5 border-t border-stone-100 pt-4">
                            <p className="text-sm font-medium text-stone-500">Payments</p>
                            {appointment.card && (
                                <p className="mt-2 rounded-lg bg-stone-900 px-3 py-2 text-sm text-white">
                                    {appointment.card.brand} •••• {appointment.card.last4} <span className="text-stone-400">exp {appointment.card.exp}</span>
                                </p>
                            )}
                            {appointment.payments.map((p) => (
                                <div key={p.id} className="mt-2 flex justify-between rounded-lg bg-stone-50 px-3 py-2 text-sm">
                                    <span>{p.provider} · {p.reference}</span>
                                    <span className="font-medium">${(p.amount_cents / 100).toFixed(2)} · {p.status}</span>
                                </div>
                            ))}
                        </div>

                        {unpaid && appointment.provider === 'stripe' && appointment.client_secret && (
                            <Checkout
                                stripeKey={appointment.stripe_key}
                                clientSecret={appointment.client_secret}
                                appointmentId={appointment.id}
                                amount={amount}
                            />
                        )}
                        {unpaid && appointment.provider !== 'stripe' && (
                            <button onClick={payMock} className="mt-4 w-full rounded-lg bg-amber-500 py-2.5 text-sm font-semibold text-stone-900 hover:bg-amber-400">
                                Pay ${amount} (test mode — no keys set)
                            </button>
                        )}

                        <div className="mt-4 flex gap-3">
                            {appointment.status !== 'cancelled' && (
                                <button onClick={cancel} className="rounded-lg border border-red-200 px-5 py-2.5 text-sm text-red-700 hover:bg-red-50">
                                    Cancel
                                </button>
                            )}
                            <Link href="/appointments" className="rounded-lg px-4 py-2.5 text-sm text-stone-500 hover:underline">Back</Link>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
