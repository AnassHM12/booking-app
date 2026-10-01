@extends('layout')
@section('title','Login')
@section('content')
<div class="card"><h2>Login</h2>
<form method="POST" action="/login">@csrf
<label>Email</label><input name="email" type="email" required>
<label>Password</label><input name="password" type="password" required>
<button class="btn btn-primary">Login</button> <a href="/register">Register</a>
</form><p><small>Demo: admin@example.com / password, staff@example.com / password, customer@example.com / password</small></p></div>
@endsection
