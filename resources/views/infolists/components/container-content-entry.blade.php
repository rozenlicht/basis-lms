<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="flex pb-5 h-12">
        {{ $getAction('addSample') }}

    </div>
    <table class="w-full border-collapse">
        <tbody>
            @for($y = 0; $y < $getRecord()->compartments_y_size; $y++)
                <tr>
                    @for($x = 0; $x < $getRecord()->compartments_x_size; $x++)
                        @php
                            $position = $getRecord()->getPositionAt($x + 1, $y + 1);
                            $content = $getRecord()->getContentAt($x + 1, $y + 1);
                            $isOccupied = $getRecord()->isPositionOccupied($x + 1, $y + 1);
                        @endphp

                        <td class="border p-0 align-middle">
                            <div
                            style="max-width: 120px;"
                                class="p-2 text-center mx-auto w-full h-16 flex flex-col items-center justify-center cursor-pointer select-none"
                                @if($isOccupied && $position && $position->sample) 
                                    wire:click="redirect('filament.resources.samples.edit')"
                                @endif
                            >
                                @if($isOccupied)
                                    <div class="text-sm font-semibold">{{ $content }}</div>
                                @else
                                    <div class="text-sm text-gray-400">Empty</div>
                                @endif
                            </div>
                        </td>
                    @endfor
                </tr>
            @endfor
        </tbody>
    </table>
</x-dynamic-component>
