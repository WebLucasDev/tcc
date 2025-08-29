@if($departments->hasPages())
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="text-sm text-[var(--color-text)] opacity-70">
            Mostrando {{ $departments->firstItem() }} a {{ $departments->lastItem() }} de {{ $departments->total() }} resultados
        </div>
        <div class="pagination-links">
            {{ $departments->links() }}
        </div>
    </div>
@endif
