import './bootstrap';

import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);
window.Chart = Chart;

// Register Alpine components before Livewire/Alpine starts
document.addEventListener('alpine:init', () => {
    window.Alpine.data('dashboardCharts', (data) => ({
        data,
        charts: {},
        init() {
            this.$nextTick(() => {
                this.renderRemote();
                this.renderCountries();
                this.renderTrend();
            });
        },
        renderRemote() {
            const ctx = document.getElementById('chart-remote');
            if (!ctx) return;
            this.charts.remote = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Remote', 'Onsite'],
                    datasets: [{ data: [this.data.remote, this.data.onsite], backgroundColor: ['#6366f1', '#e5e7eb'] }],
                },
                options: { plugins: { legend: { position: 'bottom' } } },
            });
        },
        renderCountries() {
            const ctx = document.getElementById('chart-countries');
            if (!ctx) return;
            this.charts.countries = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Object.keys(this.data.topCountries),
                    datasets: [{ label: 'Offers', data: Object.values(this.data.topCountries), backgroundColor: '#6366f1' }],
                },
                options: { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { ticks: { stepSize: 1 } } } },
            });
        },
        renderTrend() {
            const ctx = document.getElementById('chart-trend');
            if (!ctx) return;
            this.charts.trend = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: Object.keys(this.data.monthlyTrend),
                    datasets: [{
                        label: 'Offers',
                        data: Object.values(this.data.monthlyTrend),
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99,102,241,0.1)',
                        fill: true,
                        tension: 0.3,
                    }],
                },
                options: { plugins: { legend: { display: false } }, scales: { y: { ticks: { stepSize: 1 } } } },
            });
        },
    }));
});
