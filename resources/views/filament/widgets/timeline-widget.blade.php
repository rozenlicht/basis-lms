<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Recent Activity
        </x-slot>

        <x-slot name="description">
            Latest actions in the system
        </x-slot>

        <div class="space-y-4">
            @forelse($this->getViewData()['events'] as $event)
                @php
                    $colorClasses = [
                        'primary' => 'bg-primary-100 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400',
                        'success' => 'bg-success-100 dark:bg-success-900/20 text-success-600 dark:text-success-400',
                        'warning' => 'bg-warning-100 dark:bg-warning-900/20 text-warning-600 dark:text-warning-400',
                        'info' => 'bg-info-100 dark:bg-info-900/20 text-info-600 dark:text-info-400',
                        'gray' => 'bg-gray-100 dark:bg-gray-900/20 text-gray-600 dark:text-gray-400',
                    ];
                    $iconBgClass = $colorClasses[$event['color']] ?? $colorClasses['gray'];
                @endphp
                <div class="flex gap-4 group">
                    <!-- Timeline line and icon -->
                    <div class="flex flex-col items-center">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full {{ $iconBgClass }}">
                            @svg($event['icon'], 'h-5 w-5')
                        </div>
                        @if(!$loop->last)
                            <div class="h-full w-0.5 bg-gray-200 dark:bg-gray-700 mt-2"></div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0 pb-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $event['user'] }}
                                    <span class="text-gray-500 dark:text-gray-400">
                                        {{ $event['description'] }}
                                    </span>
                                    @if($event['subject'])
                                        <a 
                                            href="{{ $event['subject']['url'] }}" 
                                            class="text-primary-600 dark:text-primary-400 hover:underline font-medium"
                                        >
                                            {{ $event['subject']['label'] }}
                                        </a>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $event['time_ago'] }}
                                </p>
                            </div>
                            <time 
                                datetime="{{ $event['time']->toIso8601String() }}" 
                                class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap"
                            >
                                {{ $event['time']->format('M j, Y g:i A') }}
                            </time>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>No recent activity to display.</p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

