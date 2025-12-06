<?php
// Load admin header using existing function from index.php
load_header();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - ENSA Events</title>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <!-- CountUp.js for animated numbers -->
    <script src="https://cdn.jsdelivr.net/npm/countup.js@2.6.2/dist/countUp.umd.min.js"></script>

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            --card-shadow-hover: 0 10px 20px rgba(0, 0, 0, 0.12);
        }

        .dashboard-container {
            padding: 2rem;
            background: #f8f9fa;
            min-height: 100vh;
        }

        .dashboard-header {
            margin-bottom: 2rem;
        }

        .dashboard-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .dashboard-subtitle {
            color: #718096;
            font-size: 0.95rem;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--card-shadow-hover);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-icon.purple {
            background: linear-gradient(135deg, #667eea20 0%, #764ba220 100%);
            color: #667eea;
        }

        .stat-icon.blue {
            background: linear-gradient(135deg, #4299e120 0%, #3182ce20 100%);
            color: #4299e1;
        }

        .stat-icon.green {
            background: linear-gradient(135deg, #48bb7820 0%, #38a16920 100%);
            color: #48bb78;
        }

        .stat-icon.orange {
            background: linear-gradient(135deg, #ed8936 20 0%, #dd6b2020 100%);
            color: #ed8936;
        }

        .stat-label {
            font-size: 0.875rem;
            color: #718096;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
        }

        /* Charts Section */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .chart-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .chart-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #2d3748;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        /* Widgets Section */
        .widgets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .widget-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
        }

        .widget-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f7fafc;
        }

        .widget-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #2d3748;
        }

        .event-item {
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 0.75rem;
            background: #f7fafc;
            transition: all 0.2s ease;
        }

        .event-item:hover {
            background: #edf2f7;
            transform: translateX(4px);
        }

        .event-item:last-child {
            margin-bottom: 0;
        }

        .event-title {
            font-weight: 600;
            color: #2d3748;
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
        }

        .event-meta {
            font-size: 0.8rem;
            color: #718096;
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-primary {
            background: linear-gradient(135deg, #667eea20 0%, #764ba220 100%);
            color: #667eea;
        }

        .badge-success {
            background: linear-gradient(135deg, #48bb7820 0%, #38a16920 100%);
            color: #48bb78;
        }

        .badge-warning {
            background: linear-gradient(135deg, #ed893620 0%, #dd6b2020 100%);
            color: #ed8936;
        }

        /* Loading State */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s ease-in-out infinite;
            border-radius: 4px;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #a0aec0;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }

            .stats-grid,
            .charts-grid,
            .widgets-grid {
                grid-template-columns: 1fr;
            }

            .chart-container {
                height: 250px;
            }
        }

        /* Fade in animation */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header fade-in">
            <h1 class="dashboard-title">
                <i class="bi bi-speedometer2"></i> Dashboard Admin
            </h1>
            <p class="dashboard-subtitle">
                Vue d'ensemble de votre plateforme d'événements
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card fade-in" style="animation-delay: 0.1s">
                <div class="stat-icon purple">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <div class="stat-label">Total Événements</div>
                <div class="stat-value" id="totalEvents">-</div>
            </div>

            <div class="stat-card fade-in" style="animation-delay: 0.2s">
                <div class="stat-icon blue">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-label">Événements à Venir</div>
                <div class="stat-value" id="upcomingEvents">-</div>
            </div>

            <div class="stat-card fade-in" style="animation-delay: 0.3s">
                <div class="stat-icon green">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-label">Total Participants</div>
                <div class="stat-value" id="totalParticipants">-</div>
            </div>

            <div class="stat-card fade-in" style="animation-delay: 0.4s">
                <div class="stat-icon orange">
                    <i class="bi bi-tags"></i>
                </div>
                <div class="stat-label">Catégories</div>
                <div class="stat-value" id="totalCategories">-</div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-grid">
            <div class="chart-card fade-in" style="animation-delay: 0.5s">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="bi bi-bar-chart"></i> Événements par Mois
                    </h3>
                </div>
                <div class="chart-container">
                    <canvas id="eventsPerMonthChart"></canvas>
                </div>
            </div>

            <div class="chart-card fade-in" style="animation-delay: 0.6s">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="bi bi-pie-chart"></i> Distribution par Catégorie
                    </h3>
                </div>
                <div class="chart-container">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Widgets Row -->
        <div class="widgets-grid">
            <div class="widget-card fade-in" style="animation-delay: 0.7s">
                <div class="widget-header">
                    <h3 class="widget-title">
                        <i class="bi bi-clock-history"></i> Derniers Événements
                    </h3>
                </div>
                <div id="latestEvents">
                    <div class="skeleton" style="height: 60px; margin-bottom: 0.75rem;"></div>
                    <div class="skeleton" style="height: 60px; margin-bottom: 0.75rem;"></div>
                    <div class="skeleton" style="height: 60px;"></div>
                </div>
            </div>

            <div class="widget-card fade-in" style="animation-delay: 0.8s">
                <div class="widget-header">
                    <h3 class="widget-title">
                        <i class="bi bi-calendar-event"></i> Événements à Venir
                    </h3>
                </div>
                <div id="upcomingEventsWidget">
                    <div class="skeleton" style="height: 60px; margin-bottom: 0.75rem;"></div>
                    <div class="skeleton" style="height: 60px; margin-bottom: 0.75rem;"></div>
                    <div class="skeleton" style="height: 60px;"></div>
                </div>
            </div>

            <div class="widget-card fade-in" style="animation-delay: 0.9s">
                <div class="widget-header">
                    <h3 class="widget-title">
                        <i class="bi bi-trophy"></i> Top Événements
                    </h3>
                </div>
                <div id="topEvents">
                    <div class="skeleton" style="height: 60px; margin-bottom: 0.75rem;"></div>
                    <div class="skeleton" style="height: 60px; margin-bottom: 0.75rem;"></div>
                    <div class="skeleton" style="height: 60px;"></div>
                </div>
            </div>
        </div>

        <!-- Participants Growth Chart (Full Width) -->
        <div class="chart-card fade-in" style="animation-delay: 1s">
            <div class="chart-header">
                <h3 class="chart-title">
                    <i class="bi bi-graph-up"></i> Croissance des Participants
                </h3>
            </div>
            <div class="chart-container">
                <canvas id="participantsChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Chart.js configuration
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#718096';

        let eventsChart, categoryChart, participantsChart;

        // Fetch and display stats
        async function loadStats() {
            try {
                const response = await fetch('?page=dashboard_stats');
                const result = await response.json();

                if (result.success) {
                    const data = result.data;

                    // Animate counters
                    animateValue('totalEvents', 0, data.totalEvents, 1500);
                    animateValue('upcomingEvents', 0, data.upcomingEvents, 1500);
                    animateValue('totalParticipants', 0, data.totalParticipants, 1500);
                    animateValue('totalCategories', 0, data.totalCategories, 1500);
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        // Animate number counter
        function animateValue(id, start, end, duration) {
            const element = document.getElementById(id);
            const range = end - start;
            const increment = end > start ? 1 : -1;
            const stepTime = Math.abs(Math.floor(duration / range));
            let current = start;

            const timer = setInterval(() => {
                current += increment;
                element.textContent = current.toLocaleString();
                if (current === end) {
                    clearInterval(timer);
                }
            }, stepTime);
        }

        // Load Events Per Month Chart
        async function loadEventsPerMonth() {
            try {
                const response = await fetch('?page=dashboard_events_month');
                const result = await response.json();

                if (result.success) {
                    const labels = result.data.map(d => d.label);
                    const data = result.data.map(d => d.count);

                    const ctx = document.getElementById('eventsPerMonthChart').getContext('2d');
                    eventsChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Événements',
                                data: data,
                                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                                borderColor: 'rgba(102, 126, 234, 1)',
                                borderWidth: 2,
                                borderRadius: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Error loading events per month:', error);
            }
        }

        // Load Category Distribution Chart
        async function loadCategoryChart() {
            try {
                const response = await fetch('?page=dashboard_categories');
                const result = await response.json();

                if (result.success) {
                    const labels = result.data.map(d => d.category);
                    const data = result.data.map(d => d.count);

                    const colors = [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(66, 153, 225, 0.8)',
                        'rgba(72, 187, 120, 0.8)',
                        'rgba(237, 137, 54, 0.8)',
                        'rgba(246, 173, 85, 0.8)',
                        'rgba(229, 62, 62, 0.8)'
                    ];

                    const ctx = document.getElementById('categoryChart').getContext('2d');
                    categoryChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: colors,
                                borderWidth: 3,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Error loading category chart:', error);
            }
        }

        // Load Participants Growth Chart
        async function loadParticipantsChart() {
            try {
                const response = await fetch('?page=dashboard_participants');
                const result = await response.json();

                if (result.success) {
                    const labels = result.data.map(d => d.label);
                    const data = result.data.map(d => d.count);

                    const ctx = document.getElementById('participantsChart').getContext('2d');
                    participantsChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Inscriptions',
                                data: data,
                                borderColor: 'rgba(72, 187, 120, 1)',
                                backgroundColor: 'rgba(72, 187, 120, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: 'rgba(72, 187, 120, 1)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Error loading participants chart:', error);
            }
        }

        // Load Latest Events Widget
        async function loadLatestEvents() {
            try {
                const response = await fetch('?page=dashboard_latest');
                const result = await response.json();

                const container = document.getElementById('latestEvents');

                if (result.success && result.data.length > 0) {
                    container.innerHTML = result.data.map(event => `
                        <div class="event-item">
                            <div class="event-title">${event.titre}</div>
                            <div class="event-meta">
                                <span><i class="bi bi-calendar3"></i> ${formatDate(event.date_event)}</span>
                                <span><i class="bi bi-geo-alt"></i> ${event.lieu}</span>
                                <span class="badge badge-primary">${event.participants} participants</span>
                            </div>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<div class="empty-state"><i class="bi bi-inbox"></i><p>Aucun événement</p></div>';
                }
            } catch (error) {
                console.error('Error loading latest events:', error);
            }
        }

        // Load Upcoming Events Widget
        async function loadUpcomingEvents() {
            try {
                const response = await fetch('?page=dashboard_upcoming');
                const result = await response.json();

                const container = document.getElementById('upcomingEventsWidget');

                if (result.success && result.data.length > 0) {
                    container.innerHTML = result.data.map(event => `
                        <div class="event-item">
                            <div class="event-title">${event.titre}</div>
                            <div class="event-meta">
                                <span><i class="bi bi-calendar3"></i> ${formatDate(event.date_event)}</span>
                                <span class="badge badge-success">Dans ${event.days_until} jour${event.days_until > 1 ? 's' : ''}</span>
                            </div>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<div class="empty-state"><i class="bi bi-calendar-x"></i><p>Aucun événement à venir</p></div>';
                }
            } catch (error) {
                console.error('Error loading upcoming events:', error);
            }
        }

        // Load Top Events Widget
        async function loadTopEvents() {
            try {
                const response = await fetch('?page=dashboard_top');
                const result = await response.json();

                const container = document.getElementById('topEvents');

                if (result.success && result.data.length > 0) {
                    container.innerHTML = result.data.map((event, index) => `
                        <div class="event-item">
                            <div class="event-title">
                                ${index === 0 ? '<i class="bi bi-trophy-fill" style="color: #f6ad55;"></i> ' : ''}
                                ${event.titre}
                            </div>
                            <div class="event-meta">
                                <span><i class="bi bi-tag"></i> ${event.categorie}</span>
                                <span class="badge badge-warning">${event.participants} participants</span>
                            </div>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<div class="empty-state"><i class="bi bi-graph-down"></i><p>Aucune donnée</p></div>';
                }
            } catch (error) {
                console.error('Error loading top events:', error);
            }
        }

        // Format date helper
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        }

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function () {
            loadStats();
            loadEventsPerMonth();
            loadCategoryChart();
            loadParticipantsChart();
            loadLatestEvents();
            loadUpcomingEvents();
            loadTopEvents();
        });
    </script>
</body>

</html>