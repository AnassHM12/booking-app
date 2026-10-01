@extends('layout')
@section('title','Services')
@section('content')
<div class="card"><h2>Services</h2>
<form method="POST" action="{{ route('admin.services.store') }}">@csrf
<input name="name" placeholder="Name" required style="max-width:250px">
<input name="duration_minutes" type="number" min="15" value="60" style="max-width:150px">
<input name="price_cents" type="number" min="0" value="5000" style="max-width:150px">
<button class="btn btn-primary">Save</button></form>
<table><tr><th>Name</th><th>Duration</th><th>Price</th></tr>
@foreach($services as $s)<tr><td>{{ $s->name }}</td><td>{{ $s->duration_minutes }} min</td><td>${{ $s->price }}</td></tr>@endforeach</table>
{{ $services->links() }}</div>
@endsection
