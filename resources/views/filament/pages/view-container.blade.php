<x-filament-panels::page>
    <!-- Mobile Card Layout -->
    <div class="block md:hidden space-y-3">
        @for ($y = 0; $y < $record->compartments_y_size; $y++)
            @for ($x = 0; $x < $record->compartments_x_size; $x++)
                @php
                    $position = $record->getPositionAt($x + 1, $y + 1);
                    $content = $record->getContentAt($x + 1, $y + 1);
                    $isOccupied = $record->isPositionOccupied($x + 1, $y + 1);
                @endphp

                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            @if ($isOccupied)
                                <div class="flex items-center space-x-3">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $content }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Position ({{ $x + 1 }}, {{ $y + 1 }})
                                        </div>
                                    </div>
                                    @if($position && $position->sample)
                                        <div class="flex items-center">
                                    <x-filament::icon-button
                                            icon="heroicon-o-arrow-right"
                                            size="sm"
                                            color="primary"
                                            wire:click="gotoSample({{ $position->sample->id }})"
                                                class="shrink-0 "
                                            />
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-sm text-gray-500">
                                    Empty Position ({{ $x + 1 }}, {{ $y + 1 }})
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex items-center pl-3">
                            &nbsp;
                            @if ($isOccupied)
                                <x-filament::icon-button
                                    icon="heroicon-o-trash"
                                    size="sm"
                                    color="danger"
                                    wire:click="confirmDeletePosition({{ $x + 1 }}, {{ $y + 1 }})"
                                />
                            @else
                                <x-filament::icon-button
                                    icon="heroicon-o-plus"
                                    size="sm"
                                    color="primary"
                                    wire:click="openAddPositionModal({{ $x + 1 }}, {{ $y + 1 }})"
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
        <table class="w-full table-fixed border-collapse">
            <tbody>
                @for ($y = 0; $y < $record->compartments_y_size; $y++)
                    <tr>
                        @for ($x = 0; $x < $record->compartments_x_size; $x++)
                            @php
                                $position = $record->getPositionAt($x + 1, $y + 1);
                                $content = $record->getContentAt($x + 1, $y + 1);
                                $isOccupied = $record->isPositionOccupied($x + 1, $y + 1);
                            @endphp

                            <td class="border border-gray-200 p-0 align-middle">
                                <div
                                    @class([
                                        'w-full h-16 px-2 flex flex-col items-center justify-center text-center select-none',
                                        'hover:bg-gray-50 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-gray-300' => $isOccupied,
                                    ])
                                    @if($isOccupied && $position && $position->sample)
                                        wire:click="gotoSample({{ $position->sample->id }})"
                                        role="button"
                                        tabindex="0"
                                    @endif
                                >
                                    <div class="flex items-center justify-center w-full">
                                        @if ($isOccupied)
                                            <div class="text-sm font-medium truncate max-w-full flex-1">
                                                {{ $content }}
                                            </div>
                                            <div class="bg-danger-500 hover:bg-danger-600 p-5 rounded">
                                                <x-filament::icon-button
                                                    icon="heroicon-o-trash"
                                                    size="sm"
                                                    color="danger"
                                                    wire:click.prevent="deletePosition({{ $x + 1 }}, {{ $y + 1 }})"
                                                    class="ml-2"
                                                />
                                            </div>
                                        @else
                                            <div
                                                class="text-sm text-gray-400 flex-1 cursor-pointer hover:bg-gray-100 rounded px-2 py-1"
                                                wire:click="openAddPositionModal({{ $x + 1 }}, {{ $y + 1 }})"
                                                role="button"
                                                tabindex="0"
                                            >
                                                ({{ $x + 1 }}, {{ $y + 1 }})
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        @endfor
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
