<div>
    <div x-show="$wire.showModal"
         @open-application-modal.window="$wire.openForOffer($event.detail.jobOfferId)"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 flex items-center justify-center p-4 bg-black/50"
         style="display:none">

        <div @click.outside="$wire.close()" class="bg-white rounded-2xl shadow-xl w-full max-w-lg">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Track Application</h2>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $jobPosition }} · {{ $jobCompany }} · {{ $jobLocation }}</p>
                </div>
                <button wire:click="close" class="text-gray-400 hover:text-gray-700 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="px-6 py-5 space-y-4">

                {{-- Status pipeline --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($statuses as $key => $meta)
                            @php
                                $colors = [
                                    'gray'   => 'border-gray-300 bg-gray-50 text-gray-700',
                                    'teal'   => 'border-teal-400 bg-teal-50 text-teal-700',
                                    'blue'   => 'border-blue-400 bg-blue-50 text-blue-700',
                                    'yellow' => 'border-yellow-400 bg-yellow-50 text-yellow-700',
                                    'purple' => 'border-purple-400 bg-purple-50 text-purple-700',
                                    'green'  => 'border-green-400 bg-green-50 text-green-700',
                                    'red'    => 'border-red-400 bg-red-50 text-red-700',
                                    'orange' => 'border-orange-400 bg-orange-50 text-orange-700',
                                ];
                                $active = [
                                    'gray'   => 'ring-2 ring-gray-400',
                                    'teal'   => 'ring-2 ring-teal-400',
                                    'blue'   => 'ring-2 ring-blue-400',
                                    'yellow' => 'ring-2 ring-yellow-400',
                                    'purple' => 'ring-2 ring-purple-400',
                                    'green'  => 'ring-2 ring-green-400',
                                    'red'    => 'ring-2 ring-red-400',
                                    'orange' => 'ring-2 ring-orange-400',
                                ];
                            @endphp
                            <button type="button"
                                    wire:click="$set('status', '{{ $key }}')"
                                    class="px-2 py-2 rounded-lg border text-xs font-medium text-center transition {{ $colors[$meta['color']] }} {{ $status === $key ? $active[$meta['color']] : 'opacity-60' }}">
                                {{ $meta['label'] }}
                            </button>
                        @endforeach
                    </div>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- CV selector — shown when status requires it --}}
                @if(in_array($status, $cvRequiredStatuses))
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        CV Version *
                        <span class="font-normal text-gray-400 ml-1">— which CV did you send?</span>
                    </label>
                    @if($cvs->isEmpty())
                        <p class="text-sm text-amber-600 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                            No CVs uploaded yet.
                            <a href="{{ route('cvs.index') }}" class="underline font-medium">Upload one first →</a>
                        </p>
                    @else
                        <div class="grid grid-cols-1 gap-2">
                            @foreach($cvs as $cv)
                                <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition
                                    {{ $cv_id == $cv->id ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200 hover:border-gray-300' }}">
                                    <input type="radio" wire:model.live="cv_id" value="{{ $cv->id }}" class="text-indigo-600 focus:ring-indigo-500" />
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800">{{ $cv->name }}</p>
                                        @if($cv->description)
                                            <p class="text-xs text-gray-500">{{ $cv->description }}</p>
                                        @endif
                                        <p class="text-xs text-gray-400">{{ $cv->file_name }} · {{ $cv->file_size_formatted }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                    @error('cv_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

                @if(!in_array($status, ['saved', 'preparation']))
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Applied Date</label>
                    <input wire:model="applied_date" type="date" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                    @error('applied_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

                {{-- Interview fields --}}
                @if($status === 'interview' || $interview_time || $interview_contact || !empty($interview_interviewers))
                <div class="space-y-3 p-3 rounded-lg bg-purple-50 border border-purple-200">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-purple-800 mb-1">Interview Date & Time</label>
                            <input wire:model="interview_time" type="datetime-local" class="w-full rounded-lg border-purple-300 text-sm shadow-sm focus:ring-purple-500 focus:border-purple-500 bg-white" />
                            @error('interview_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-purple-800 mb-1">Contact</label>
                            <input wire:model="interview_contact" type="text" placeholder="Name, phone, or email" class="w-full rounded-lg border-purple-300 text-sm shadow-sm focus:ring-purple-500 focus:border-purple-500 bg-white" />
                            @error('interview_contact') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-800 mb-1">Interviewers</label>
                        @if(!empty($interview_interviewers))
                            <div class="flex flex-wrap gap-2 mb-2">
                                @foreach($interview_interviewers as $i => $name)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                        {{ $name }}
                                        <button type="button" wire:click="removeInterviewer({{ $i }})" class="text-purple-500 hover:text-purple-700 leading-none">&times;</button>
                                    </span>
                                @endforeach
                            </div>
                        @endif
                        <div class="flex gap-2">
                            <input wire:model="new_interviewer" wire:keydown.enter.prevent="addInterviewer" type="text" placeholder="Add interviewer name" class="flex-1 rounded-lg border-purple-300 text-sm shadow-sm focus:ring-purple-500 focus:border-purple-500 bg-white" />
                            <button type="button" wire:click="addInterviewer" class="px-3 py-1.5 text-sm font-medium text-purple-700 bg-white border border-purple-300 rounded-lg hover:bg-purple-50 transition">Add</button>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Offer fields --}}
                @if($status === 'offer' || $offer_salary || $offer_benefits)
                <div class="space-y-3 p-3 rounded-lg bg-green-50 border border-green-200">
                    <div>
                        <label class="block text-sm font-medium text-green-800 mb-1">Salary / Compensation</label>
                        <input wire:model="offer_salary" type="text" placeholder="e.g. $85,000/yr or €4,500/mo" class="w-full rounded-lg border-green-300 text-sm shadow-sm focus:ring-green-500 focus:border-green-500 bg-white" />
                        @error('offer_salary') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-green-800 mb-1">Benefits</label>
                        <textarea wire:model="offer_benefits" rows="3" placeholder="Health insurance, remote work, stock options..." class="w-full rounded-lg border-green-300 text-sm shadow-sm focus:ring-green-500 focus:border-green-500 bg-white"></textarea>
                        @error('offer_benefits') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                @endif

                {{-- Rejected date --}}
                @if($status === 'rejected' || $rejected_at)
                <div class="p-3 rounded-lg bg-red-50 border border-red-200">
                    <label class="block text-sm font-medium text-red-800 mb-1">Rejection Date</label>
                    <input wire:model="rejected_at" type="date" class="w-full rounded-lg border-red-300 text-sm shadow-sm focus:ring-red-500 focus:border-red-500 bg-white" />
                    @error('rejected_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

                {{-- Withdrawn date --}}
                @if($status === 'withdrawn' || $withdrawn_at)
                <div class="p-3 rounded-lg bg-orange-50 border border-orange-200">
                    <label class="block text-sm font-medium text-orange-800 mb-1">Withdrawal Date</label>
                    <input wire:model="withdrawn_at" type="date" class="w-full rounded-lg border-orange-300 text-sm shadow-sm focus:ring-orange-500 focus:border-orange-500 bg-white" />
                    @error('withdrawn_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

                {{-- Screening fields --}}
                @if($status === 'screening' || $screening_time || $screening_contact)
                <div class="grid grid-cols-2 gap-4 p-3 rounded-lg bg-yellow-50 border border-yellow-200">
                    <div>
                        <label class="block text-sm font-medium text-yellow-800 mb-1">Screening Date & Time</label>
                        <input wire:model="screening_time" type="datetime-local" class="w-full rounded-lg border-yellow-300 text-sm shadow-sm focus:ring-yellow-500 focus:border-yellow-500 bg-white" />
                        @error('screening_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-yellow-800 mb-1">Contact</label>
                        <input wire:model="screening_contact" type="text" placeholder="Name, phone, or email" class="w-full rounded-lg border-yellow-300 text-sm shadow-sm focus:ring-yellow-500 focus:border-yellow-500 bg-white" />
                        @error('screening_contact') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea wire:model="notes" rows="3" placeholder="Interview notes, contact details, follow-up actions..." class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

                <div class="flex justify-between items-center pt-2">
                    @if($applicationId)
                        <button type="button" wire:click="delete" wire:confirm="Remove this application from tracking?"
                                class="text-sm text-red-500 hover:text-red-700 font-medium px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                            Remove tracking
                        </button>
                    @else
                        <span></span>
                    @endif
                    <div class="flex gap-3">
                        <button type="button" wire:click="close" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:border-gray-400 hover:text-gray-900 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 active:bg-indigo-800 shadow-sm hover:shadow transition-all">
                            <span wire:loading.remove>Save</span>
                            <span wire:loading>Saving...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
