<!-- Paginação -->
<div class="bg-[var(--color-background)] rounded-xl shadow-lg p-4 border border-[var(--color-text)]/10">
    <div class="flex items-center justify-between">
        <div class="text-sm text-[var(--color-text)]">
            Mostrando
            <span class="font-semibold">{{ $solicitations->firstItem() ?? 0 }}</span>
            a
            <span class="font-semibold">{{ $solicitations->lastItem() ?? 0 }}</span>
            de
            <span class="font-semibold">{{ $solicitations->total() }}</span>
            resultados
        </div>

        <div class="flex gap-2">
            {{ $solicitations->links() }}
        </div>
    </div>
</div>
