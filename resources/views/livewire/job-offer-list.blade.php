<div>
    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-5">
        <input wire:model.live.debounce.300ms="search"
               type="search"
               placeholder="Search company, position, city..."
               class="flex-1 rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" />

        <select wire:model.live="statusFilter" class="rounded-lg border-gray-300 shadow-sm text-sm">
            <option value="">All Statuses</option>
            <option value="open">Open</option>
            <option value="closed">Closed</option>
        </select>

        <select wire:model.live="remoteFilter" class="rounded-lg border-gray-300 shadow-sm text-sm">
            <option value="">Remote & Onsite</option>
            <option value="remote">Remote Only</option>
            <option value="onsite">Onsite Only</option>
        </select>

        <select wire:model.live="countryFilter" class="rounded-lg border-gray-300 shadow-sm text-sm">
            <option value="">All Countries</option>
            @foreach($countries as $country)
                <option value="{{ $country }}">{{ $country }}</option>
            @endforeach
        </select>

        <button @click="$dispatch('open-job-offer-create')"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition whitespace-nowrap">
            + Add Offer
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortByColumn('company')" class="px-4 py-3 text-left font-medium text-gray-500 cursor-pointer hover:text-gray-700 select-none">
                            Company @if($sortBy === 'company') <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span> @endif
                        </th>
                        <th wire:click="sortByColumn('position')" class="px-4 py-3 text-left font-medium text-gray-500 cursor-pointer hover:text-gray-700 select-none">
                            Position @if($sortBy === 'position') <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span> @endif
                        </th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Location</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">ATS Risk</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Salary</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Type</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">My Status</th>
                        <th wire:click="sortByColumn('status')" class="px-4 py-3 text-left font-medium text-gray-500 cursor-pointer hover:text-gray-700 select-none">
                            Status @if($sortBy === 'status') <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span> @endif
                        </th>
                        <th wire:click="sortByColumn('posted_date')" class="px-4 py-3 text-left font-medium text-gray-500 cursor-pointer hover:text-gray-700 select-none">
                            Posted @if($sortBy === 'posted_date') <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span> @endif
                        </th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($jobOffers as $offer)
                        <tr @click="$dispatch('open-job-offer-detail', { jobOfferId: {{ $offer->id }} })"
                            class="hover:bg-gray-50 transition cursor-pointer">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $offer->company }}</div>
                                @if($offer->company_rating)
                                    <div class="flex items-center gap-1 mt-0.5">
                                        @php $stars = round($offer->company_rating * 2) / 2; @endphp
                                        @for($s = 1; $s <= 5; $s++)
                                            @if($s <= floor($stars))
                                                <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @elseif($stars - floor($stars) >= 0.5 && $s == ceil($stars))
                                                <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 1l2.39 4.843 5.345.777-3.867 3.769.913 5.318L10 13.347V1z"/><path fill="#d1d5db" d="M10 1v12.347l-4.781 2.36.913-5.318L2.265 6.62l5.345-.777L10 1z"/></svg>
                                            @else
                                                <svg class="w-3 h-3 text-gray-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @endif
                                        @endfor
                                        <span class="text-xs text-gray-500 ml-0.5">{{ number_format($offer->company_rating, 1) }}
                                            @if($offer->company_rating_source)
                                                <span class="text-gray-400">· {{ $offer->company_rating_source }}</span>
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                @if($offer->url)
                                    <a href="{{ $offer->url }}" target="_blank" @click.stop class="text-indigo-600 hover:underline">{{ $offer->position }}</a>
                                @else
                                    {{ $offer->position }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ collect([$offer->city, $offer->country])->filter()->implode(', ') }}
                            </td>
                            <td class="px-4 py-3">
                                @if($offer->ats_probability)
                                    @php
                                        $atsMeta = App\Models\JobOffer::$atsProbabilityLevels[$offer->ats_probability];
                                        $atsColors = ['green' => 'bg-green-100 text-green-700', 'yellow' => 'bg-yellow-100 text-yellow-700', 'red' => 'bg-red-100 text-red-700'];
                                    @endphp
                                    <span title="{{ $atsMeta['hint'] }}" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $atsColors[$atsMeta['color']] }}">
                                        {{ $atsMeta['label'] }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                @if($offer->salary_min)
                                    {{ number_format($offer->salary_min) }}–{{ number_format($offer->salary_max ?? $offer->salary_min) }} {{ $offer->salary_currency }}
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($offer->is_remote)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Remote</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Onsite</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if(isset($myApplications[$offer->id]))
                                    @php $appStatus = App\Models\Application::$statuses[$myApplications[$offer->id]]; @endphp
                                    @php $badgeColors = ['gray'=>'bg-gray-100 text-gray-600','teal'=>'bg-teal-100 text-teal-700','blue'=>'bg-blue-100 text-blue-700','yellow'=>'bg-yellow-100 text-yellow-700','purple'=>'bg-purple-100 text-purple-700','green'=>'bg-green-100 text-green-700','red'=>'bg-red-100 text-red-600','orange'=>'bg-orange-100 text-orange-700']; @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeColors[$appStatus['color']] }}">
                                        {{ $appStatus['label'] }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($offer->status === 'open')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Open</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">Closed</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-400 whitespace-nowrap">
                                {{ $offer->posted_date?->format('M d, Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap" @click.stop>
                                <button @click.stop="$dispatch('open-job-offer-edit', { id: {{ $offer->id }} })"
                                        class="text-xs font-medium px-2 py-1 rounded text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 transition-colors mr-1">Edit</button>
                                @if(isset($myApplications[$offer->id]))
                                    <button @click.stop="$dispatch('open-application-modal', { jobOfferId: {{ $offer->id }} })"
                                            class="text-xs font-medium px-2 py-1 rounded text-green-700 hover:text-green-900 hover:bg-green-50 transition-colors mr-1">Track</button>
                                @else
                                    <button wire:click.stop="saveOffer({{ $offer->id }})"
                                            class="text-xs font-medium px-2 py-1 rounded text-teal-700 hover:text-teal-900 hover:bg-teal-50 transition-colors mr-1">Save</button>
                                @endif
                                <button wire:click.stop="deleteOffer({{ $offer->id }})"
                                        wire:confirm="Delete this job offer?"
                                        class="text-xs font-medium px-2 py-1 rounded text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-gray-400">No job offers found. Add your first one!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jobOffers->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $jobOffers->links() }}
            </div>
        @endif
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
