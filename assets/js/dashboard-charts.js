/**
 * ByteStore Admin Dashboard Charts — stable fixed-height init
 */
(function () {
    'use strict';

    if (typeof Chart === 'undefined') return;

    const instances = [];

    function destroyAll() {
        instances.forEach((c) => { try { c.destroy(); } catch (e) { /* noop */ } });
        instances.length = 0;
    }

    function createChart(canvas, config) {
        if (!canvas || canvas.dataset.chartReady === '1') return null;
        const ctx = canvas.getContext('2d');
        if (!ctx) return null;
        const chart = new Chart(ctx, config);
        canvas.dataset.chartReady = '1';
        instances.push(chart);
        return chart;
    }

    function initDashboardCharts() {
        if (!document.getElementById('chartRevenue')) return;
        if (window.__byteStoreChartsInit) return;
        window.__byteStoreChartsInit = true;

        destroyAll();

        const data = window.byteStoreChartData || {};
        const primary = getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim() || '#6366f1';
        const muted = getComputedStyle(document.documentElement).getPropertyValue('--color-text-muted').trim() || '#94a3b8';
        const grid = 'rgba(148,163,184,0.15)';

        const lineBarDefaults = {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            plugins: { legend: { labels: { color: muted } } },
            scales: {
                x: { ticks: { color: muted }, grid: { color: grid } },
                y: { ticks: { color: muted }, grid: { color: grid }, beginAtZero: true },
            },
        };

        createChart(document.getElementById('chartRevenue'), {
            type: 'line',
            data: {
                labels: data.labels || [],
                datasets: [{
                    label: 'Revenue (Rs.)',
                    data: data.revenue || [],
                    borderColor: primary,
                    backgroundColor: primary + '33',
                    fill: true,
                    tension: 0.35,
                }],
            },
            options: lineBarDefaults,
        });

        createChart(document.getElementById('chartOrders'), {
            type: 'bar',
            data: {
                labels: data.labels || [],
                datasets: [{ label: 'Orders', data: data.orders || [], backgroundColor: primary }],
            },
            options: lineBarDefaults,
        });

        createChart(document.getElementById('chartCategory'), {
            type: 'doughnut',
            data: {
                labels: data.catLabels || [],
                datasets: [{
                    data: data.catRevenue || [],
                    backgroundColor: ['#6366f1', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', '#ef4444', '#64748b'],
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: { legend: { position: 'bottom', labels: { color: muted } } },
            },
        });

        createChart(document.getElementById('chartTopProducts'), {
            type: 'bar',
            data: {
                labels: data.topLabels || [],
                datasets: [{ label: 'Units Sold', data: data.topSold || [], backgroundColor: '#10b981' }],
            },
            options: { ...lineBarDefaults, indexAxis: 'y' },
        });

        createChart(document.getElementById('chartCustomers'), {
            type: 'line',
            data: {
                labels: data.custLabels || [],
                datasets: [{
                    label: 'New Customers',
                    data: data.custData || [],
                    borderColor: '#8b5cf6',
                    backgroundColor: '#8b5cf633',
                    fill: true,
                    tension: 0.35,
                }],
            },
            options: lineBarDefaults,
        });

        createChart(document.getElementById('chartInventory'), {
            type: 'pie',
            data: {
                labels: ['In Stock', 'Low Stock', 'Out of Stock'],
                datasets: [{ data: data.invData || [0, 0, 0], backgroundColor: ['#10b981', '#f59e0b', '#ef4444'] }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: { legend: { position: 'bottom', labels: { color: muted } } },
            },
        });
    }

    document.addEventListener('DOMContentLoaded', initDashboardCharts);
})();
