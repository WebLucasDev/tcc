@extends('layouts.layout')

@section('title', 'Banco de Horas')

@section('content')
<div class="space-y-6">

    <x-error/>
    <x-success/>


</div>
@endsection

@push('scripts')
    @vite(['resources/js/menus/for-employees/comp-time.js'])
@endpush
