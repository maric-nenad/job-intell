<div>
    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <p class="text-sm text-gray-500">Total Offers</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $total }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <p class="text-sm text-gray-500">Open</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $open }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <p class="text-sm text-gray-500">Closed</p>
            <p class="text-3xl font-bold text-red-500 mt-1">{{ $closed }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <p class="text-sm text-gray-500">Remote</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ $remote }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <p class="text-sm text-gray-500">Onsite</p>
            <p class="text-3xl font-bold text-purple-600 mt-1">{{ $onsite }}</p>
        </div>
    </div>

    {{-- Charts Row --}}
    @php
        $chartData = json_encode([
            'topCountries' => $topCountries,
            'topCities'    => $topCities,
            'monthlyTrend' => $monthlyTrend,
            'remote'       => $remote,
            'onsite'       => $onsite,
        ]);
    @endphp
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8"
         x-data="dashboardCharts({{ $chartData }})"
         x-init="init()">

        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <h3 class="text-sm font-medium text-gray-500 mb-4">Remote vs Onsite</h3>
            <canvas id="chart-remote" height="200"></canvas>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <h3 class="text-sm font-medium text-gray-500 mb-4">Top Countries</h3>
            <canvas id="chart-countries" height="200"></canvas>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 lg:col-span-1">
            <h3 class="text-sm font-medium text-gray-500 mb-4">Monthly Trend</h3>
            <canvas id="chart-trend" height="200"></canvas>
        </div>
    </div>

    {{-- Top Tables --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <h3 class="text-sm font-semibold text-gray-600 mb-3">Top Companies</h3>
            <ul class="space-y-2">
                @forelse($topCompanies as $company => $count)
                    <li class="flex justify-between text-sm">
                        <span class="text-gray-700 truncate">{{ $company }}</span>
                        <span class="font-semibold text-gray-900 ml-2">{{ $count }}</span>
                    </li>
                @empty
                    <li class="text-sm text-gray-400">No data yet.</li>
                @endforelse
            </ul>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <h3 class="text-sm font-semibold text-gray-600 mb-3">Top Cities</h3>
            <ul class="space-y-2">
                @forelse($topCities as $city => $count)
                    <li class="flex justify-between text-sm">
                        <span class="text-gray-700 truncate">{{ $city }}</span>
                        <span class="font-semibold text-gray-900 ml-2">{{ $count }}</span>
                    </li>
                @empty
                    <li class="text-sm text-gray-400">No data yet.</li>
                @endforelse
            </ul>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <h3 class="text-sm font-semibold text-gray-600 mb-3">Top Countries</h3>
            <ul class="space-y-2">
                @forelse($topCountries as $country => $count)
                    <li class="flex justify-between text-sm">
                        <span class="text-gray-700 truncate">{{ $country }}</span>
                        <span class="font-semibold text-gray-900 ml-2">{{ $count }}</span>
                    </li>
                @empty
                    <li class="text-sm text-gray-400">No data yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
