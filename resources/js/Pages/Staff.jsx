import { useForm } from '@inertiajs/react';
import Layout from '../Components/Layout';

export default function Staff({ staff, meta }) {
    const { data, setData, post, processing } = useForm({ name: '' });
    const submit = (e) => { e.preventDefault(); post('/admin/staff', { onSuccess: () => setData('name', '') }); };

    return (
        <Layout>
            <div className="rounded-xl bg-white p-6 shadow-sm ring-1 ring-stone-200">
                <h2 className="font-serif text-2xl">Staff</h2>
                <form onSubmit={submit} className="mt-4 flex items-end gap-3">
                    <div><label className="text-xs text-stone-500">Name</label>
                        <input value={data.name} onChange={(e) => setData('name', e.target.value)} className="block w-52 rounded-md border border-stone-300 px-3 py-2" /></div>
                    <button disabled={processing} className="rounded-lg bg-stone-900 px-4 py-2 text-sm text-white">Save</button>
                </form>
                <div className="mt-4 grid gap-3 sm:grid-cols-2">
                    {staff.map((m) => (
                        <div key={m.id} className="flex items-center gap-3 rounded-lg border border-stone-200 p-3">
                            <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-stone-200 text-xs font-semibold text-stone-700">
                                {m.name.split(' ').map((w) => w[0]).join('').slice(0, 2).toUpperCase()}
                            </span>
                            <span className="font-medium">{m.name}</span>
                        </div>
                    ))}
                </div>
                <p className="mt-3 text-xs text-stone-400">Page {meta.current_page} of {meta.last_page}</p>
            </div>
        </Layout>
    );
}
