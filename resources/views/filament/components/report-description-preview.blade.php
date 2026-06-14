<div class="space-y-2">
    <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-3">
        <div class="flex items-center gap-2 mb-1">
            <x-heroicon-o-eye class="w-4 h-4 text-gray-500" />
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Preview Hasil</span>
        </div>
        <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap break-words">
            {{ $preview }}
        </div>
    </div>

    @if (!empty($validation['errors']))
        <div class="bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-700 rounded-lg p-3">
            <div class="flex items-center gap-2 mb-1">
                <x-heroicon-o-x-circle class="w-4 h-4 text-danger-500" />
                <span class="text-xs font-medium text-danger-500 uppercase tracking-wide">Error</span>
            </div>
            <ul class="space-y-1">
                @foreach ($validation['errors'] as $error)
                    <li class="text-xs text-danger-600 dark:text-danger-400 flex items-start gap-1">
                        <span>&bull;</span>
                        <span>{{ $error }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (!empty($validation['warnings']))
        <div class="bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-700 rounded-lg p-3">
            <div class="flex items-center gap-2 mb-1">
                <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-warning-500" />
                <span class="text-xs font-medium text-warning-500 uppercase tracking-wide">Peringatan</span>
            </div>
            <ul class="space-y-1">
                @foreach ($validation['warnings'] as $warning)
                    <li class="text-xs text-warning-600 dark:text-warning-400 flex items-start gap-1">
                        <span>&bull;</span>
                        <span>{{ $warning }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (empty($validation['errors']) && empty($validation['warnings']))
        <div class="bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-700 rounded-lg p-3">
            <div class="flex items-center gap-2">
                <x-heroicon-o-check-circle class="w-4 h-4 text-success-500" />
                <span class="text-xs font-medium text-success-500 uppercase tracking-wide">Template Valid</span>
            </div>
        </div>
    @endif
</div>
