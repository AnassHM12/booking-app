import { useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import Layout from '../Components/Layout';

const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({ email: '', password: '' });
    const [emailError, setEmailError] = useState(null);

    const onEmailChange = (e) => {
        setData('email', e.target.value);
        if (emailError) setEmailError(null);
    };

    const submit = (e) => {
        e.preventDefault();
        if (!data.email.trim()) {
            setEmailError('Invalid email.');
            return;
        }
        if (!EMAIL_RE.test(data.email.trim())) {
            setEmailError('Invalid email.');
            return;
        }
        post('/login');
    };

    return (
        <Layout>
            <div className="mx-auto max-w-md overflow-hidden rounded-2xl bg-white shadow ring-1 ring-stone-200">
                <img src="https://images.unsplash.com/photo-1522337660859-02fbefca4702?auto=format&fit=crop&w=900&q=60" alt="Salon chairs" className="h-40 w-full object-cover" loading="lazy" />
                <form onSubmit={submit} noValidate className="p-6">
                    <h2 className="font-serif text-2xl">Welcome back</h2>
                    <p className="mt-1 text-sm text-stone-500">Log in to manage your bookings.</p>
                    <label className="mt-4 block text-sm text-stone-500">Email</label>
                    <input type="email" value={data.email} onChange={onEmailChange}
                        className={`mt-1 w-full rounded-md border px-3 py-2 ${emailError || errors.email ? 'border-red-400' : 'border-stone-300'}`} />
                    {(emailError || errors.email) && <p className="mt-1 text-sm text-red-600">{emailError || errors.email}</p>}
                    <label className="mt-3 block text-sm text-stone-500">Password</label>
                    <input type="password" value={data.password} onChange={(e) => setData('password', e.target.value)}
                        className="mt-1 w-full rounded-md border border-stone-300 px-3 py-2" />
                    {errors.password && <p className="mt-1 text-sm text-red-600">{errors.password}</p>}
                    <button disabled={processing} className="mt-5 w-full rounded-lg bg-stone-900 py-2.5 text-sm font-medium text-white hover:bg-stone-700">
                        Log in
                    </button>
                    <p className="mt-3 text-center text-sm text-stone-500">
                        No account? <Link href="/register" className="underline">Register</Link>
                    </p>
                    <p className="mt-2 text-center text-xs text-stone-400">Demo: customer@example.com / password</p>
                </form>
            </div>
        </Layout>
    );
}
