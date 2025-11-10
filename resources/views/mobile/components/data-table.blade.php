@php
    $level = $level ?? 0;
    $bg = match (true) {
        $level === 0 => 'bg-white',
        $level === 1 => 'bg-slate-50',
        default => 'bg-slate-100',
    };

    $formatValue = function ($value) {
        if (is_null($value)) {
            return '—';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_numeric($value)) {
            return is_float($value) ? rtrim(rtrim(number_format($value, 4, '.', ''), '0'), '.') : $value;
        }

        return (string) $value;
    };
@endphp

<table class="w-full border-separate border-spacing-y-1 text-left {{ $level === 0 ? 'text-sm' : 'text-xs' }}">
    <tbody>
        @foreach ($data as $key => $value)
            <tr class="{{ $bg }} rounded-xl shadow-sm">
                <td class="w-1/3 min-w-[110px] rounded-l-xl px-3 py-2 font-semibold text-slate-700 align-top">
                    {{ $key }}
                </td>
                <td class="rounded-r-xl px-3 py-2 text-slate-600 align-top">
                    @if (is_array($value))
                        @include('mobile.components.data-table', ['data' => $value, 'level' => $level + 1])
                    @else
                        <span>{{ $formatValue($value) }}</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
