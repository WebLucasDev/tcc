@if($positions->hasPages())
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="text-sm text-[var(--color-text)] opacity-70">
            Mostrando {{ $positions->firstItem() }} a {{ $positions->lastItem() }} de {{ $positions->total() }} resultados
        </div>
        <div class="pagination-links">
            {{ $positions->links() }}
        </div>
    </div>
@endif
