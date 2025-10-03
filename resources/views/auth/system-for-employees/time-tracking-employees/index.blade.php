@extends('layouts.layout')

@section('title', 'Bater Ponto')

@section('content')
<div class="space-y-6">

    <x-error/>
    <x-success/>


</div>
@endsection

@push('scripts')
    @vite(['resources/js/menus/for-employees/time-tracking.js'])
@endpush
