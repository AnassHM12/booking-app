# Booking App (Laravel + React + Stripe)

A salon style appointment booking app. Customers pick a service and stylist, pay by card, and get email confirmations. I built it as a portfolio piece next to the timetable app.

The frontend is React through Inertia.js with Tailwind. There is a landing page with photos, a booking dashboard, a stylist picker, and a checkout form.

## Features

- Sign up, log in, log out, with three roles: `admin`, `staff`, `customer`
- Manage services and staff (4 stylists seeded)
- Book appointments. The app refuses double bookings for the same stylist.
- Card checkout with Stripe Elements and PaymentIntents. Works without keys too, using a mock provider.
- Admins only ever see card brand plus last 4 digits. Full card numbers never reach the server or the database.
- Emails on confirm and cancel, sent through the queue (`AppointmentMail`)
- Rules on who can see or cancel what, in `AppointmentPolicy`
- Feature tests for the overlap check and the policy (`tests/Feature/BookingTest.php`)

## Tech Stack

- PHP 8.2+, Laravel 12, SQLite
- React 19 + Inertia.js + Tailwind v4, built with Vite
- Queue driver `database`, mail driver `log`, so no SMTP setup needed

## Quick Start

```powershell
$env:Path += ";C:\xampp\php;C:\ProgramData\ComposerSetup\bin"
cd booking-app
composer install
& "C:\Program Files\nodejs\npm.cmd" install
& "C:\Program Files\nodejs\npm.cmd" run build
php artisan migrate --force
php artisan db:seed --force
php artisan serve
php artisan queue:work
```

Open http://127.0.0.1:8000. Run the queue worker in a second terminal so the emails get sent (they land in `storage/logs/laravel.log`).

Demo logins, all with password `password`: `admin@example.com`, `staff@example.com`, `customer@example.com`.
Test card: `4242 4242 4242 4242`, any future expiry, any CVC. Stripe keys go in `.env` and are never committed.

## Project Structure

```
app/Models/User.php (role plus isStaff/isAdmin helpers)
app/Models/Service|Staff|Appointment|Payment.php
app/Services/PaymentService.php (Stripe intents, server side checks, card last 4)
app/Policies/AppointmentPolicy.php
app/Mail/AppointmentMail.php (queued)
app/Http/Controllers/Auth|Appointment|Admin|StripeWebhookController.php
app/Http/Middleware/HandleInertiaRequests.php
database/migrations/2026_10_01_00000{1,2}_*
database/seeders/BookingSeeder.php
routes/web.php (landing, auth, bookings, admin, webhooks/stripe)
resources/js/app.jsx + Pages/{Landing,Login,Register,Dashboard,BookingForm,BookingDetail,Services,Staff}.jsx
resources/js/Components/{Layout,Checkout}.jsx
resources/views/app.blade.php (Inertia root, plus emails/appointment.blade.php)
tests/Feature/BookingTest.php
```

## Data Model

- `users(id, name, email, password, role)`
- `services(id, name, duration_minutes, price_cents)`
- `staff(id, name, user_id?)`
- `appointments(id, service_id, staff_id, customer_id, starts_at, ends_at, status, payment_status)`
- `payments(id, appointment_id, provider, amount_cents, status, reference)`

How it works:

- Overlap check: a new booking conflicts when `starts_at < new_end AND ends_at > new_start` for the same stylist. Cancelled bookings do not count.
- Pay flow: pending payment goes to succeeded, appointment goes to confirmed, confirmation email gets queued.
- Cancel: status goes to cancelled, cancellation email gets queued.

## Payments (Stripe plus mock fallback)

- Without keys the app uses the mock provider. Booking creates a `MOCK-*` payment and the Pay button marks it succeeded, so the demo works out of the box.
- With `STRIPE_KEY` and `STRIPE_SECRET` set, booking creates a real Stripe PaymentIntent. The amount comes from the service price on the server, never from the browser.
- The card field is hosted by Stripe (Elements). Raw card numbers go straight to Stripe and never touch this server.
- To confirm, the browser sends back only the payment intent id. `PaymentService::confirmStripe()` fetches the intent again and checks the status, the amount, and the appointment id before marking anything paid.
- There is also a `POST /webhooks/stripe` endpoint as a backup. It checks the Stripe signature and skips anything already handled.
- To test locally: card `4242 4242 4242 4242`, any future date and CVC. For webhooks: `stripe listen --forward-to localhost:8000/webhooks/stripe`.
