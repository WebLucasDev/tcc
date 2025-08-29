@if($collaborators->hasPages())
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="text-sm text-[var(--color-text)] opacity-70">
            Mostrando {{ $collaborators->firstItem() }} a {{ $collaborators->lastItem() }} de {{ $collaborators->total() }} resultados
        </div>
        <div class="pagination-links">
            {{ $collaborators->links() }}
        </div>
    </div>
@endif
