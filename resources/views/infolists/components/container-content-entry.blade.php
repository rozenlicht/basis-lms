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
                            $sample = $getRecord()->samples->firstWhere(fn ($sample) =>
                                $sample->compartment_x - 1 == $x && $sample->compartment_y - 1 == $y
                            );
                        @endphp

                        <td class="border p-0 align-middle">
                            <div
                            style="max-width: 120px;"
                                class="p-2 text-center mx-auto w-full h-16 flex flex-col items-center justify-center cursor-pointer select-none"
                                @if($sample) 
                                    wire:click="redirect('filament.resources.samples.edit')"
                                @endif
                            >
                                @if($sample)
                                    <div class="text-sm font-semibold">{{ $sample->unique_ref }}</div>
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
