# Booking App (Laravel + React + Stripe)

Salon-style appointment booking with secure card payments, queued email notifications, roles, and policies. Frontend is **React via Inertia.js** with Tailwind — landing hero, photo cards, stylist picker, dashboard stats. Built as a job-portfolio piece alongside the timetable app.

## Features

- Auth (register/login/logout) with roles: `admin`, `staff`, `customer`
- Services + staff management (4 seeded stylists)
- Book appointments with staff double-booking protection
- Secure card checkout via Stripe Elements + PaymentIntents (mock fallback when no keys set)
- Admins see only card brand + last 4 — full numbers never touch the server or database
- Queued mails on confirm/cancel (`AppointmentMail`, `ShouldQueue`, database queue, log mailer)
- Authorization via `AppointmentPolicy` (customers see/cancel own, staff confirm)
- Feature tests: overlap logic + policy (`tests/Feature/BookingTest.php`)

## Tech Stack

- PHP 8.2+ / Laravel 12 / SQLite / **React 19 + Inertia.js + Tailwind v4 (Vite build)**
- Queue: `database`, Mail: `log` (no SMTP needed)

## Quick Start

```powershell
$env:Path += ";C:\xampp\php;C:\ProgramData\ComposerSetup\bin"
cd booking-app
composer install
& "C:\Program Files\nodejs\npm.cmd" install
& "C:\Program Files\nodejs\npm.cmd" run build   # builds React frontend to public/build
php artisan migrate --force
php artisan db:seed --force
php artisan serve          # http://127.0.0.1:8000
php artisan queue:work     # second terminal: sends queued mails to storage/logs/laravel.log
```

Demo logins (password `password`): `admin@example.com`, `staff@example.com`, `customer@example.com`.
Test card: `4242 4242 4242 4242`, any future expiry, any CVC. Keys go in `.env` (`STRIPE_KEY`, `STRIPE_SECRET`) — never committed.

## Project Structure

```
app/Models/User.php (role + isStaff/isAdmin)
app/Models/Service|Staff|Appointment|Payment.php
app/Services/PaymentService.php   # Stripe intents, server-side verification, card last-4
app/Policies/AppointmentPolicy.php
app/Mail/AppointmentMail.php (queued)
app/Http/Controllers/Auth|Appointment|Admin|StripeWebhookController.php
app/Http/Middleware/HandleInertiaRequests.php
database/migrations/2026_10_01_00000{1,2}_*
database/seeders/BookingSeeder.php
routes/web.php                    # landing, auth, bookings, admin, webhooks/stripe
resources/js/app.jsx + Pages/{Landing,Login,Register,Dashboard,BookingForm,BookingDetail,Services,Staff}.jsx
resources/js/Components/{Layout,Checkout}.jsx
resources/views/app.blade.php     # Inertia root (+ emails/appointment.blade.php)
tests/Feature/BookingTest.php
```

## Data Model

- `users(id, name, email, password, role)`
- `services(id, name, duration_minutes, price_cents)`
- `staff(id, name, user_id?)`
- `appointments(id, service_id, staff_id, customer_id, starts_at, ends_at, status, payment_status)`
- `payments(id, appointment_id, provider, amount_cents, status, reference)`

Key rules:
- Overlap: `starts_at < new_end AND ends_at > new_start` per staff, excluding cancelled.
- Pay flow: pending mock payment → succeeded, appointment → confirmed + queued mail.
- Cancel: status → cancelled + queued mail.

## Payments (Stripe + mock fallback)

- Without keys: mock provider — book creates a `MOCK-*` payment, "Pay" marks it succeeded. Demo works out of the box.
- With `STRIPE_KEY` + `STRIPE_SECRET` set: booking creates a real Stripe **PaymentIntent** (amount taken from the service price server-side, never from the browser).
- Card input is a Stripe-hosted **Elements** field — raw card numbers go straight to Stripe and never touch this server (keeps PCI scope at SAQ A).
- Confirm flow: browser sends only the `payment_intent_id`; `PaymentService::confirmStripe()` re-fetches the intent, checks `status === succeeded`, amount matches, and `metadata.appointment_id` matches before marking paid.
- Webhook `POST /webhooks/stripe` (CSRF-exempt, signature-verified) handles `payment_intent.succeeded` idempotently as a backup.
- Test with card `4242 4242 4242 4242`, any future date/CVC. Forward webhooks locally: `stripe listen --forward-to localhost:8000/webhooks/stripe`.
