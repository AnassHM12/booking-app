@extends('layout')
@section('title','Bookings')
@section('content')
<div class="card"><h2>Bookings <a class="btn btn-primary" href="{{ route('appointments.create') }}">+ Book</a></h2>
<form method="GET"><select name="status" onchange="this.form.submit()"><option value="">All</option><option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option><option value="confirmed" {{ request('status')=='confirmed'?'selected':'' }}>Confirmed</option><option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option></select></form>
<table><tr><th>When</th><th>Service</th><th>Staff</th><th>Customer</th><th>Status</th><th>Pay</th><th></th></tr>
@foreach($appointments as $a)
<tr><td>{{ $a->starts_at->format('D d M H:i') }}–{{ $a->ends_at->format('H:i') }}</td><td>{{ $a->service->name }}</td><td>{{ $a->staff->name }}</td><td>{{ $a->customer->name }}</td><td>{{ $a->status }}</td>
<td><span class="badge {{ $a->payment_status }}">{{ $a->payment_status }}</span></td>
<td><a href="{{ route('appointments.show',$a) }}">View</a></td></tr>
@endforeach</table>
{{ $appointments->links() }}</div>
@endsection
