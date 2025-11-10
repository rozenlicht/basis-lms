<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $timeline = collect($getState() ?? [])
            ->map(function ($item) {
                if ($item instanceof \Closure) {
                    $item = $item();
                }

                if ($item instanceof \Illuminate\Contracts\Support\Arrayable) {
                    return $item->toArray();
                }

                if (is_object($item)) {
                    return (array) $item;
                }

                return $item;
            });
    @endphp

    <div class="space-y-3">
        @forelse ($timeline as $item)
            <div @class([
                'rounded-xl border p-4 shadow-sm',
                'bg-slate-50 text-slate-600 border-slate-200' => ($item['scope'] ?? null) === 'source-material',
                'bg-white border-slate-200' => ($item['scope'] ?? null) !== 'source-material',
            ])>
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-1">
                        <p class="font-semibold text-base">
                            {{ $item['name'] ?? 'Processing Step' }}
                        </p>

                        @if (! empty($item['performed_at']))
                            <p class="text-xs uppercase tracking-wide text-slate-500">
                                {{ $item['performed_at'] }}
                            </p>
                        @endif
                    </div>

                    <span @class([
                        'px-3 py-1 text-xs font-semibold rounded-full uppercase tracking-wide',
                        'bg-slate-200 text-slate-700' => ($item['scope'] ?? null) === 'source-material',
                        'bg-primary-100 text-primary-800' => ($item['scope'] ?? null) !== 'source-material',
                    ])>
                        {{ ($item['scope'] ?? null) === 'source-material' ? 'Source Material' : 'Sample' }}
                    </span>
                </div>

                @if (! empty($item['content']))
                    <div class="mt-3 text-sm leading-relaxed">
                        {!! nl2br(e($item['content'])) !!}
                    </div>
                @endif

                @if (! empty($item['description']))
                    <div class="mt-3 text-xs italic text-slate-500">
                        {!! nl2br(e($item['description'])) !!}
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center text-sm text-slate-500">
                No processing steps recorded yet.
            </div>
        @endforelse
    </div>
</x-dynamic-component>
