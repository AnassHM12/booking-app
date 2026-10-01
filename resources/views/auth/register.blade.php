@extends('layout')
@section('title','Register')
@section('content')
<div class="card"><h2>Register</h2>
<form method="POST" action="/register">@csrf
<label>Name</label><input name="name" required>
<label>Email</label><input name="email" type="email" required>
<label>Password</label><input name="password" type="password" required>
<label>Confirm</label><input name="password_confirmation" type="password" required>
<button class="btn btn-primary">Create account</button></form></div>
@endsection
