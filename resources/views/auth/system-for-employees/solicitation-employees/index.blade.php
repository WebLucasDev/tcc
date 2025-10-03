@extends('layouts.layout')

@section('title', 'Solicitações')

@section('content')
<div class="space-y-6">

    <x-error/>
    <x-success/>


</div>
@endsection

@push('scripts')
    @vite(['resources/js/menus/for-employees/solicitations.js'])
@endpush
