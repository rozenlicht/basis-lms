<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Starred Items
        </x-slot>

        <x-slot name="description">
            Your starred source materials and samples
        </x-slot>

        @if($this->getViewData()['hasItems'])
            <div class="space-y-6">
                @if($this->getViewData()['starredSourceMaterials']->isNotEmpty())
                    <div>
                        <h3 class="text-lg font-semibold mb-3 flex items-center gap-2 text-gray-900 dark:text-white">
                            @svg('heroicon-o-inbox-arrow-down', 'w-5 h-5')
                            Source Materials
                        </h3>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reference</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Grade</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Supplier ID</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Samples</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($this->getViewData()['starredSourceMaterials']->groupBy('grade') as $grade => $items)
                                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                                            <td colspan="5" class="px-4 py-2 font-semibold text-sm text-gray-700 dark:text-gray-300">
                                                Grade: {{ $grade }}
                                            </td>
                                        </tr>
                                        @foreach($items as $item)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                    <a href="{{ $item['url'] }}" target="_blank" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium">
                                                        {{ $item['reference'] }}
                                                    </a>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    {{ $item['name'] }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                    @if($item['grade'] && $item['grade'] !== 'No Grade')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-info-100 text-info-800 dark:bg-info-900 dark:text-info-200">
                                                            {{ $item['grade'] }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $item['supplier_identifier'] }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200">
                                                        {{ $item['samples_count'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if($this->getViewData()['starredSamples']->isNotEmpty())
                    <div>
                        <h3 class="text-lg font-semibold mb-3 flex items-center gap-2 text-gray-900 dark:text-white">
                            @svg('heroicon-o-puzzle-piece', 'w-5 h-5')
                            Samples
                        </h3>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reference</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Source Material</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Grade</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Supplier ID</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($this->getViewData()['starredSamples']->groupBy('grade') as $grade => $items)
                                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                                            <td colspan="4" class="px-4 py-2 font-semibold text-sm text-gray-700 dark:text-gray-300">
                                                Grade: {{ $grade }}
                                            </td>
                                        </tr>
                                        @foreach($items as $item)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                    <a href="{{ $item['url'] }}" target="_blank" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium">
                                                        {{ $item['reference'] }}
                                                    </a>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    {{ $item['name'] }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                    @if($item['grade'] && $item['grade'] !== 'No Grade')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-info-100 text-info-800 dark:bg-info-900 dark:text-info-200">
                                                            {{ $item['grade'] }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $item['supplier_identifier'] }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <x-filament::empty-state
                heading="No starred items"
                description="Star source materials or samples to see them here"
                icon="heroicon-o-star"
            />
        @endif
    </x-filament::section>
</x-filament-widgets::widget>

