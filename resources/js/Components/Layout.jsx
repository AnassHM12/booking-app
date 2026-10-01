import { Link, usePage } from '@inertiajs/react';

export default function Layout({ children }) {
    const { auth, flash } = usePage().props;
    const user = auth?.user;

    return (
        <div className="min-h-screen bg-stone-100 text-stone-800">
            <nav className="bg-stone-900 text-stone-200">
                <div className="mx-auto flex max-w-6xl flex-wrap items-center gap-5 px-5 py-3.5">
                    <Link href="/" className="text-lg font-semibold text-white">
                        Glow &amp; Co. <span className="font-normal text-amber-400">Bookings</span>
                    </Link>
                    {user ? (
                        <>
                            <Link href="/appointments" className="text-sm hover:text-white">My bookings</Link>
                            <Link href="/appointments/create" className="text-sm hover:text-white">Book now</Link>
                            <Link href="/admin/services" className="text-sm hover:text-white">Services</Link>
                            <Link href="/admin/staff" className="text-sm hover:text-white">Staff</Link>
                            <span className="ml-auto text-sm text-stone-400">{user.name} · {user.role}</span>
                            <Link href="/logout" method="post" as="button" className="text-sm hover:text-white">Log out</Link>
                        </>
                    ) : (
                        <div className="ml-auto flex gap-4">
                            <Link href="/login" className="text-sm hover:text-white">Log in</Link>
                            <Link href="/register" className="rounded-md bg-amber-500 px-3 py-1 text-sm font-medium text-stone-900 hover:bg-amber-400">Register</Link>
                        </div>
                    )}
                </div>
            </nav>

            <main className="mx-auto max-w-6xl px-5 py-8">
                {flash?.success && (
                    <div className="mb-5 rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800">
                        {flash.success}
                    </div>
                )}
                {children}
            </main>

            <footer className="pb-8 text-center text-xs text-stone-500">
                Glow &amp; Co. booking demo · Laravel + React + SQLite
            </footer>
        </div>
    );
}
