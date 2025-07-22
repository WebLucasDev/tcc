@extends('layouts.layout')
@section('title', 'Dashboard')

<form action="{{ route('logout') }}" method="POST" style="display: inline;">
	@csrf
	<button type="submit" style="background: none; border: none; color: blue; text-decoration: underline; cursor: pointer;">logout</button>
</form>
