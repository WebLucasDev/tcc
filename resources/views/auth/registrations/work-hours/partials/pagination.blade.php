@if($workHours->hasPages())
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="text-sm text-[var(--color-text)] opacity-70">
            Mostrando {{ $workHours->firstItem() }} a {{ $workHours->lastItem() }} de {{ $workHours->total() }} resultados
        </div>
        <div class="pagination-links">
            {{ $workHours->links() }}
        </div>
    </div>
@endif
