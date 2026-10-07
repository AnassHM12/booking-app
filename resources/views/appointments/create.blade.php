@extends('layout')
@section('title','New booking')
@section('content')
<div class="card"><h2>New booking</h2>
<form method="POST" action="{{ route('appointments.store') }}">@csrf
<label>Service</label><select name="service_id">@foreach($services as $s)<option value="{{ $s->id }}">{{ $s->name }}, ${{ $s->price }} / {{ $s->duration_minutes }}min</option>@endforeach</select>
<label>Staff</label><select name="staff_id">@foreach($staff as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select>
<label>Start (future date/time)</label><input type="datetime-local" name="starts_at" required>
<button class="btn btn-primary">Book (creates mock payment)</button></form></div>
@endsection
