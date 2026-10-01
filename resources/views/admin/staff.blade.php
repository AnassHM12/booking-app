@extends('layout')
@section('title','Staff')
@section('content')
<div class="card"><h2>Staff</h2>
<form method="POST" action="{{ route('admin.staff.store') }}">@csrf
<input name="name" placeholder="Name" required style="max-width:250px">
<button class="btn btn-primary">Save</button></form>
<table><tr><th>Name</th></tr>
@foreach($staff as $s)<tr><td>{{ $s->name }}</td></tr>@endforeach</table>
{{ $staff->links() }}</div>
@endsection
