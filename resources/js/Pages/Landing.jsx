import { Link } from '@inertiajs/react';
import Layout from '../Components/Layout';

const HERO = 'https://images.unsplash.com/photo-1580618672591-eb180b1a973f?auto=format&fit=crop&w=1600&q=70';
const SHOTS = [
    { img: 'https://images.unsplash.com/photo-1585747860715-2ba37e788b70?auto=format&fit=crop&w=800&q=60', title: 'Haircut & styling', text: '30-minute chair sessions with senior stylists.' },
    { img: 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=800&q=60', title: 'Spa & massage', text: 'Unwind with hour-long treatments in quiet rooms.' },
    { img: 'https://images.unsplash.com/photo-1605497788044-5a32c7078486?auto=format&fit=crop&w=800&q=60', title: 'Consultations', text: 'Talk through what you need before you commit.' },
];

export default function Landing({ services }) {
    return (
        <Layout>
            <div className="overflow-hidden rounded-2xl bg-stone-900 text-white shadow">
                <div className="grid md:grid-cols-2">
                    <div className="p-8 md:p-12">
                        <p className="text-xs font-semibold uppercase tracking-widest text-amber-400">Salon &amp; spa bookings</p>
                        <h1 className="mt-3 font-serif text-4xl leading-tight">Book a chair, <br />skip the phone call.</h1>
                        <p className="mt-4 max-w-md text-stone-300">
                            Pick a service, choose your stylist, and pay online. We hold your slot the second you confirm. No double bookings, no waiting around.
                        </p>
                        <div className="mt-6 flex gap-3">
                            <Link href="/register" className="rounded-lg bg-amber-500 px-5 py-2.5 text-sm font-semibold text-stone-900 hover:bg-amber-400">
                                Book an appointment
                            </Link>
                            <Link href="/login" className="rounded-lg border border-stone-600 px-5 py-2.5 text-sm text-stone-200 hover:border-stone-400">
                                Log in
                            </Link>
                        </div>
                        <div className="mt-8 flex gap-6 text-sm text-stone-400">
                            <span><strong className="text-white">2k+</strong> happy clients</span>
                            <span><strong className="text-white">4.9</strong> average rating</span>
                            <span><strong className="text-white">Same-day</strong> slots</span>
                        </div>
                    </div>
                    <div className="min-h-64 bg-stone-800">
                        <img src={HERO} alt="Salon interior" className="h-full w-full object-cover" loading="lazy" />
                    </div>
                </div>
            </div>

            <div className="mt-10 grid gap-5 md:grid-cols-3">
                {SHOTS.map((s) => (
                    <div key={s.title} className="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-stone-200">
                        <img src={s.img} alt={s.title} className="h-44 w-full object-cover" loading="lazy" />
                        <div className="p-5">
                            <h3 className="font-serif text-lg">{s.title}</h3>
                            <p className="mt-1 text-sm text-stone-500">{s.text}</p>
                        </div>
                    </div>
                ))}
            </div>

            {services?.length > 0 && (
                <div className="mt-10 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-stone-200 md:p-8">
                    <h2 className="font-serif text-2xl">Services &amp; prices</h2>
                    <div className="mt-4 divide-y divide-stone-100">
                        {services.map((s) => (
                            <div key={s.id} className="flex items-center justify-between py-3">
                                <div>
                                    <p className="font-medium">{s.name}</p>
                                    <p className="text-sm text-stone-500">{s.duration_minutes} min</p>
                                </div>
                                <div className="flex items-center gap-4">
                                    <span className="font-serif text-lg">${(s.price_cents / 100).toFixed(2)}</span>
                                    <Link href="/register" className="rounded-md bg-stone-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-stone-700">Book</Link>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            )}
        </Layout>
    );
}
