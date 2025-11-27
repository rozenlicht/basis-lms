<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Recent Activity
        </x-slot>

        <x-slot name="description">
            Latest actions in the system
        </x-slot>

        <div class="relative">
            @forelse($this->getViewData()['events'] as $event)
                @php
                    $colorClasses = [
                        'primary' => 'bg-primary-500 dark:bg-primary-600 text-white border-primary-600 dark:border-primary-700',
                        'success' => 'bg-success-500 dark:bg-success-600 text-white border-success-600 dark:border-success-700',
                        'warning' => 'bg-warning-500 dark:bg-warning-600 text-white border-warning-600 dark:border-warning-700',
                        'info' => 'bg-info-500 dark:bg-info-600 text-white border-info-600 dark:border-info-700',
                        'gray' => 'bg-gray-500 dark:bg-gray-600 text-white border-gray-600 dark:border-gray-700',
                    ];
                    $iconBgClass = $colorClasses[$event['color']] ?? $colorClasses['gray'];
                @endphp
                <div class="relative flex gap-6 pb-6 last:pb-0">
                    <!-- Timeline line and icon -->
                    <div class="flex flex-col items-center flex-shrink-0">
                        <div class="relative z-10 flex h-12 w-12 items-center justify-center rounded-full border-2 {{ $iconBgClass }} shadow-lg">
                            @svg($event['icon'], 'h-6 w-6')
                        </div>
                        @if(!$loop->last)
                            <div class="absolute top-12 left-1/2 -translate-x-1/2 w-0.5 h-full bg-gradient-to-b from-gray-300 via-gray-200 to-transparent dark:from-gray-600 dark:via-gray-700"></div>
                        @endif
                    </div>

                    <!-- Content Card -->
                    <div class="flex-1 min-w-0 pt-1">
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow p-4">
                            <div class="flex items-start justify-between gap-4 mb-2">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-semibold text-gray-900 dark:text-white">
                                            {{ $event['user'] }}
                                        </span>
                                        <span class="text-gray-600 dark:text-gray-300">
                                            {{ $event['description'] }}
                                        </span>
                                        @if($event['subject'])
                                            <a 
                                                href="{{ $event['subject']['url'] }}" 
                                                class="inline-flex items-center gap-1 text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 hover:underline font-medium transition-colors"
                                            >
                                                {{ $event['subject']['label'] }}
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <time 
                                    datetime="{{ $event['time']->toIso8601String() }}" 
                                    class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap flex-shrink-0"
                                >
                                    {{ $event['time']->format('M j, Y') }}
                                    <span class="block text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">
                                        {{ $event['time']->format('g:i A') }}
                                    </span>
                                </time>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $event['time_ago'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No recent activity to display.</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Activity will appear here as users interact with the system.</p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

