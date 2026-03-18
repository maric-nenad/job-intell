<div class="space-y-6">

    {{-- Summary stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @php
            $statuses = \App\Models\Application::$statuses;
            $badgeColors = [
                'gray'   => 'bg-gray-100 text-gray-700',
                'teal'   => 'bg-teal-100 text-teal-700',
                'blue'   => 'bg-blue-100 text-blue-700',
                'yellow' => 'bg-yellow-100 text-yellow-700',
                'purple' => 'bg-purple-100 text-purple-700',
                'green'  => 'bg-green-100 text-green-700',
                'red'    => 'bg-red-100 text-red-700',
                'orange' => 'bg-orange-100 text-orange-700',
            ];
        @endphp
        @foreach($statuses as $key => $meta)
            @if(($counts[$key] ?? 0) > 0)
            <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 flex items-center justify-between">
                <span class="text-sm text-gray-600">{{ $meta['label'] }}</span>
                <span class="text-sm font-bold px-2 py-0.5 rounded-full {{ $badgeColors[$meta['color']] }}">
                    {{ $counts[$key] }}
                </span>
            </div>
            @endif
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <input wire:model.live.debounce.300ms="search"
               type="search"
               placeholder="Search company or position..."
               class="flex-1 rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" />
    </div>

    {{-- Application cards --}}
    @forelse($applications as $app)
    @php
        $offer  = $app->jobOffer;
        $status = $statuses[$app->status];
    @endphp
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Card header --}}
        <div class="flex items-start justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="font-semibold text-gray-900">{{ $offer->position }}</h3>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $offer->company }}
                    @if($offer->city || $offer->country)
                        · {{ collect([$offer->city, $offer->country])->filter()->implode(', ') }}
                    @endif
                    @if($offer->is_remote)
                        · <span class="text-blue-500">Remote</span>
                    @endif
                </p>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $badgeColors[$status['color']] }}">
                {{ $status['label'] }}
            </span>
        </div>

        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 gap-6">

            {{-- Job offer column --}}
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-3">Job Offer</h4>
                <dl class="space-y-1.5">
                    @if($offer->salary_min || $offer->salary_max)
                    <div class="flex gap-2">
                        <dt class="w-28 shrink-0 text-xs text-gray-500">Salary</dt>
                        <dd class="text-xs text-gray-800">
                            @if($offer->salary_min && $offer->salary_max)
                                {{ number_format($offer->salary_min) }} – {{ number_format($offer->salary_max) }} {{ $offer->salary_currency }}
                            @elseif($offer->salary_min)
                                From {{ number_format($offer->salary_min) }} {{ $offer->salary_currency }}
                            @else
                                Up to {{ number_format($offer->salary_max) }} {{ $offer->salary_currency }}
                            @endif
                        </dd>
                    </div>
                    @endif

                    @if($offer->posted_date)
                    <div class="flex gap-2">
                        <dt class="w-28 shrink-0 text-xs text-gray-500">Posted</dt>
                        <dd class="text-xs text-gray-800">{{ $offer->posted_date->format('M d, Y') }}</dd>
                    </div>
                    @endif

                    @if($offer->url)
                    <div class="flex gap-2">
                        <dt class="w-28 shrink-0 text-xs text-gray-500">URL</dt>
                        <dd class="text-xs">
                            <a href="{{ $offer->url }}" target="_blank" rel="noopener"
                               class="text-indigo-600 hover:underline truncate block max-w-xs">
                                {{ $offer->url }}
                            </a>
                        </dd>
                    </div>
                    @endif

                    @if($offer->notes)
                    <div class="flex gap-2">
                        <dt class="w-28 shrink-0 text-xs text-gray-500">Notes</dt>
                        <dd class="text-xs text-gray-800 line-clamp-3">{{ $offer->notes }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Progress column --}}
            <div class="space-y-4">

                {{-- Application --}}
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">Application</h4>
                    <dl class="space-y-1.5">
                        @if($app->applied_date)
                        <div class="flex gap-2">
                            <dt class="w-28 shrink-0 text-xs text-gray-500">Applied</dt>
                            <dd class="text-xs text-gray-800">{{ $app->applied_date->format('M d, Y') }}</dd>
                        </div>
                        @endif
                        @if($app->cv)
                        <div class="flex gap-2">
                            <dt class="w-28 shrink-0 text-xs text-gray-500">CV used</dt>
                            <dd class="text-xs text-gray-800">{{ $app->cv->name }}</dd>
                        </div>
                        @endif
                        @if($app->rejected_at)
                        <div class="flex gap-2">
                            <dt class="w-28 shrink-0 text-xs text-gray-500">Rejected</dt>
                            <dd class="text-xs text-red-600 font-medium">{{ $app->rejected_at->format('M d, Y') }}</dd>
                        </div>
                        @endif
                        @if($app->withdrawn_at)
                        <div class="flex gap-2">
                            <dt class="w-28 shrink-0 text-xs text-gray-500">Withdrawn</dt>
                            <dd class="text-xs text-orange-600 font-medium">{{ $app->withdrawn_at->format('M d, Y') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                {{-- Screening --}}
                @if($app->screening_time || $app->screening_contact)
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-wide text-yellow-600 mb-2">Screening</h4>
                    <dl class="space-y-1.5">
                        @if($app->screening_time)
                        <div class="flex gap-2">
                            <dt class="w-28 shrink-0 text-xs text-gray-500">Date & time</dt>
                            <dd class="text-xs text-gray-800">{{ $app->screening_time->format('M d, Y · H:i') }}</dd>
                        </div>
                        @endif
                        @if($app->screening_contact)
                        <div class="flex gap-2">
                            <dt class="w-28 shrink-0 text-xs text-gray-500">Contact</dt>
                            <dd class="text-xs text-gray-800">{{ $app->screening_contact }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
                @endif

                {{-- Interview --}}
                @if($app->interview_time || $app->interview_contact || !empty($app->interview_interviewers))
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-wide text-purple-600 mb-2">Interview</h4>
                    <dl class="space-y-1.5">
                        @if($app->interview_time)
                        <div class="flex gap-2">
                            <dt class="w-28 shrink-0 text-xs text-gray-500">Date & time</dt>
                            <dd class="text-xs text-gray-800">{{ $app->interview_time->format('M d, Y · H:i') }}</dd>
                        </div>
                        @endif
                        @if($app->interview_contact)
                        <div class="flex gap-2">
                            <dt class="w-28 shrink-0 text-xs text-gray-500">Contact</dt>
                            <dd class="text-xs text-gray-800">{{ $app->interview_contact }}</dd>
                        </div>
                        @endif
                        @if(!empty($app->interview_interviewers))
                        <div class="flex gap-2">
                            <dt class="w-28 shrink-0 text-xs text-gray-500">Interviewers</dt>
                            <dd class="flex flex-wrap gap-1">
                                @foreach($app->interview_interviewers as $name)
                                    <span class="px-1.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">{{ $name }}</span>
                                @endforeach
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
                @endif

                {{-- Offer --}}
                @if($app->offer_salary || $app->offer_benefits)
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-wide text-green-600 mb-2">Offer</h4>
                    <dl class="space-y-1.5">
                        @if($app->offer_salary)
                        <div class="flex gap-2">
                            <dt class="w-28 shrink-0 text-xs text-gray-500">Salary</dt>
                            <dd class="text-xs text-gray-800">{{ $app->offer_salary }}</dd>
                        </div>
                        @endif
                        @if($app->offer_benefits)
                        <div class="flex gap-2">
                            <dt class="w-28 shrink-0 text-xs text-gray-500">Benefits</dt>
                            <dd class="text-xs text-gray-800 whitespace-pre-line">{{ $app->offer_benefits }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
                @endif

            </div>
        </div>

        {{-- Notes footer --}}
        @if($app->notes)
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100">
            <span class="text-xs text-gray-500 font-medium">Notes: </span>
            <span class="text-xs text-gray-700">{{ $app->notes }}</span>
        </div>
        @endif

    </div>
    @empty
    <div class="text-center py-16 text-gray-400 text-sm">No applications tracked yet.</div>
    @endforelse

</div>
