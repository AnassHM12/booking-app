import { useForm } from '@inertiajs/react';
import Layout from '../Components/Layout';

export default function Services({ services, meta }) {
    const { data, setData, post, processing } = useForm({ name: '', duration_minutes: 60, price_cents: 5000 });
    const submit = (e) => { e.preventDefault(); post('/admin/services', { onSuccess: () => setData({ name: '', duration_minutes: 60, price_cents: 5000 }) }); };

    return (
        <Layout>
            <div className="rounded-xl bg-white p-6 shadow-sm ring-1 ring-stone-200">
                <h2 className="font-serif text-2xl">Services</h2>
                <form onSubmit={submit} className="mt-4 flex flex-wrap items-end gap-3">
                    <div><label className="text-xs text-stone-500">Name</label>
                        <input value={data.name} onChange={(e) => setData('name', e.target.value)} className="block w-52 rounded-md border border-stone-300 px-3 py-2" /></div>
                    <div><label className="text-xs text-stone-500">Minutes</label>
                        <input type="number" min="15" value={data.duration_minutes} onChange={(e) => setData('duration_minutes', e.target.value)} className="block w-28 rounded-md border border-stone-300 px-3 py-2" /></div>
                    <div><label className="text-xs text-stone-500">Price (cents)</label>
                        <input type="number" min="0" value={data.price_cents} onChange={(e) => setData('price_cents', e.target.value)} className="block w-32 rounded-md border border-stone-300 px-3 py-2" /></div>
                    <button disabled={processing} className="rounded-lg bg-stone-900 px-4 py-2 text-sm text-white">Save</button>
                </form>
                <div className="mt-4 divide-y divide-stone-100">
                    {services.map((s) => (
                        <div key={s.id} className="flex items-center justify-between py-2.5">
                            <span>{s.name} <span className="text-sm text-stone-400">· {s.duration_minutes} min</span></span>
                            <span className="font-serif">${(s.price_cents / 100).toFixed(2)}</span>
                        </div>
                    ))}
                </div>
                <p className="mt-3 text-xs text-stone-400">Page {meta.current_page} of {meta.last_page}</p>
            </div>
        </Layout>
    );
}
