// Dashboard JavaScript for Purple Admin
class Dashboard {
    constructor() {
        this.sidebar = document.getElementById('sidebar');
        this.sidebarToggle = document.getElementById('sidebarToggle');
        this.mobileMenuToggle = document.getElementById('mobileMenuToggle');
        this.isSidebarOpen = false;
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeCharts();
        this.setupResponsiveHandlers();
        this.addAnimations();
    }

    setupEventListeners() {
        // Mobile menu toggle
        if (this.mobileMenuToggle) {
            this.mobileMenuToggle.addEventListener('click', () => {
                this.toggleSidebar();
            });
        }

        // Sidebar toggle
        if (this.sidebarToggle) {
            this.sidebarToggle.addEventListener('click', () => {
                this.toggleSidebar();
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && this.isSidebarOpen) {
                if (!this.sidebar.contains(e.target) && !this.mobileMenuToggle.contains(e.target)) {
                    this.closeSidebar();
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            this.handleResize();
        });

        // Search functionality
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.handleSearch(e.target.value);
            });
        }

        // Quick action buttons
        const quickActionBtns = document.querySelectorAll('.quick-action-btn');
        quickActionBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.handleQuickAction(e.target.closest('.quick-action-btn'));
            });
        });

        // Notification buttons
        const notificationBtns = document.querySelectorAll('.notification-btn');
        notificationBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.handleNotificationClick(e.target.closest('.notification-btn'));
            });
        });
    }

    toggleSidebar() {
        this.isSidebarOpen = !this.isSidebarOpen;
        
        if (this.isSidebarOpen) {
            this.openSidebar();
        } else {
            this.closeSidebar();
        }
    }

    openSidebar() {
        this.sidebar.classList.add('open');
        this.isSidebarOpen = true;
        document.body.style.overflow = 'hidden';
    }

    closeSidebar() {
        this.sidebar.classList.remove('open');
        this.isSidebarOpen = false;
        document.body.style.overflow = '';
    }

    handleResize() {
        if (window.innerWidth > 768) {
            this.closeSidebar();
            document.body.style.overflow = '';
        }
    }

    handleSearch(query) {
        // Implement search functionality
        console.log('Searching for:', query);
        
        // Add loading state
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.style.opacity = '0.6';
        }

        // Simulate search delay
        setTimeout(() => {
            if (searchInput) {
                searchInput.style.opacity = '1';
            }
            this.showSearchResults(query);
        }, 500);
    }

    showSearchResults(query) {
        // Create or update search results
        let resultsContainer = document.querySelector('.search-results');
        
        if (!resultsContainer) {
            resultsContainer = document.createElement('div');
            resultsContainer.className = 'search-results';
            resultsContainer.style.cssText = `
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
                z-index: 1000;
                max-height: 300px;
                overflow-y: auto;
            `;
            
            const searchBar = document.querySelector('.search-bar');
            if (searchBar) {
                searchBar.style.position = 'relative';
                searchBar.appendChild(resultsContainer);
            }
        }

        // Mock search results
        const mockResults = [
            { title: 'User Management', type: 'Page', url: '/users' },
            { title: 'Analytics Dashboard', type: 'Page', url: '/analytics' },
            { title: 'Settings Panel', type: 'Page', url: '/settings' },
            { title: 'Recent Reports', type: 'Document', url: '/reports' }
        ].filter(item => 
            item.title.toLowerCase().includes(query.toLowerCase())
        );

        if (query.length > 0 && mockResults.length > 0) {
            resultsContainer.innerHTML = mockResults.map(result => `
                <div class="search-result-item" style="padding: 12px 16px; border-bottom: 1px solid #f3f4f6; cursor: pointer; transition: background-color 0.2s;">
                    <div style="font-weight: 500; color: #374151;">${result.title}</div>
                    <div style="font-size: 12px; color: #6b7280;">${result.type}</div>
                </div>
            `).join('');

            // Add click handlers
            resultsContainer.querySelectorAll('.search-result-item').forEach((item, index) => {
                item.addEventListener('click', () => {
                    console.log('Navigating to:', mockResults[index].url);
                    this.closeSearchResults();
                });
                
                item.addEventListener('mouseenter', () => {
                    item.style.backgroundColor = '#f9fafb';
                });
                
                item.addEventListener('mouseleave', () => {
                    item.style.backgroundColor = '';
                });
            });
            
            resultsContainer.style.display = 'block';
        } else {
            resultsContainer.style.display = 'none';
        }
    }

    closeSearchResults() {
        const resultsContainer = document.querySelector('.search-results');
        if (resultsContainer) {
            resultsContainer.style.display = 'none';
        }
    }

    handleQuickAction(button) {
        const buttonText = button.textContent.trim();
        
        // Add loading state
        const originalText = button.innerHTML;
        button.innerHTML = `
            <svg style="width: 18px; height: 18px; animation: spin 1s linear infinite;" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
            Processing...
        `;
        
        button.style.pointerEvents = 'none';
        
        // Simulate action
        setTimeout(() => {
            button.innerHTML = originalText;
            button.style.pointerEvents = '';
            
            // Show success message
            this.showNotification(`${buttonText} action completed!`, 'success');
        }, 2000);
    }

    handleNotificationClick(button) {
        const badge = button.querySelector('.notification-badge');
        if (badge) {
            // Animate badge
            badge.style.transform = 'scale(1.2)';
            setTimeout(() => {
                badge.style.transform = 'scale(1)';
            }, 200);
            
            // Show notification panel (mock)
            this.showNotificationPanel(button);
        }
    }

    showNotificationPanel(button) {
        // Create notification panel
        let panel = document.querySelector('.notification-panel');
        
        if (!panel) {
            panel = document.createElement('div');
            panel.className = 'notification-panel';
            panel.style.cssText = `
                position: absolute;
                top: 100%;
                right: 0;
                width: 320px;
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
                z-index: 1000;
                max-height: 400px;
                overflow-y: auto;
            `;
            
            button.style.position = 'relative';
            button.appendChild(panel);
        }

        // Mock notifications
        const notifications = [
            { title: 'New user registered', time: '2 minutes ago', type: 'info' },
            { title: 'System update available', time: '1 hour ago', type: 'warning' },
            { title: 'Backup completed', time: '3 hours ago', type: 'success' },
            { title: 'High server load', time: '5 hours ago', type: 'error' }
        ];

        panel.innerHTML = `
            <div style="padding: 16px; border-bottom: 1px solid #f3f4f6;">
                <h3 style="font-size: 16px; font-weight: 600; color: #374151;">Notifications</h3>
            </div>
            <div>
                ${notifications.map(notification => `
                    <div style="padding: 12px 16px; border-bottom: 1px solid #f3f4f6; cursor: pointer; transition: background-color 0.2s;">
                        <div style="font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">${notification.title}</div>
                        <div style="font-size: 12px; color: #6b7280;">${notification.time}</div>
                    </div>
                `).join('')}
            </div>
        `;

        // Add hover effects
        panel.querySelectorAll('div[style*="cursor: pointer"]').forEach(item => {
            item.addEventListener('mouseenter', () => {
                item.style.backgroundColor = '#f9fafb';
            });
            
            item.addEventListener('mouseleave', () => {
                item.style.backgroundColor = '';
            });
        });

        panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        
        const colors = {
            success: '#22c55e',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            color: #374151;
            padding: 16px 20px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-left: 4px solid ${colors[type]};
            z-index: 10000;
            max-width: 400px;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        
        notification.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 8px; height: 8px; border-radius: 50%; background: ${colors[type]};"></div>
                <div style="flex: 1;">${message}</div>
                <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; color: #6b7280; cursor: pointer; padding: 4px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    }

    initializeCharts() {
        // Initialize Chart.js charts
        this.initSalesChart();
        this.initTrafficChart();
    }

    initSalesChart() {
        const ctx = document.getElementById('salesChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [
                    {
                        label: 'CHN',
                        data: [12, 19, 3, 5, 2, 3],
                        backgroundColor: '#667eea',
                        borderRadius: 4
                    },
                    {
                        label: 'USA',
                        data: [2, 3, 20, 5, 1, 4],
                        backgroundColor: '#38f9d7',
                        borderRadius: 4
                    },
                    {
                        label: 'UK',
                        data: [3, 10, 13, 15, 22, 30],
                        backgroundColor: '#f59e0b',
                        borderRadius: 4
                    }
                ]
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
                        grid: {
                            color: '#f3f4f6'
                        },
                        ticks: {
                            color: '#6b7280'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280'
                        }
                    }
                }
            }
        });
    }

    initTrafficChart() {
        const ctx = document.getElementById('trafficChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Direct', 'Social', 'Email'],
                datasets: [{
                    data: [45, 30, 25],
                    backgroundColor: [
                        '#f093fb',
                        '#38f9d7',
                        '#4facfe'
                    ],
                    borderWidth: 0
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
                cutout: '70%'
            }
        });
    }

    setupResponsiveHandlers() {
        // Handle responsive behavior
        const handleResize = () => {
            const width = window.innerWidth;
            
            if (width <= 768) {
                // Mobile behavior
                this.closeSidebar();
            } else {
                // Desktop behavior
                this.closeSidebar();
            }
        };

        window.addEventListener('resize', handleResize);
        handleResize(); // Initial call
    }

    addAnimations() {
        // Add entrance animations to elements
        const animatedElements = document.querySelectorAll('.stat-card, .chart-card, .content-card');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                }
            });
        }, {
            threshold: 0.1
        });

        animatedElements.forEach(element => {
            observer.observe(element);
        });

        // Add staggered animation to stat cards
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new Dashboard();
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .fade-in {
        animation: fadeIn 0.6s ease-out forwards;
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
`;
document.head.appendChild(style);
