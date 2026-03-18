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
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $offer->company }}
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                @if($offer->url)
                                    <a href="{{ $offer->url }}" target="_blank" class="text-indigo-600 hover:underline">{{ $offer->position }}</a>
                                @else
                                    {{ $offer->position }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ collect([$offer->city, $offer->country])->filter()->implode(', ') }}
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
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <button @click="$dispatch('open-job-offer-edit', { id: {{ $offer->id }} })"
                                        class="text-xs font-medium px-2 py-1 rounded text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 transition-colors mr-1">Edit</button>
                                @if(isset($myApplications[$offer->id]))
                                    <button @click="$dispatch('open-application-modal', { jobOfferId: {{ $offer->id }} })"
                                            class="text-xs font-medium px-2 py-1 rounded text-green-700 hover:text-green-900 hover:bg-green-50 transition-colors mr-1">Track</button>
                                @else
                                    <button wire:click="saveOffer({{ $offer->id }})"
                                            class="text-xs font-medium px-2 py-1 rounded text-teal-700 hover:text-teal-900 hover:bg-teal-50 transition-colors mr-1">Save</button>
                                @endif
                                <button wire:click="deleteOffer({{ $offer->id }})"
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
