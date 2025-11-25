<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Container Info -->
        <div class="text-sm text-gray-600 dark:text-gray-400">
            <p>Grid: {{ $record->readable_size }} • {{ $record->positions()->count() }} position(s) occupied</p>
        </div>

        <!-- Mobile Card Layout -->
        <div class="block md:hidden space-y-3">
            @for ($y = 0; $y < $record->compartments_y_size; $y++)
                @for ($x = 0; $x < $record->compartments_x_size; $x++)
                    @php
                        $position = $record->getPositionAt($x + 1, $y + 1);
                        $content = $record->getContentAt($x + 1, $y + 1);
                        $isOccupied = $record->isPositionOccupied($x + 1, $y + 1);
                    @endphp

                    <div class="bg-white dark:bg-gray-800 border rounded-lg shadow-sm transition-all hover:shadow-md p-4
                        {{ $isOccupied ? 'border-primary-300 dark:border-primary-700 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                @if ($isOccupied)
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-primary-500 dark:bg-primary-600 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-base font-semibold text-gray-900 dark:text-white truncate">
                                                {{ $content }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                Position ({{ $x + 1 }}, {{ $y + 1 }})
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                Empty Position
                                            </div>
                                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                                ({{ $x + 1 }}, {{ $y + 1 }})
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex items-center gap-2 ml-3">
                                @if ($isOccupied)
                                    @if($position && $position->sample)
                                        <x-filament::icon-button
                                            icon="heroicon-o-arrow-right"
                                            size="sm"
                                            color="primary"
                                            wire:click="gotoSample({{ $position->sample->id }})"
                                            tooltip="View Sample"
                                        />
                                    @endif
                                    <x-filament::icon-button
                                        icon="heroicon-o-trash"
                                        size="sm"
                                        color="danger"
                                        wire:click="deletePosition({{ $x + 1 }}, {{ $y + 1 }})"
                                        tooltip="Clear Position"
                                        onclick="return confirm('Are you sure you want to clear this position?')"
                                    />
                                @else
                                    <x-filament::icon-button
                                        icon="heroicon-o-plus"
                                        size="sm"
                                        color="primary"
                                        wire:click="openAddPositionModal({{ $x + 1 }}, {{ $y + 1 }})"
                                        tooltip="Add to Position"
                                    />
                                @endif
                            </div>
                        </div>
                    </div>
                @endfor
            @endfor
        </div>

        <!-- Desktop Grid Layout -->
        <div class="hidden md:block">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="inline-flex flex-col gap-3">
                    @for ($y = 0; $y < $record->compartments_y_size; $y++)
                        <div class="flex gap-3">
                            @for ($x = 0; $x < $record->compartments_x_size; $x++)
                                @php
                                    $position = $record->getPositionAt($x + 1, $y + 1);
                                    $content = $record->getContentAt($x + 1, $y + 1);
                                    $isOccupied = $record->isPositionOccupied($x + 1, $y + 1);
                                @endphp

                                <div
                                    @class([
                                        'relative group rounded-lg border-2 p-4 w-32 h-32 flex flex-col items-center justify-center transition-all duration-200',
                                        'border-primary-300 dark:border-primary-700 bg-primary-50 dark:bg-primary-900/20 hover:bg-primary-100 dark:hover:bg-primary-900/30 hover:shadow-md cursor-pointer' => $isOccupied,
                                        'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-700/50 hover:border-gray-300 dark:hover:border-gray-600' => !$isOccupied,
                                    ])
                                    @if($isOccupied && $position && $position->sample)
                                        wire:click="gotoSample({{ $position->sample->id }})"
                                        role="button"
                                        tabindex="0"
                                    @elseif(!$isOccupied)
                                        wire:click="openAddPositionModal({{ $x + 1 }}, {{ $y + 1 }})"
                                        role="button"
                                        tabindex="0"
                                    @endif
                                >
                                    <!-- Position Label -->
                                    <div class="absolute top-1 left-1 text-xs font-medium text-gray-400 dark:text-gray-500">
                                        ({{ $x + 1 }}, {{ $y + 1 }})
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 flex flex-col items-center justify-center w-full text-center">
                                        @if ($isOccupied)
                                            <div class="mb-1">
                                                <div class="w-10 h-10 rounded-lg bg-primary-500 dark:bg-primary-600 flex items-center justify-center mx-auto">
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="text-xs font-semibold text-gray-900 dark:text-white break-words max-w-full px-1 line-clamp-2">
                                                {{ $content }}
                                            </div>
                                        @else
                                            <div class="mb-1">
                                                <div class="w-10 h-10 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center mx-auto">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                Empty
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Action Buttons (shown on hover) -->
                                    <div class="absolute bottom-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
                                        @if ($isOccupied)
                                            @if($position && $position->sample)
                                                <x-filament::icon-button
                                                    icon="heroicon-o-arrow-right"
                                                    size="xs"
                                                    color="primary"
                                                    wire:click.stop="gotoSample({{ $position->sample->id }})"
                                                    tooltip="View Sample"
                                                />
                                            @endif
                                            <x-filament::icon-button
                                                icon="heroicon-o-trash"
                                                size="xs"
                                                color="danger"
                                                wire:click.stop="deletePosition({{ $x + 1 }}, {{ $y + 1 }})"
                                                tooltip="Clear Position"
                                                onclick="event.stopPropagation(); return confirm('Are you sure you want to clear this position?')"
                                            />
                                        @else
                                            <x-filament::icon-button
                                                icon="heroicon-o-plus"
                                                size="xs"
                                                color="primary"
                                                wire:click.stop="openAddPositionModal({{ $x + 1 }}, {{ $y + 1 }})"
                                                tooltip="Add to Position"
                                            />
                                        @endif
                                    </div>
                                </div>
                            @endfor
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
