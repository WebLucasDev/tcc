@if ($errors->any())
    <div class="fixed top-6 left-1/2 transform -translate-x-1/2 z-[9999] w-full max-w-md px-4 flex flex-col items-center pointer-events-none">
        <div class="mb-4 text-[var(--color-error)] bg-[var(--color-background)] border border-[var(--color-error)] shadow-lg rounded px-4 py-3 w-full text-center pointer-events-auto animate-fade-in" id="error" style="z-index:9999;">
            {{ $errors->first() }}
        </div>
    </div>
@endif
