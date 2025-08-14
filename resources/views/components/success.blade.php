@if (session('success'))
    <div class="mb-4 text-[var(--color-success)]" id="success">
        {{ session('success') }}
    </div>
@endif
