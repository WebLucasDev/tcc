@if($solicitations->hasPages())
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="text-sm text-[var(--color-text)] opacity-70">
            Mostrando {{ $solicitations->firstItem() }} a {{ $solicitations->lastItem() }} de {{ $solicitations->total() }} resultados
        </div>
        @if($solicitations->lastPage() > 1)
            <div class="pagination-links">
                {{ $solicitations->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@elseif($solicitations->count() > 0)
    <div class="text-center">
        <div class="text-sm text-[var(--color-text)] opacity-70">
            Mostrando {{ $solicitations->count() }} de {{ $solicitations->total() }} resultado{{ $solicitations->total() != 1 ? 's' : '' }}
        </div>
    </div>
@endif
