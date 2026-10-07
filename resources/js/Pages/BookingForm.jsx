import { useForm } from '@inertiajs/react';
import Layout from '../Components/Layout';

export default function BookingForm({ services, staff }) {
    const { data, setData, post, processing, errors } = useForm({ service_id: services[0]?.id ?? '', staff_id: staff[0]?.id ?? '', starts_at: '' });
    const submit = (e) => { e.preventDefault(); post('/appointments'); };
    const field = 'mt-1 w-full rounded-md border border-stone-300 px-3 py-2 bg-white';

    return (
        <Layout>
            <div className="grid gap-6 md:grid-cols-5">
                <div className="overflow-hidden rounded-2xl shadow ring-1 ring-stone-200 md:col-span-2">
                    <img src="https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=800&q=60" alt="Stylist at work" className="h-full min-h-72 w-full object-cover" loading="lazy" />
                </div>
                <form onSubmit={submit} className="rounded-2xl bg-white p-6 shadow ring-1 ring-stone-200 md:col-span-3">
                    <h2 className="font-serif text-2xl">New booking</h2>
                    <p className="mt-1 text-sm text-stone-500">We hold the slot while you check out.</p>
                    <label className="mt-4 block text-sm text-stone-500">Service</label>
                    <select value={data.service_id} onChange={(e) => setData('service_id', e.target.value)} className={field}>
                        {services.map((s) => (
                            <option key={s.id} value={s.id}>{s.name} (${(s.price_cents / 100).toFixed(2)} / {s.duration_minutes} min)</option>
                        ))}
                    </select>
                    <label className="mt-3 block text-sm text-stone-500">Stylist</label>
                    <div className="mt-1 grid grid-cols-2 gap-2">
                        {staff.map((m) => (
                            <button type="button" key={m.id} onClick={() => setData('staff_id', m.id)}
                                className={`flex items-center gap-2 rounded-lg border p-2 text-left ${String(data.staff_id) === String(m.id) ? 'border-amber-500 bg-amber-50' : 'border-stone-200 hover:border-stone-400'}`}>
                                <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-stone-200 text-xs font-semibold text-stone-700">
                                    {m.name.split(' ').map((w) => w[0]).join('').slice(0, 2).toUpperCase()}
                                </span>
                                <span className="text-sm font-medium">{m.name}</span>
                            </button>
                        ))}
                    </div>
                    <label className="mt-3 block text-sm text-stone-500">Date &amp; time</label>
                    <input type="datetime-local" value={data.starts_at} onChange={(e) => setData('starts_at', e.target.value)} className={field} />
                    {errors.starts_at && <p className="text-sm text-red-600">{errors.starts_at}</p>}
                    {errors.conflict && <p className="mt-2 rounded-md bg-red-50 px-3 py-2 text-sm text-red-700">{errors.conflict}</p>}
                    <button disabled={processing} className="mt-5 w-full rounded-lg bg-stone-900 py-2.5 text-sm font-medium text-white hover:bg-stone-700">
                        Continue to payment →
                    </button>
                </form>
            </div>
        </Layout>
    );
}
