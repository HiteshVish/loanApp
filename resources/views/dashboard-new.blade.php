<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Styles -->
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <div class="logo-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <span class="logo-text">Purple</span>
                </div>
                <button class="sidebar-toggle mobile-only" id="sidebarToggle">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 6h18M3 12h18M3 18h18"/>
                    </svg>
                </button>
            </div>

            <div class="user-profile">
                <div class="user-avatar">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}">
                    @else
                        <div class="avatar-placeholder">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">Project Manager</div>
                </div>
                <div class="user-status">
                    <div class="status-indicator online"></div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li class="nav-item active">
                        <a href="{{ route('dashboard') }}" class="nav-link">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                            </svg>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <span class="nav-text">Analytics</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A1.5 1.5 0 0 0 18.54 8H16c-.8 0-1.54.37-2.01.99L12 11l-1.99-2.01A2.5 2.5 0 0 0 8 8H5.46c-.8 0-1.54.37-2.01.99L1 14.5V22h2v-6h2.5l2.54 7.63A1.5 1.5 0 0 0 9.46 24H12c.8 0 1.54-.37 2.01-.99L16 21l1.99 2.01A2.5 2.5 0 0 0 20 24h2.54c.8 0 1.54-.37 2.01-.99L26 16.5V8h-2v6h-2.5z"/>
                            </svg>
                            <span class="nav-text">Users</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                            </svg>
                            <span class="nav-text">Reports</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            <span class="nav-text">Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <button class="mobile-menu-toggle" id="mobileMenuToggle">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 6h18M3 12h18M3 18h18"/>
                        </svg>
                    </button>
                    <div class="page-title">
                        <svg class="page-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                        </svg>
                        <h1>Dashboard</h1>
                    </div>
                </div>

                <div class="header-center">
                    <div class="search-bar">
                        <svg class="search-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                        <input type="text" placeholder="Search projects..." class="search-input">
                    </div>
                </div>

                <div class="header-right">
                    <div class="header-actions">
                        <button class="action-btn" title="Fullscreen">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
                            </svg>
                        </button>
                        <button class="action-btn notification-btn" title="Messages">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                            <span class="notification-badge">3</span>
                        </button>
                        <button class="action-btn notification-btn" title="Notifications">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                            </svg>
                            <span class="notification-badge">5</span>
                        </button>
                        <button class="action-btn" title="Logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="user-profile-header">
                        <div class="user-avatar-small">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}">
                            @else
                                <div class="avatar-placeholder-small">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <span class="user-name-small">{{ Auth::user()->name }}</span>
                        <svg class="dropdown-arrow" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7 10l5 5 5-5z"/>
                        </svg>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <main class="dashboard-main">
                <div class="dashboard-content">
                    <!-- Stats Cards -->
                    <div class="stats-grid">
                        <div class="stat-card sales">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <h3 class="stat-title">Weekly Sales</h3>
                                    <div class="stat-value">$15,000</div>
                                    <div class="stat-change positive">+60%</div>
                                </div>
                                <div class="stat-icon">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M3 13h2v8H3v-8zm4-6h2v14H7V7zm4-4h2v18h-2V3zm4 8h2v10h-2V11zm4-6h2v16h-2V5z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card orders">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <h3 class="stat-title">Weekly Orders</h3>
                                    <div class="stat-value">45,633</div>
                                    <div class="stat-change negative">-10%</div>
                                </div>
                                <div class="stat-icon">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19 7h-8v6h8V7zm-2 4h-4V9h4v2zm4-12H3C1.9 1 1 1.9 1 3v18c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V3c0-1.1-.9-2-2-2zm0 18H3V5h18v14z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card visitors">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <h3 class="stat-title">Visitors Online</h3>
                                    <div class="stat-value">95,574</div>
                                    <div class="stat-change positive">+5%</div>
                                </div>
                                <div class="stat-icon">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="charts-grid">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Visit And Sales Statistics</h3>
                                <div class="chart-legend">
                                    <div class="legend-item">
                                        <span class="legend-color chn"></span>
                                        <span>CHN</span>
                                    </div>
                                    <div class="legend-item">
                                        <span class="legend-color usa"></span>
                                        <span>USA</span>
                                    </div>
                                    <div class="legend-item">
                                        <span class="legend-color uk"></span>
                                        <span>UK</span>
                                    </div>
                                </div>
                            </div>
                            <div class="chart-content">
                                <canvas id="salesChart" width="400" height="200"></canvas>
                            </div>
                        </div>

                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Traffic Sources</h3>
                                <button class="upgrade-btn">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm2.7-2h8.6l.9-5.4L12 8.5 6.8 8.6l.9 5.4z"/>
                                    </svg>
                                    Upgrade to Pro
                                </button>
                            </div>
                            <div class="chart-content">
                                <canvas id="trafficChart" width="300" height="300"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Content -->
                    <div class="content-grid">
                        <div class="content-card">
                            <div class="card-header">
                                <h3 class="card-title">Recent Activity</h3>
                            </div>
                            <div class="card-content">
                                <div class="activity-list">
                                    <div class="activity-item">
                                        <div class="activity-icon">
                                            <svg viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                            </svg>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-text">User {{ Auth::user()->name }} logged in</div>
                                            <div class="activity-time">Just now</div>
                                        </div>
                                    </div>
                                    <div class="activity-item">
                                        <div class="activity-icon">
                                            <svg viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-text">Google OAuth login enabled</div>
                                            <div class="activity-time">2 minutes ago</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="content-card">
                            <div class="card-header">
                                <h3 class="card-title">Quick Actions</h3>
                            </div>
                            <div class="card-content">
                                <div class="quick-actions">
                                    <button class="quick-action-btn">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                                        </svg>
                                        Add User
                                    </button>
                                    <button class="quick-action-btn">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                        </svg>
                                        Generate Report
                                    </button>
                                    <button class="quick-action-btn">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                        </svg>
                                        View Analytics
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
