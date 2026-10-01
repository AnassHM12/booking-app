<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title','Booking App')</title>
<style>
:root{--ink:#1e293b;--muted:#64748b;--line:#e2e8f0;--bg:#f1f5f9;--teal:#0f766e;--teal-dark:#115e59}
*{box-sizing:border-box}
body{font-family:Georgia,'Times New Roman',serif;margin:0;background:var(--bg);color:var(--ink);line-height:1.5}
nav{background:#1e293b;color:#fff;padding:14px 22px;display:flex;gap:18px;align-items:center;flex-wrap:wrap}
nav a,nav button{color:#cbd5e1;text-decoration:none;font-weight:normal;background:none;border:0;cursor:pointer;font-size:.95em;font-family:inherit}
nav a:hover,nav button:hover{color:#fff}
nav .brand{color:#fff;font-size:1.1em;margin-right:8px}
.container{max-width:960px;margin:28px auto;padding:0 18px}
.card{background:#fff;border:1px solid var(--line);border-radius:8px;padding:22px 24px;margin-bottom:18px;box-shadow:0 1px 3px rgba(0,0,0,.06)}
.card h2{margin:0 0 12px;font-size:1.3em;font-weight:normal;border-bottom:1px solid var(--line);padding-bottom:10px}
.btn{display:inline-block;padding:8px 16px;border-radius:6px;border:1px solid transparent;cursor:pointer;text-decoration:none;font-size:.92em;font-family:inherit}
.btn-primary{background:var(--teal);color:#fff;border-color:var(--teal-dark)}
.btn-primary:hover{background:var(--teal-dark)}
.btn-danger{background:#fff;color:#b91c1c;border-color:#fecaca}
.btn-danger:hover{background:#fef2f2}
.btn-secondary{background:#f8fafc;color:var(--ink);border-color:var(--line)}
table{width:100%;border-collapse:collapse;margin-top:12px;font-size:.93em;font-family:-apple-system,'Segoe UI',Arial,sans-serif}
th,td{padding:9px 10px;border-bottom:1px solid var(--line);text-align:left}
th{font-weight:600;color:var(--muted);font-size:.82em;text-transform:uppercase;letter-spacing:.04em;background:none}
tr:hover td{background:#f8fafc}
label{display:block;font-size:.88em;color:var(--muted);margin-top:8px;font-family:-apple-system,'Segoe UI',Arial,sans-serif}
input,select{padding:9px 11px;border:1px solid #cbd5e1;border-radius:6px;width:100%;margin:4px 0 10px;font-size:.95em;font-family:inherit;background:#fff}
input:focus,select:focus{outline:2px solid #99f6e4;border-color:var(--teal)}
form.inline{display:inline}
.alert{padding:10px 14px;border-radius:6px;margin-bottom:14px;font-size:.92em;font-family:-apple-system,'Segoe UI',Arial,sans-serif}
.alert-success{background:#f0fdf4;color:#166534;border:1px solid #bbf7d0}
.alert-error{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}
.badge{padding:2px 10px;border-radius:20px;font-size:.78em;border:1px solid var(--line);background:#f8fafc;color:var(--muted);font-family:-apple-system,'Segoe UI',Arial,sans-serif}
.paid{background:#f0fdf4;color:#166534;border-color:#bbf7d0}
.unpaid{background:#fffbeb;color:#92400e;border-color:#fde68a}
footer{text-align:center;color:var(--muted);font-size:.82em;padding:24px;font-family:-apple-system,'Segoe UI',Arial,sans-serif}
</style>
</head>
<body>
<nav><span class="brand">Booking App</span>
@if(auth()->check())
<a href="{{ route('appointments.index') }}">Bookings</a>
<a href="{{ route('appointments.create') }}">New booking</a>
<a href="{{ route('admin.services') }}">Services</a>
<a href="{{ route('admin.staff') }}">Staff</a>
<span style="margin-left:auto;color:#94a3b8;font-size:.9em">{{ auth()->user()->name }} &middot; {{ auth()->user()->role }}</span>
<form class="inline" method="POST" action="{{ route('logout') }}">@csrf<button>Log out</button></form>
@else
<a href="{{ route('login') }}">Log in</a><a href="{{ route('register') }}">Register</a>
@endif
</nav>
<div class="container">
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-error"><ul style="margin:0;padding-left:18px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
@yield('content')
</div>
<footer>Simple booking demo &middot; Laravel + SQLite</footer>
</body>
</html>
