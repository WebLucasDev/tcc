@extends('layouts.layout')
@section('title', 'Cargos')

@section('content')

    <x-success/>
    <x-error/>

    <div class="space-y-6">

    </div>

@endsection

@push('scripts')
    @vite(['resources/js/menus/work-hours.js'])
@endpush
