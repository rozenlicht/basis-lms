<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Recent Activity
        </x-slot>

        <x-slot name="description">
            Latest actions in the system
        </x-slot>

        <div class="relative pl-4">
            @forelse($this->getViewData()['events'] as $event)
                @php
                    $colorClasses = [
                        'primary' => 'bg-primary-500 dark:bg-primary-600 text-white ring-primary-200 dark:ring-primary-800',
                        'success' => 'bg-success-500 dark:bg-success-600 text-white ring-success-200 dark:ring-success-800',
                        'warning' => 'bg-warning-500 dark:bg-warning-600 text-white ring-warning-200 dark:ring-warning-800',
                        'info' => 'bg-info-500 dark:bg-info-600 text-white ring-info-200 dark:ring-info-800',
                        'gray' => 'bg-gray-500 dark:bg-gray-600 text-white ring-gray-200 dark:ring-gray-800',
                    ];
                    $iconBgClass = $colorClasses[$event['color']] ?? $colorClasses['gray'];
                @endphp
                <div class="relative flex gap-5 pb-8 last:pb-0 group">
                    <!-- Timeline line and icon -->
                    <div class="relative flex flex-col items-center flex-shrink-0">
                        <!-- Vertical line -->
                        @if(!$loop->last)
                            <div class="absolute top-10 left-1/2 -translate-x-1/2 w-px h-full bg-gray-200 dark:bg-gray-700"></div>
                        @endif
                        
                        <!-- Icon circle -->
                        <div class="relative z-10 flex h-10 w-10 items-center justify-center rounded-full {{ $iconBgClass }} ring-4 ring-white dark:ring-gray-900 shadow-md group-hover:scale-110 transition-transform duration-200">
                            @svg($event['icon'], 'h-5 w-5')
                        </div>
                    </div>

                    <!-- Content Card -->
                    <div class="flex-1 min-w-0 pt-0.5 pb-2">
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-200 overflow-hidden">
                            <div class="p-5">
                                <!-- Main content row -->
                                <div class="flex items-start justify-between gap-4 mb-3">
                                    <div class="flex-1 min-w-0 space-y-1.5">
                                        <!-- User and action -->
                                        <div class="flex items-baseline gap-2 flex-wrap">
                                            <span class="font-semibold text-base text-gray-900 dark:text-white">
                                                {{ $event['user'] }}
                                            </span>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $event['description'] }}
                                            </span>
                                        </div>
                                        
                                        <!-- Subject link -->
                                        @if($event['subject'])
                                            <div class="flex items-center gap-1.5">
                                                <a 
                                                    href="{{ $event['subject']['url'] }}" 
                                                    class="inline-flex items-center gap-1.5 text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium transition-colors group/link"
                                                >
                                                    <span class="truncate max-w-md">{{ $event['subject']['label'] }}</span>
                                                    <svg class="w-3.5 h-3.5 flex-shrink-0 opacity-0 group-hover/link:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Date/time badge -->
                                    <div class="flex-shrink-0 text-right">
                                        <time 
                                            datetime="{{ $event['time']->toIso8601String() }}" 
                                            class="inline-flex flex-col items-end bg-gray-50 dark:bg-gray-900/50 rounded-lg px-3 py-2 border border-gray-200 dark:border-gray-700"
                                        >
                                            <span class="text-xs font-medium text-gray-900 dark:text-gray-100">
                                                {{ $event['time']->format('M j, Y') }}
                                            </span>
                                            <span class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">
                                                {{ $event['time']->format('g:i A') }}
                                            </span>
                                        </time>
                                    </div>
                                </div>
                                
                                <!-- Time ago -->
                                <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 pt-2 border-t border-gray-100 dark:border-gray-700/50">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ $event['time_ago'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 dark:bg-gray-800/50 border-2 border-dashed border-gray-300 dark:border-gray-700 mb-5">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">No recent activity</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Activity will appear here as users interact with the system.</p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

