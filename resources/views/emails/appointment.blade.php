<p>Hello {{ $appointment->customer->name }},</p>
<p>Your appointment is <strong>{{ $kind }}</strong>:</p>
<p>{{ $appointment->service->name }} with {{ $appointment->staff->name }}<br>
{{ $appointment->starts_at }} – {{ $appointment->ends_at }}</p>
<p>Booking #{{ $appointment->id }} · {{ $appointment->payment_status }}</p>
