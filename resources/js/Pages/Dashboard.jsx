import { Link, router } from '@inertiajs/react';
import Layout from '../Components/Layout';

function pill(status) {
    if (status === 'paid' || status === 'confirmed') return 'bg-green-100 text-green-800 border-green-200';
    if (status === 'cancelled') return 'bg-stone-200 text-stone-600 border-stone-300';
    return 'bg-amber-100 text-amber-800 border-amber-200';
}

export default function Dashboard({ appointments, meta, filters }) {
    const go = (page) => router.get('/appointments', { ...filters, page }, { preserveState: true });

    const upcoming = appointments.filter((a) => a.status !== 'cancelled').length;
    const paid = appointments.filter((a) => a.payment_status === 'paid').length;

    return (
        <Layout>
            <div className="grid gap-4 md:grid-cols-3">
                <div className="rounded-xl bg-stone-900 p-5 text-white">
                    <p className="text-xs uppercase tracking-wider text-stone-400">Active bookings</p>
                    <p className="mt-1 font-serif text-3xl">{upcoming}</p>
                </div>
                <div className="rounded-xl bg-white p-5 shadow-sm ring-1 ring-stone-200">
                    <p className="text-xs uppercase tracking-wider text-stone-400">Paid</p>
                    <p className="mt-1 font-serif text-3xl">{paid}</p>
                </div>
                <div className="rounded-xl bg-amber-500 p-5 text-stone-900">
                    <p className="text-xs uppercase tracking-wider">Need a slot?</p>
                    <Link href="/appointments/create" className="mt-1 inline-block font-serif text-xl underline">Book now →</Link>
                </div>
            </div>

            <div className="mt-6 rounded-xl bg-white p-5 shadow-sm ring-1 ring-stone-200">
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <h2 className="font-serif text-xl">Bookings</h2>
                    <div className="flex gap-2 text-sm">
                        {['', 'pending', 'confirmed', 'cancelled'].map((s) => (
                            <button key={s || 'all'} onClick={() => router.get('/appointments', { status: s || undefined })}
                                className={`rounded-full border px-3 py-1 ${filters.status === s || (!filters.status && !s) ? 'bg-stone-900 text-white border-stone-900' : 'border-stone-300 hover:border-stone-500'}`}>
                                {s || 'All'}
                            </button>
                        ))}
                    </div>
                </div>
                <div className="mt-3 divide-y divide-stone-100">
                    {appointments.length === 0 && <p className="py-6 text-center text-sm text-stone-500">Nothing here yet — go book something.</p>}
                    {appointments.map((a) => (
                        <Link key={a.id} href={`/appointments/${a.id}`} className="flex items-center gap-4 py-3 hover:bg-stone-50">
                            <img src={a.staff_img} alt="" className="h-11 w-11 rounded-full object-cover" loading="lazy" />
                            <div className="min-w-0 flex-1">
                                <p className="truncate font-medium">{a.service} <span className="font-normal text-stone-400">with {a.staff}</span></p>
                                <p className="text-sm text-stone-500">{a.when}</p>
                            </div>
                            <span className={`rounded-full border px-2.5 py-0.5 text-xs ${pill(a.status)}`}>{a.status}</span>
                            <span className={`rounded-full border px-2.5 py-0.5 text-xs ${pill(a.payment_status)}`}>{a.payment_status}</span>
                        </Link>
                    ))}
                </div>
                {meta.last_page > 1 && (
                    <div className="mt-4 flex items-center justify-between text-sm">
                        <button disabled={meta.current_page <= 1} onClick={() => go(meta.current_page - 1)} className="rounded-md border border-stone-300 px-3 py-1 disabled:opacity-40">← Prev</button>
                        <span className="text-stone-500">Page {meta.current_page} of {meta.last_page}</span>
                        <button disabled={meta.current_page >= meta.last_page} onClick={() => go(meta.current_page + 1)} className="rounded-md border border-stone-300 px-3 py-1 disabled:opacity-40">Next →</button>
                    </div>
                )}
            </div>
        </Layout>
    );
}
