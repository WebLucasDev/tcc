<div class="px-6 py-4 border-t border-[var(--color-text)]/10">
    <div class="flex items-center justify-between">
        <div class="text-sm text-[var(--color-text)]">
            Mostrando <span class="font-medium">{{ $paginationInfo['from'] }}</span> até
            <span class="font-medium">{{ $paginationInfo['to'] }}</span> de
            <span class="font-medium">{{ $paginationInfo['total'] }}</span> registros
        </div>

        <div class="flex space-x-2">
            {{ $timeTrackings->links() }}
        </div>
    </div>
</div>
