<x-filament-panels::page>
    <table class="w-full table-fixed border-collapse">
        <tbody>
            @for ($y = 0; $y < $record->compartments_y_size; $y++)
                <tr>
                    @for ($x = 0; $x < $record->compartments_x_size; $x++)
                        @php
                            $sample = $record->samples->firstWhere(fn ($sample) =>
                                $sample->compartment_x - 1 == $x && $sample->compartment_y - 1 == $y
                            );
                        @endphp

                        <td class="border border-gray-200 p-0 align-middle">
                            <div
                                @class([
                                    'w-full h-16 px-2 flex flex-col items-center justify-center text-center select-none',
                                    'hover:bg-gray-50 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-gray-300' => $sample,
                                ])
                                @if($sample)
                                    wire:click="gotoSample({{ $sample->id }})"
                                    role="button"
                                    tabindex="0"
                                @endif
                            >
                                <div class="flex items-center justify-center w-full">
                                    @if ($sample)
                                        <div class="text-sm font-medium truncate max-w-full flex-1">
                                            {{ $sample->unique_ref }}
                                        </div>
                                        <div class="bg-danger-500 hover:bg-danger-600 p-5 rounded">
                                            <x-filament::icon-button
                                                icon="heroicon-o-trash"
                                                size="sm"
                                                color="danger"
                                                wire:click.prevent="deleteSample({{ $sample->id }})"
                                                class="ml-2"
                                            />
                                        </div>
                                    @else
                                        <div
                                            class="text-sm text-gray-400 flex-1 cursor-pointer hover:bg-gray-100 rounded px-2 py-1"
                                            wire:click="openConnectSampleModal({{ $x + 1 }}, {{ $y + 1 }})"
                                            role="button"
                                            tabindex="0"
                                        >
                                            Empty
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
</x-filament-panels::page>
