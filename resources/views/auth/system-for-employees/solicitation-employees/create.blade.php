@extends('layouts.layout')

@section('title', 'Nova Solicitação')

@section('content')
<div class="space-y-6">

    <x-error/>
    <x-success/>

    <!-- Header com Breadcrumb -->
    <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
        <nav class="text-sm mb-4">
            <ol class="flex items-center space-x-2 text-[var(--color-text)]/60">
                <li>
                    <a href="{{ route('system-for-employees.solicitation.index') }}" class="hover:text-[var(--color-main)] transition-colors">
                        <i class="fa-solid fa-file-lines mr-1"></i>
                        Solicitações
                    </a>
                </li>
                <li>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </li>
                <li class="text-[var(--color-main)] font-semibold">
                    Nova Solicitação
                </li>
            </ol>
        </nav>

        <h1 class="text-xl sm:text-3xl font-bold text-[var(--color-main)]">
            <i class="fa-solid fa-plus mr-2"></i>
            Nova Solicitação de Alteração
        </h1>
        <p class="text-[var(--color-text)] mt-2 text-sm sm:text-base">
            Solicite a correção de horários de ponto registrados incorretamente
        </p>
    </div>

    <!-- Formulário -->
    <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
        <form action="{{ route('system-for-employees.solicitation.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Informação -->
            <div class="bg-[var(--color-main)]/10 border border-[var(--color-main)]/30 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fa-solid fa-circle-info text-[var(--color-main)] text-xl mr-3 mt-1"></i>
                    <div class="text-sm text-[var(--color-text)]">
                        <p class="font-semibold mb-1 text-sm sm:text-base">Como funciona?</p>
                        <p class="mb-2 text-sm sm:text-base"><strong>Passo 1:</strong> Escolha o dia que você deseja corrigir;</p>
                        <p class="mb-2 text-sm sm:text-base"><strong>Passo 2:</strong> Selecione o período;</p>
                        <p class="mb-2 text-sm sm:text-base"><strong>Passo 3:</strong> Veja os horários que estão registrados atualmente;</p>
                        <p><strong>Passo 4:</strong> Informe os horários corretos e justifique o motivo da alteração.</p>
                    </div>
                </div>
            </div>

            <!-- Seleção de Registro de Ponto -->
            <div>
                <label for="time_tracking_id" class="block text-sm font-bold text-[var(--color-text)] mb-2">
                    <i class="fa-solid fa-calendar-day mr-1"></i>
                    Selecione o Dia <span class="text-red-500">*</span>
                </label>
                <select
                    id="time_tracking_id"
                    name="time_tracking_id"
                    required
                    onchange="loadTimeTrackingData(this.value)"
                    class="w-full px-4 py-3 bg-[var(--color-background)] text-[var(--color-text)] border border-[var(--color-text)]/20 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all">
                    <option value="">-- Selecione um dia --</option>
                    @foreach($timeTrackings as $timeTracking)
                        <option value="{{ $timeTracking->id }}" data-tracking="{{ json_encode($timeTracking) }}">
                            {{ \Carbon\Carbon::parse($timeTracking->date)->format('d/m/Y (l)') }} -
                            Status: {{ $timeTracking->status->label() }}
                        </option>
                    @endforeach
                </select>
                @error('time_tracking_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Seleção de Período -->
            <div id="period-selection" class="hidden">
                <label for="period" class="block text-sm font-bold text-[var(--color-text)] mb-2">
                    <i class="fa-solid fa-business-time mr-1"></i>
                    Selecione o Período que Deseja Ajustar <span class="text-red-500">*</span>
                </label>
                <select
                    id="period"
                    name="period"
                    required
                    onchange="loadPeriodData(this.value)"
                    class="w-full px-4 py-3 bg-[var(--color-background)] text-[var(--color-text)] border border-[var(--color-text)]/20 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all">
                    <option value="">-- Selecione o período --</option>
                    <option value="morning">Período da Manhã (Entrada → Saída Almoço)</option>
                    <option value="afternoon">Período da Tarde (Retorno Almoço → Saída)</option>
                </select>
                @error('period')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Horários Atuais (exibidos após seleção) -->
            <div id="current-times" class="hidden bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-4">
                <h3 class="font-bold text-[var(--color-text)] mb-3 flex items-center">
                    <i class="fa-solid fa-clock-rotate-left mr-2 text-yellow-500"></i>
                    Horário Registrado Atualmente no Sistema
                </h3>
                <p class="text-sm text-[var(--color-text)]/70 mb-3">
                    Estes são os horários que constam no registro de ponto do dia selecionado
                </p>
                <div class="bg-[var(--color-background)] p-4 rounded-lg border border-[var(--color-text)]/10">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[var(--color-text)]/60 text-xs mb-1">Entrada:</p>
                            <p class="font-bold text-2xl" id="current-entry">--:--</p>
                        </div>
                        <div>
                            <p class="text-[var(--color-text)]/60 text-xs mb-1">Saída:</p>
                            <p class="font-bold text-2xl" id="current-exit">--:--</p>
                        </div>
                    </div>
                </div>
                <!-- Campos hidden para enviar os horários antigos -->
                <input type="hidden" id="old_time_start" name="old_time_start">
                <input type="hidden" id="old_time_finish" name="old_time_finish">
            </div>

            <!-- Novo Horário (obrigatório) -->
            <div id="new-time-section" class="hidden bg-[var(--color-main)]/5 border border-[var(--color-main)]/30 rounded-lg p-4">
                <h3 class="font-bold text-[var(--color-main)] mb-3 flex items-center">
                    <i class="fa-solid fa-clock mr-2"></i>
                    Novo Horário Solicitado (Correto)
                </h3>
                <p class="text-sm text-[var(--color-text)]/70 mb-4">
                    Informe o horário correto de entrada e saída que deveria estar registrado
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="new_time_start" class="block text-sm font-semibold text-[var(--color-text)] mb-2">
                            Horário de Entrada Correto <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="time"
                            id="new_time_start"
                            name="new_time_start"
                            value="{{ old('new_time_start') }}"
                            required
                            class="w-full px-4 py-3 bg-[var(--color-background)] text-[var(--color-text)] border border-[var(--color-text)]/20 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all text-lg font-semibold">
                        @error('new_time_start')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new_time_finish" class="block text-sm font-semibold text-[var(--color-text)] mb-2">
                            Horário de Saída Correto <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="time"
                            id="new_time_finish"
                            name="new_time_finish"
                            value="{{ old('new_time_finish') }}"
                            required
                            class="w-full px-4 py-3 bg-[var(--color-background)] text-[var(--color-text)] border border-[var(--color-text)]/20 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all text-lg font-semibold">
                        @error('new_time_finish')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Motivo -->
            <div>
                <label for="reason" class="block text-sm font-bold text-[var(--color-text)] mb-2">
                    <i class="fa-solid fa-comment-dots mr-1"></i>
                    Motivo da Solicitação <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="reason"
                    name="reason"
                    rows="4"
                    required
                    maxlength="500"
                    placeholder="Explique detalhadamente o motivo da solicitação de alteração..."
                    class="w-full px-4 py-3 bg-[var(--color-background)] text-[var(--color-text)] border border-[var(--color-text)]/20 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all resize-none">{{ old('reason') }}</textarea>
                <div class="flex justify-between mt-1">
                    @error('reason')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @else
                        <p class="text-[var(--color-text)]/60 text-sm">Seja claro e objetivo na justificativa</p>
                    @enderror
                    <p class="text-[var(--color-text)]/60 text-sm">
                        <span id="char-count">0</span>/500 caracteres
                    </p>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-[var(--color-text)]/10">
                <button
                    type="submit"
                    class="flex-1 bg-[var(--color-main)] hover:bg-[var(--color-main)]/90 text-white px-6 py-3 rounded-lg font-semibold transition-colors shadow-lg hover:shadow-xl">
                    <i class="fa-solid fa-paper-plane mr-2"></i>
                    Enviar Solicitação
                </button>
                <a
                    href="{{ route('system-for-employees.solicitation.index') }}"
                    class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors text-center">
                    <i class="fa-solid fa-times mr-2"></i>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/menus/for-employees/solicitation-create.js'])
@endpush
