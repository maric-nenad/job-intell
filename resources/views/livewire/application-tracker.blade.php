<div>
    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <input wire:model.live.debounce.300ms="search"
               type="search"
               placeholder="Search company or position..."
               class="flex-1 rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" />

        <select wire:model.live="statusFilter" class="rounded-lg border-gray-300 shadow-sm text-sm">
            <option value="">All Stages</option>
            @foreach(App\Models\Application::$statuses as $key => $meta)
                <option value="{{ $key }}">{{ $meta['label'] }}</option>
            @endforeach
        </select>
    </div>

    {{-- Pipeline columns --}}
    <div class="flex gap-4 overflow-x-auto pb-4">
        @php
            $colColors = [
                'gray'   => ['header' => 'bg-gray-100 text-gray-600',   'badge' => 'bg-gray-200 text-gray-700',   'card' => 'border-gray-200'],
                'teal'   => ['header' => 'bg-teal-50 text-teal-700',    'badge' => 'bg-teal-100 text-teal-700',   'card' => 'border-teal-200'],
                'blue'   => ['header' => 'bg-blue-50 text-blue-700',    'badge' => 'bg-blue-100 text-blue-700',   'card' => 'border-blue-200'],
                'yellow' => ['header' => 'bg-yellow-50 text-yellow-700','badge' => 'bg-yellow-100 text-yellow-700','card' => 'border-yellow-200'],
                'purple' => ['header' => 'bg-purple-50 text-purple-700','badge' => 'bg-purple-100 text-purple-700','card' => 'border-purple-200'],
                'green'  => ['header' => 'bg-green-50 text-green-700',  'badge' => 'bg-green-100 text-green-700', 'card' => 'border-green-200'],
                'red'    => ['header' => 'bg-red-50 text-red-700',      'badge' => 'bg-red-100 text-red-700',     'card' => 'border-red-200'],
                'orange' => ['header' => 'bg-orange-50 text-orange-700','badge' => 'bg-orange-100 text-orange-700','card' => 'border-orange-200'],
            ];
        @endphp

        @foreach($pipeline as $statusKey => $column)
            @php $c = $colColors[$column['meta']['color']]; @endphp
            <div class="shrink-0 w-64">
                {{-- Column header --}}
                <div class="flex items-center justify-between px-3 py-2 rounded-lg mb-3 {{ $c['header'] }}">
                    <span class="text-xs font-semibold uppercase tracking-wide">{{ $column['meta']['label'] }}</span>
                    <span class="text-xs font-bold px-1.5 py-0.5 rounded-full {{ $c['badge'] }}">
                        {{ $column['applications']->count() }}
                    </span>
                </div>

                {{-- Cards --}}
                <div class="space-y-3 min-h-20">
                    @forelse($column['applications'] as $app)
                        <div @click="$dispatch('open-application-detail', { applicationId: {{ $app->id }} })"
                             class="bg-white rounded-xl border shadow-sm p-4 {{ $c['card'] }} cursor-pointer hover:shadow-md transition-shadow">
                            <div class="font-semibold text-gray-800 text-sm leading-tight">{{ $app->jobOffer->position }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ $app->jobOffer->company }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">
                                {{ collect([$app->jobOffer->city, $app->jobOffer->country])->filter()->implode(', ') }}
                                @if($app->jobOffer->is_remote)
                                    · <span class="text-blue-500">Remote</span>
                                @endif
                            </div>


                            @php
                                $since = $app->status_changed_at ?? $app->created_at;
                                $days  = (int) $since->diffInDays(now());
                                $ageBadge = match(true) {
                                    $days <= 3  => 'bg-green-100 text-green-700',
                                    $days <= 7  => 'bg-yellow-100 text-yellow-700',
                                    default     => 'bg-red-100 text-red-700',
                                };
                                $ageLabel = $days === 0 ? 'Today' : ($days === 1 ? '1 day' : "{$days} days");
                            @endphp
                            <div class="mt-2 flex items-center gap-1">
                                <span class="inline-flex items-center gap-1 text-xs font-medium px-1.5 py-0.5 rounded-full {{ $ageBadge }}">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $ageLabel }}
                                </span>
                            </div>

                            @if($app->cv)
                                <div class="mt-2 flex items-center gap-1 text-xs text-gray-400">
                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span class="truncate">{{ $app->cv->name }}</span>
                                </div>
                            @endif

                            @if($app->rejected_at)
                                <div class="mt-2 text-xs text-red-500 font-medium">Rejected {{ $app->rejected_at->format('M d, Y') }}</div>
                            @endif
                            @if($app->withdrawn_at)
                                <div class="mt-2 text-xs text-orange-500 font-medium">Withdrawn {{ $app->withdrawn_at->format('M d, Y') }}</div>
                            @endif

                            @if($app->notes)
                                <p class="mt-2 text-xs text-gray-500 line-clamp-2">{{ $app->notes }}</p>
                            @endif

                            {{-- Actions --}}
                            <div class="mt-3 flex items-center justify-between">
                                <button @click.stop="$dispatch('open-application-modal', { jobOfferId: {{ $app->job_offer_id }} })"
                                        class="text-xs text-indigo-600 hover:text-indigo-900 font-medium px-2 py-1 rounded hover:bg-indigo-50 transition-colors">Edit</button>

                                <select @click.stop class="text-xs border-gray-200 rounded py-0.5 text-gray-600 cursor-pointer hover:border-gray-400 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-300 transition-colors"
                                        wire:change="updateStatus({{ $app->id }}, $event.target.value)">
                                    @foreach(App\Models\Application::$statuses as $key => $meta)
                                        <option value="{{ $key }}" {{ $app->status === $key ? 'selected' : '' }}>
                                            {{ $meta['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @empty
                        <div class="text-xs text-gray-300 text-center py-4">Empty</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    {{-- Notification toast --}}
    <div x-data="{ show: false, msg: '' }"
         x-on:notify.window="msg = $event.detail.message; show = true; setTimeout(() => show = false, 3000)"
         x-show="show"
         x-transition
         class="fixed bottom-5 right-5 bg-gray-800 text-white text-sm px-4 py-2 rounded-lg shadow-lg z-50">
        <span x-text="msg"></span>
    </div>
</div>
