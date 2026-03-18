<div>
    <div
        x-show="$wire.show"
        @open-job-offer-detail.window="$wire.open($event.detail.jobOfferId)"
        @keydown.escape.window="$wire.close()"
        style="display:none"
        class="fixed inset-0 z-40"
    >
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/40" @click="$wire.close()"></div>

        {{-- Panel --}}
        <div class="absolute inset-y-0 right-0 w-full max-w-2xl bg-white shadow-xl flex flex-col overflow-y-auto">

            @if($offer)
                {{-- Header --}}
                <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $offer->position }}</h2>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $offer->company }}</p>
                    </div>
                    <button wire:click="close" class="text-gray-400 hover:text-gray-600 ml-4 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="flex-1 px-6 py-5 space-y-6">

                    {{-- Summary --}}
                    @if($offer->summary)
                        <div class="bg-indigo-50 rounded-lg px-4 py-3">
                            <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wide mb-1">Summary</p>
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $offer->summary }}</p>
                        </div>
                    @endif

                    {{-- Skills --}}
                    @if(!empty($offer->skills))
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">Required Skills</h3>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($offer->skills as $skill)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">{{ $skill }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Company Info --}}
                    @if($offer->company_valuation || $offer->company_employees || $offer->company_owners || $offer->company_rating)
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-3">Company</h3>
                            <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">

                                @if($offer->company_valuation)
                                    <div>
                                        <dt class="text-gray-500 font-medium">Valuation</dt>
                                        <dd class="text-gray-800 mt-0.5">{{ $offer->company_valuation }}</dd>
                                    </div>
                                @endif

                                @if($offer->company_employees)
                                    <div>
                                        <dt class="text-gray-500 font-medium">Employees</dt>
                                        <dd class="text-gray-800 mt-0.5">{{ $offer->company_employees }}</dd>
                                    </div>
                                @endif

                                @if($offer->company_owners)
                                    <div class="col-span-2">
                                        <dt class="text-gray-500 font-medium">Founders / Owners</dt>
                                        <dd class="text-gray-800 mt-0.5">{{ $offer->company_owners }}</dd>
                                    </div>
                                @endif

                                @if($offer->company_rating)
                                    <div class="col-span-2">
                                        <dt class="text-gray-500 font-medium mb-1">Rating</dt>
                                        <dd class="flex items-center gap-1.5">
                                            @php $stars = round($offer->company_rating * 2) / 2; @endphp
                                            @for($s = 1; $s <= 5; $s++)
                                                @if($s <= floor($stars))
                                                    <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                @elseif($stars - floor($stars) >= 0.5 && $s == ceil($stars))
                                                    <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 1l2.39 4.843 5.345.777-3.867 3.769.913 5.318L10 13.347V1z"/><path fill="#d1d5db" d="M10 1v12.347l-4.781 2.36.913-5.318L2.265 6.62l5.345-.777L10 1z"/></svg>
                                                @else
                                                    <svg class="w-4 h-4 text-gray-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                @endif
                                            @endfor
                                            <span class="text-sm font-medium text-gray-700">{{ number_format($offer->company_rating, 1) }}</span>
                                            @if($offer->company_rating_source)
                                                <span class="text-xs text-gray-400">· {{ $offer->company_rating_source }}</span>
                                            @endif
                                        </dd>
                                    </div>
                                @endif

                            </dl>
                        </div>
                    @endif

                    {{-- Job Offer Info --}}
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-3">Job Offer</h3>
                        <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">

                            <div>
                                <dt class="text-gray-500 font-medium">Location</dt>
                                <dd class="text-gray-800 mt-0.5">
                                    {{ collect([$offer->city, $offer->country])->filter()->implode(', ') ?: '—' }}
                                    @if($offer->is_remote)
                                        <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Remote</span>
                                    @endif
                                </dd>
                            </div>

                            <div>
                                <dt class="text-gray-500 font-medium">Status</dt>
                                <dd class="mt-0.5">
                                    @if($offer->status === 'open')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Open</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">Closed</span>
                                    @endif
                                </dd>
                            </div>

                            <div>
                                <dt class="text-gray-500 font-medium">Salary</dt>
                                <dd class="text-gray-800 mt-0.5">
                                    @if($offer->salary_min)
                                        {{ number_format($offer->salary_min) }}–{{ number_format($offer->salary_max ?? $offer->salary_min) }} {{ $offer->salary_currency }}
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </dd>
                            </div>

                            <div>
                                <dt class="text-gray-500 font-medium">Posted</dt>
                                <dd class="text-gray-800 mt-0.5">{{ $offer->posted_date?->format('M d, Y') ?? '—' }}</dd>
                            </div>

                            @if($offer->url)
                                <div class="col-span-2">
                                    <dt class="text-gray-500 font-medium">Link</dt>
                                    <dd class="mt-0.5">
                                        <a href="{{ $offer->url }}" target="_blank" class="text-indigo-600 hover:underline break-all">{{ $offer->url }}</a>
                                    </dd>
                                </div>
                            @endif

                            @if($offer->description)
                                <div class="col-span-2">
                                    <dt class="text-gray-500 font-medium">Description</dt>
                                    <dd class="text-gray-700 mt-0.5 whitespace-pre-line">{{ $offer->description }}</dd>
                                </div>
                            @endif

                            @if($offer->requirements)
                                <div class="col-span-2">
                                    <dt class="text-gray-500 font-medium">Requirements</dt>
                                    <dd class="text-gray-700 mt-0.5 whitespace-pre-line">{{ $offer->requirements }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    {{-- Application Tracking --}}
                    @if($application)
                        <div class="border-t border-gray-100 pt-5">
                            <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-3">My Application</h3>

                            @php
                                $badgeColors = [
                                    'gray'   => 'bg-gray-100 text-gray-600',
                                    'teal'   => 'bg-teal-100 text-teal-700',
                                    'blue'   => 'bg-blue-100 text-blue-700',
                                    'yellow' => 'bg-yellow-100 text-yellow-700',
                                    'purple' => 'bg-purple-100 text-purple-700',
                                    'green'  => 'bg-green-100 text-green-700',
                                    'red'    => 'bg-red-100 text-red-600',
                                    'orange' => 'bg-orange-100 text-orange-700',
                                ];
                                $statusMeta = App\Models\Application::$statuses[$application->status] ?? null;
                            @endphp

                            <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                                <div>
                                    <dt class="text-gray-500 font-medium">Stage</dt>
                                    <dd class="mt-0.5">
                                        @if($statusMeta)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeColors[$statusMeta['color']] }}">
                                                {{ $statusMeta['label'] }}
                                            </span>
                                        @endif
                                    </dd>
                                </div>

                                @if($application->cv)
                                    <div>
                                        <dt class="text-gray-500 font-medium">CV Used</dt>
                                        <dd class="text-gray-800 mt-0.5">{{ $application->cv->name }}</dd>
                                    </div>
                                @endif

                                @if($application->applied_date)
                                    <div>
                                        <dt class="text-gray-500 font-medium">Applied</dt>
                                        <dd class="text-gray-800 mt-0.5">{{ $application->applied_date->format('M d, Y') }}</dd>
                                    </div>
                                @endif
                            </dl>

                            {{-- Screening --}}
                            @if($application->screening_time || $application->screening_contact)
                                <div class="mt-4 bg-yellow-50 rounded-lg px-4 py-3">
                                    <p class="text-xs font-semibold text-yellow-700 uppercase tracking-wide mb-2">Screening</p>
                                    <dl class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                                        @if($application->screening_time)
                                            <div>
                                                <dt class="text-yellow-600 font-medium">Date & Time</dt>
                                                <dd class="text-gray-800 mt-0.5">{{ $application->screening_time->format('M d, Y H:i') }}</dd>
                                            </div>
                                        @endif
                                        @if($application->screening_contact)
                                            <div>
                                                <dt class="text-yellow-600 font-medium">Contact</dt>
                                                <dd class="text-gray-800 mt-0.5">{{ $application->screening_contact }}</dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>
                            @endif

                            {{-- Interview --}}
                            @if($application->interview_time || $application->interview_contact || !empty($application->interview_interviewers))
                                <div class="mt-4 bg-purple-50 rounded-lg px-4 py-3">
                                    <p class="text-xs font-semibold text-purple-700 uppercase tracking-wide mb-2">Interview</p>
                                    <dl class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                                        @if($application->interview_time)
                                            <div>
                                                <dt class="text-purple-600 font-medium">Date & Time</dt>
                                                <dd class="text-gray-800 mt-0.5">{{ $application->interview_time->format('M d, Y H:i') }}</dd>
                                            </div>
                                        @endif
                                        @if($application->interview_contact)
                                            <div>
                                                <dt class="text-purple-600 font-medium">Contact</dt>
                                                <dd class="text-gray-800 mt-0.5">{{ $application->interview_contact }}</dd>
                                            </div>
                                        @endif
                                        @if(!empty($application->interview_interviewers))
                                            <div class="col-span-2">
                                                <dt class="text-purple-600 font-medium">Interviewers</dt>
                                                <dd class="mt-1 flex flex-wrap gap-1">
                                                    @foreach($application->interview_interviewers as $name)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">{{ $name }}</span>
                                                    @endforeach
                                                </dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>
                            @endif

                            {{-- Offer --}}
                            @if($application->offer_salary || $application->offer_benefits)
                                <div class="mt-4 bg-green-50 rounded-lg px-4 py-3">
                                    <p class="text-xs font-semibold text-green-700 uppercase tracking-wide mb-2">Offer</p>
                                    <dl class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                                        @if($application->offer_salary)
                                            <div>
                                                <dt class="text-green-600 font-medium">Salary</dt>
                                                <dd class="text-gray-800 mt-0.5">{{ $application->offer_salary }}</dd>
                                            </div>
                                        @endif
                                        @if($application->offer_benefits)
                                            <div class="col-span-2">
                                                <dt class="text-green-600 font-medium">Benefits</dt>
                                                <dd class="text-gray-700 mt-0.5 whitespace-pre-line">{{ $application->offer_benefits }}</dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>
                            @endif

                            {{-- Rejected --}}
                            @if($application->rejected_at)
                                <div class="mt-4 bg-red-50 rounded-lg px-4 py-3">
                                    <p class="text-xs font-semibold text-red-700 uppercase tracking-wide mb-1">Rejected</p>
                                    <p class="text-sm text-gray-800">{{ $application->rejected_at->format('M d, Y') }}</p>
                                </div>
                            @endif

                            {{-- Withdrawn --}}
                            @if($application->withdrawn_at)
                                <div class="mt-4 bg-orange-50 rounded-lg px-4 py-3">
                                    <p class="text-xs font-semibold text-orange-700 uppercase tracking-wide mb-1">Withdrawn</p>
                                    <p class="text-sm text-gray-800">{{ $application->withdrawn_at->format('M d, Y') }}</p>
                                </div>
                            @endif

                            {{-- Notes --}}
                            @if($application->notes)
                                <div class="mt-4">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Notes</p>
                                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $application->notes }}</p>
                                </div>
                            @endif
                        </div>
                    @endif

                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
                    <button wire:click="close"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Close
                    </button>
                </div>
            @endif

        </div>
    </div>
</div>
