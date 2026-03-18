<div>
    <div x-show="$wire.showModal"
         @open-job-offer-create.window="$wire.create()"
         @open-job-offer-edit.window="$wire.edit($event.detail.id)"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 flex items-center justify-center p-4 bg-black/50"
         style="display:none">

        <div @click.outside="$wire.close()"
             class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">
                    {{ $offerId ? 'Edit Job Offer' : 'Add Job Offer' }}
                </h2>
                <button wire:click="close" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="px-6 py-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Company --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Company *</label>
                        <input wire:model="company" type="text" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Stripe" />
                        @error('company') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Position --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Position *</label>
                        <input wire:model="position" type="text" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Senior Backend Engineer" />
                        @error('position') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Country --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                        <input wire:model="country" type="text" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Germany" />
                        @error('country') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- City --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                        <input wire:model="city" type="text" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Berlin" />
                        @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Salary Min --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Salary Min</label>
                        <input wire:model="salary_min" type="number" min="0" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. 60000" />
                        @error('salary_min') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Salary Max --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Salary Max</label>
                        <div class="flex gap-2">
                            <input wire:model="salary_max" type="number" min="0" class="flex-1 rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. 90000" />
                            <select wire:model="salary_currency" class="w-24 rounded-lg border-gray-300 text-sm shadow-sm">
                                <option>USD</option><option>EUR</option><option>GBP</option><option>PLN</option><option>CZK</option><option>CHF</option>
                            </select>
                        </div>
                        @error('salary_max') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select wire:model="status" class="w-full rounded-lg border-gray-300 text-sm shadow-sm">
                            <option value="open">Open</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>

                    {{-- Posted Date --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Posted Date</label>
                        <input wire:model="posted_date" type="date" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                        @error('posted_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Remote toggle --}}
                <div class="flex items-center gap-3">
                    <input wire:model="is_remote" type="checkbox" id="is_remote" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                    <label for="is_remote" class="text-sm font-medium text-gray-700">Remote position</label>
                </div>

                {{-- URL --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Job URL</label>
                    <input wire:model="url" type="url" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="https://..." />
                    @error('url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Summary --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Summary</label>
                    <textarea wire:model="summary" rows="4" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Brief summary of the role, responsibilities, and what makes it interesting..."></textarea>
                </div>

                {{-- Skills --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Required Skills</label>
                    <div class="flex gap-2">
                        <input wire:model="new_skill"
                               wire:keydown.enter.prevent="addSkill"
                               type="text"
                               placeholder="e.g. Go, PostgreSQL, Kubernetes..."
                               class="flex-1 rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                        <button type="button" wire:click="addSkill"
                                class="px-3 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition whitespace-nowrap">
                            Add
                        </button>
                    </div>
                    @if(!empty($skills))
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            @foreach($skills as $i => $skill)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                    {{ $skill }}
                                    <button type="button" wire:click="removeSkill({{ $i }})" class="text-indigo-400 hover:text-indigo-700 leading-none">&times;</button>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Company Info --}}
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-3">Company Info</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Valuation</label>
                            <input wire:model="company_valuation" type="text"
                                   class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="e.g. $1.2B, Series B, Bootstrapped" />
                            @error('company_valuation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Number of Employees</label>
                            <select wire:model="company_employees"
                                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Unknown</option>
                                <option>1–10</option>
                                <option>11–50</option>
                                <option>51–200</option>
                                <option>201–500</option>
                                <option>501–1 000</option>
                                <option>1 001–5 000</option>
                                <option>5 001–10 000</option>
                                <option>10 000+</option>
                            </select>
                            @error('company_employees') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Founders / Owners</label>
                        <input wire:model="company_owners" type="text"
                               class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="e.g. Patrick Collison, John Collison" />
                        @error('company_owners') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Company Rating --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Company Rating <span class="text-gray-400 font-normal">(1–5)</span></label>
                        <div class="flex items-center gap-2">
                            <input wire:model.live="company_rating" type="number" min="1" max="5" step="0.1"
                                   class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="e.g. 4.2" />
                        </div>
                        @error('company_rating') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rating Source</label>
                        <input wire:model="company_rating_source" type="text"
                               class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Glassdoor, Kununu, LinkedIn..." />
                        @error('company_rating_source') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- ATS / AI Screening Probability --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        ATS / AI Screening Probability
                        <span class="font-normal text-gray-400 ml-1">— does this company auto-screen CVs?</span>
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(App\Models\JobOffer::$atsProbabilityLevels as $key => $meta)
                            @php
                                $btnColors = [
                                    'green'  => 'border-green-300 bg-green-50 text-green-700',
                                    'yellow' => 'border-yellow-300 bg-yellow-50 text-yellow-700',
                                    'red'    => 'border-red-300 bg-red-50 text-red-700',
                                ];
                                $activeRing = [
                                    'green'  => 'ring-2 ring-green-400',
                                    'yellow' => 'ring-2 ring-yellow-400',
                                    'red'    => 'ring-2 ring-red-400',
                                ];
                            @endphp
                            <button type="button"
                                    wire:click="$set('ats_probability', '{{ $ats_probability === $key ? '' : $key }}')"
                                    class="px-2 py-2 rounded-lg border text-xs font-medium text-center transition {{ $btnColors[$meta['color']] }} {{ $ats_probability === $key ? $activeRing[$meta['color']] : 'opacity-60' }}">
                                <div class="font-semibold">{{ $meta['label'] }}</div>
                                <div class="text-xs font-normal opacity-80 mt-0.5">{{ $meta['hint'] }}</div>
                            </button>
                        @endforeach
                    </div>
                    @error('ats_probability') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea wire:model="notes" rows="3" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Any additional notes..."></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="close" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">
                        <span wire:loading.remove>{{ $offerId ? 'Update' : 'Save' }}</span>
                        <span wire:loading>Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
