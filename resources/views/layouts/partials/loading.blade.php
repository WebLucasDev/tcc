<!-- Loading Global Component -->
<div id="global-loading" class="fixed inset-0 bg-black/85 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-[var(--color-background)] rounded-lg p-8 shadow-xl">
            <div class="flex flex-col items-center gap-4">
                <div class="relative">
                    <div class="w-12 h-12 border-4 border-[var(--color-main)] border-opacity-20 rounded-full animate-spin border-t-[var(--color-main)]"></div>
                </div>
                <div class="text-[var(--color-text)] font-medium loading-message">
                    {{ $message ?? 'Carregando...' }}
                </div>
            </div>
        </div>
    </div>
</div>
