@if ($errors->any())
    @foreach ($errors->all() as $error)
        <div class="mb-4 text-[var(--color-error)]" id="message-error">
            {{ $error }}
        </div>
    @endforeach
@endif
