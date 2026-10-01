@extends('layout')
@section('title','Booking detail')
@section('content')
<div class="card"><h2>Booking #{{ $appointment->id }}</h2>
<p><strong>{{ $appointment->service->name }}</strong> with {{ $appointment->staff->name }}<br>
{{ $appointment->starts_at->format('l d M Y H:i') }} – {{ $appointment->ends_at->format('H:i') }}<br>
Status: {{ $appointment->status }} · Payment: <span class="badge {{ $appointment->payment_status }}">{{ $appointment->payment_status }}</span></p>
<h3>Payments</h3>
<table><tr><th>Provider</th><th>Amount</th><th>Status</th><th>Ref</th></tr>
@foreach($appointment->payments as $p)<tr><td>{{ $p->provider }}</td><td>${{ number_format($p->amount_cents/100,2) }}</td><td>{{ $p->status }}</td><td>{{ $p->reference }}</td></tr>@endforeach</table>
<p>
@if($appointment->payment_status!=='paid' && $appointment->status!=='cancelled')
<form class="inline" method="POST" action="{{ route('appointments.pay',$appointment) }}">@csrf<button class="btn btn-primary">Pay (mock)</button></form>
@endif
@if($appointment->status!=='cancelled')
<form class="inline" method="POST" action="{{ route('appointments.cancel',$appointment) }}">@csrf<button class="btn btn-danger" onclick="return confirm('Cancel?')">Cancel</button></form>
@endif
</p>
<p><small>Emails are queued (database queue, log mailer). Run <code>php artisan queue:work</code> and check <code>storage/logs/laravel.log</code>.</small></p>
</div>
@endsection
