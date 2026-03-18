<div>
    <div
        x-show="$wire.show"
        @open-application-detail.window="$wire.load($event.detail.applicationId)"
        @keydown.escape.window="$wire.close()"
        style="display:none"
        class="fixed inset-0 z-40"
    >
        {{-- Backdrop --}}
        <div
            class="absolute inset-0 bg-black/40"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="$wire.close()"
        ></div>

        {{-- Panel --}}
        <div
            class="absolute inset-y-0 right-0 w-full max-w-2xl bg-white shadow-2xl flex flex-col"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
        >
            @if($application)
            @php
                $offer  = $application->jobOffer;
                $status = \App\Models\Application::$statuses[$application->status];

                $statusColors = [
                    'gray'   => 'bg-gray-100 text-gray-700',
                    'teal'   => 'bg-teal-100 text-teal-700',
                    'blue'   => 'bg-blue-100 text-blue-700',
                    'yellow' => 'bg-yellow-100 text-yellow-700',
                    'purple' => 'bg-purple-100 text-purple-700',
                    'green'  => 'bg-green-100 text-green-700',
                    'red'    => 'bg-red-100 text-red-700',
                    'orange' => 'bg-orange-100 text-orange-700',
                ];

                $pipelineStages = ['saved', 'preparation', 'applied', 'screening', 'interview', 'offer'];
                $terminalStatuses = ['rejected', 'withdrawn'];
                $isTerminal = in_array($application->status, $terminalStatuses);
                $currentPipelineIndex = array_search($application->status, $pipelineStages);

                $stageColors = [
                    'saved'       => ['dot' => 'bg-gray-400',   'text' => 'text-gray-600'],
                    'preparation' => ['dot' => 'bg-teal-500',   'text' => 'text-teal-700'],
                    'applied'     => ['dot' => 'bg-blue-500',   'text' => 'text-blue-700'],
                    'screening'   => ['dot' => 'bg-yellow-500', 'text' => 'text-yellow-700'],
                    'interview'   => ['dot' => 'bg-purple-500', 'text' => 'text-purple-700'],
                    'offer'       => ['dot' => 'bg-green-500',  'text' => 'text-green-700'],
                ];
            @endphp

            {{-- Header --}}
            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 shrink-0">
                <div class="min-w-0 flex-1 mr-4">
                    <h2 class="text-lg font-semibold text-gray-900 truncate">{{ $offer->position }}</h2>
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
                <div class="flex items-center gap-3 shrink-0">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$status['color']] }}">
                        {{ $status['label'] }}
                    </span>
                    <button wire:click="close" class="text-gray-400 hover:text-gray-700 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Pipeline progress --}}
            @if(!$isTerminal)
            <div class="px-6 py-4 border-b border-gray-100 shrink-0 bg-gray-50">
                <div class="flex items-center">
                    @foreach($pipelineStages as $i => $stage)
                        @php
                            $reached   = $currentPipelineIndex !== false && $i <= $currentPipelineIndex;
                            $isCurrent = $i === $currentPipelineIndex;
                            $sc        = $stageColors[$stage];
                        @endphp
                        <div class="flex flex-col items-center {{ $i < count($pipelineStages) - 1 ? 'flex-1' : '' }}">
                            <div class="flex items-center w-full">
                                <div class="w-3 h-3 rounded-full {{ $reached ? $sc['dot'] : 'bg-gray-200' }} {{ $isCurrent ? 'ring-2 ring-offset-2 ring-current ' . $sc['text'] : '' }} transition-all"></div>
                                @if($i < count($pipelineStages) - 1)
                                    <div class="flex-1 h-0.5 mx-1 {{ $currentPipelineIndex !== false && $i < $currentPipelineIndex ? $sc['dot'] : 'bg-gray-200' }} transition-all"></div>
                                @endif
                            </div>
                            <span class="text-xs mt-1.5 whitespace-nowrap {{ $isCurrent ? 'font-semibold ' . $sc['text'] : ($reached ? 'text-gray-500' : 'text-gray-300') }}">
                                {{ \App\Models\Application::$statuses[$stage]['label'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="px-6 py-3 border-b border-gray-100 shrink-0 bg-gray-50">
                <span class="text-sm text-gray-500">
                    @if($application->status === 'rejected') Application was rejected. @else Application was withdrawn. @endif
                </span>
            </div>
            @endif

            {{-- Scrollable body --}}
            <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5">

                <div class="grid grid-cols-2 gap-4">

                    {{-- Job Offer --}}
                    <section class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-3">Job Offer</h3>
                        <dl class="space-y-2">
                            @if($offer->salary_min || $offer->salary_max)
                            <div>
                                <dt class="text-xs text-gray-400">Salary</dt>
                                <dd class="text-sm font-medium text-gray-800 mt-0.5">
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

                            <div>
                                <dt class="text-xs text-gray-400">Type</dt>
                                <dd class="text-sm text-gray-800 mt-0.5">{{ $offer->is_remote ? 'Remote' : 'Onsite' }}</dd>
                            </div>

                            @if($offer->posted_date)
                            <div>
                                <dt class="text-xs text-gray-400">Posted</dt>
                                <dd class="text-sm text-gray-800 mt-0.5">{{ $offer->posted_date->format('M d, Y') }}</dd>
                            </div>
                            @endif

                            @if($offer->url)
                            <div>
                                <dt class="text-xs text-gray-400">URL</dt>
                                <dd class="mt-0.5">
                                    <a href="{{ $offer->url }}" target="_blank" rel="noopener"
                                       class="text-xs text-indigo-600 hover:underline break-all">{{ $offer->url }}</a>
                                </dd>
                            </div>
                            @endif

                            @if($offer->company_rating)
                            <div>
                                <dt class="text-xs text-gray-400">Company Rating</dt>
                                <dd class="mt-0.5 flex items-center gap-1">
                                    @php $stars = round($offer->company_rating * 2) / 2; @endphp
                                    @for($s = 1; $s <= 5; $s++)
                                        @if($s <= floor($stars))
                                            <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @else
                                            <svg class="w-3.5 h-3.5 text-gray-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endif
                                    @endfor
                                    <span class="text-xs text-gray-600 font-medium">{{ number_format($offer->company_rating, 1) }}</span>
                                    @if($offer->company_rating_source)
                                        <span class="text-xs text-gray-400">· {{ $offer->company_rating_source }}</span>
                                    @endif
                                </dd>
                            </div>
                            @endif

                            @if($offer->notes)
                            <div>
                                <dt class="text-xs text-gray-400">Notes</dt>
                                <dd class="text-xs text-gray-700 mt-0.5 whitespace-pre-line">{{ $offer->notes }}</dd>
                            </div>
                            @endif
                        </dl>
                    </section>

                    {{-- Application summary --}}
                    <section class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-3">Application</h3>
                        <dl class="space-y-2">
                            @if($application->applied_date)
                            <div>
                                <dt class="text-xs text-gray-400">Applied</dt>
                                <dd class="text-sm text-gray-800 mt-0.5">{{ $application->applied_date->format('M d, Y') }}</dd>
                            </div>
                            @endif

                            @if($application->cv)
                            <div>
                                <dt class="text-xs text-gray-400">CV used</dt>
                                <dd class="text-sm text-gray-800 mt-0.5">{{ $application->cv->name }}</dd>
                            </div>
                            @endif

                            @if($application->rejected_at)
                            <div>
                                <dt class="text-xs text-gray-400">Rejected</dt>
                                <dd class="text-sm text-red-600 font-medium mt-0.5">{{ $application->rejected_at->format('M d, Y') }}</dd>
                            </div>
                            @endif

                            @if($application->withdrawn_at)
                            <div>
                                <dt class="text-xs text-gray-400">Withdrawn</dt>
                                <dd class="text-sm text-orange-600 font-medium mt-0.5">{{ $application->withdrawn_at->format('M d, Y') }}</dd>
                            </div>
                            @endif

                            @if(!$application->applied_date && !$application->cv && !$application->rejected_at && !$application->withdrawn_at)
                            <p class="text-xs text-gray-400 italic">No details recorded yet.</p>
                            @endif
                        </dl>
                    </section>
                </div>

                @php
                    $stageIndex = fn($s) => array_search($s, $pipelineStages);
                    $reached    = fn($s) => !$isTerminal && $currentPipelineIndex !== false && $currentPipelineIndex >= $stageIndex($s);
                @endphp

                {{-- Screening --}}
                @if($application->screening_time || $application->screening_contact || $reached('screening'))
                <section class="rounded-xl p-4 border border-yellow-200 bg-yellow-50">
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-yellow-700 mb-3">Screening</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-xs text-yellow-600">Date & time</dt>
                            <dd class="text-sm mt-0.5 {{ $application->screening_time ? 'text-gray-800 font-medium' : 'text-gray-400 italic' }}">
                                {{ $application->screening_time ? $application->screening_time->format('M d, Y · H:i') : 'Not set' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-yellow-600">Contact</dt>
                            <dd class="text-sm mt-0.5 {{ $application->screening_contact ? 'text-gray-800 font-medium' : 'text-gray-400 italic' }}">
                                {{ $application->screening_contact ?: 'Not set' }}
                            </dd>
                        </div>
                    </div>
                </section>
                @endif

                {{-- Interview --}}
                @if($application->interview_time || $application->interview_contact || !empty($application->interview_interviewers) || $reached('interview'))
                <section class="rounded-xl p-4 border border-purple-200 bg-purple-50">
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-purple-700 mb-3">Interview</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-xs text-purple-600">Date & time</dt>
                            <dd class="text-sm mt-0.5 {{ $application->interview_time ? 'text-gray-800 font-medium' : 'text-gray-400 italic' }}">
                                {{ $application->interview_time ? $application->interview_time->format('M d, Y · H:i') : 'Not set' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-purple-600">Contact</dt>
                            <dd class="text-sm mt-0.5 {{ $application->interview_contact ? 'text-gray-800 font-medium' : 'text-gray-400 italic' }}">
                                {{ $application->interview_contact ?: 'Not set' }}
                            </dd>
                        </div>
                    </div>
                    @if(!empty($application->interview_interviewers))
                    <div class="mt-3">
                        <dt class="text-xs text-purple-600 mb-1.5">Interviewers</dt>
                        <dd class="flex flex-wrap gap-1.5">
                            @foreach($application->interview_interviewers as $name)
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">{{ $name }}</span>
                            @endforeach
                        </dd>
                    </div>
                    @endif
                </section>
                @endif

                {{-- Offer --}}
                @if($application->offer_salary || $application->offer_benefits || $reached('offer'))
                <section class="rounded-xl p-4 border border-green-200 bg-green-50">
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-green-700 mb-3">Offer Received</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-xs text-green-600">Salary / Compensation</dt>
                            <dd class="text-sm mt-0.5 {{ $application->offer_salary ? 'text-gray-800 font-medium' : 'text-gray-400 italic' }}">
                                {{ $application->offer_salary ?: 'Not set' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-green-600">Benefits</dt>
                            <dd class="text-sm mt-0.5 {{ $application->offer_benefits ? 'text-gray-800 whitespace-pre-line' : 'text-gray-400 italic' }}">
                                {{ $application->offer_benefits ?: 'Not set' }}
                            </dd>
                        </div>
                    </div>
                </section>
                @endif

                {{-- Notes --}}
                @if($application->notes)
                <section class="rounded-xl p-4 border border-gray-200 bg-gray-50">
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">Notes</h3>
                    <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $application->notes }}</p>
                </section>
                @endif

            </div>

            {{-- Footer --}}
            <div class="shrink-0 px-6 py-4 border-t border-gray-100 flex justify-between items-center">
                <span class="text-xs text-gray-400">Last updated {{ $application->updated_at->diffForHumans() }}</span>
                <button wire:click="close"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                    Close
                </button>
            </div>

            @endif
        </div>
    </div>
</div>
