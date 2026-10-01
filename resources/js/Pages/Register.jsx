import { Link, useForm } from '@inertiajs/react';
import Layout from '../Components/Layout';

export default function Register() {
    const { data, setData, post, processing, errors } = useForm({ name: '', email: '', password: '', password_confirmation: '' });
    const submit = (e) => { e.preventDefault(); post('/register'); };
    const field = 'mt-1 w-full rounded-md border border-stone-300 px-3 py-2';

    return (
        <Layout>
            <div className="mx-auto max-w-md rounded-2xl bg-white p-6 shadow ring-1 ring-stone-200">
                <h2 className="font-serif text-2xl">Create your account</h2>
                <p className="mt-1 text-sm text-stone-500">Book in under a minute.</p>
                <form onSubmit={submit} className="mt-4">
                    <label className="block text-sm text-stone-500">Name</label>
                    <input value={data.name} onChange={(e) => setData('name', e.target.value)} className={field} />
                    {errors.name && <p className="text-sm text-red-600">{errors.name}</p>}
                    <label className="mt-3 block text-sm text-stone-500">Email</label>
                    <input type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} className={field} />
                    {errors.email && <p className="text-sm text-red-600">{errors.email}</p>}
                    <label className="mt-3 block text-sm text-stone-500">Password</label>
                    <input type="password" value={data.password} onChange={(e) => setData('password', e.target.value)} className={field} />
                    {errors.password && <p className="text-sm text-red-600">{errors.password}</p>}
                    <label className="mt-3 block text-sm text-stone-500">Confirm password</label>
                    <input type="password" value={data.password_confirmation} onChange={(e) => setData('password_confirmation', e.target.value)} className={field} />
                    <button disabled={processing} className="mt-5 w-full rounded-lg bg-amber-500 py-2.5 text-sm font-semibold text-stone-900 hover:bg-amber-400">
                        Create account
                    </button>
                </form>
                <p className="mt-3 text-center text-sm text-stone-500">
                    Have an account? <Link href="/login" className="underline">Log in</Link>
                </p>
            </div>
        </Layout>
    );
}
