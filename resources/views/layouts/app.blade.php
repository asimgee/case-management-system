<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AI Legal Assistant')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563EB;
            --primary-light: #3B82F6;
            --primary-dark: #1D4ED8;
            --secondary-color: #64748B;
            --accent-color: #8B5CF6;
            --success-color: #10B981;
            --warning-color: #F59E0B;
            --error-color: #EF4444;
            --bg-light: #F8FAFC;
            --bg-dark: #0F172A;
            --text-light: #1E293B;
            --text-dark: #F1F5F9;
        }
        
        body {
            box-sizing: border-box;
            background-color: var(--bg-light);
            color: var(--text-light);
        }
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .sidebar-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover {
            transition: all 0.2s ease-in-out;
        }
        
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .nav-item {
            transition: all 0.2s ease-in-out;
            border-radius: 12px;
        }
        
        .nav-item:hover {
            background-color: rgba(37, 99, 235, 0.1);
            transform: translateX(4px);
        }
        
        .nav-item.active {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
        }
        
        .chart-container {
            height: 300px;
            position: relative;
        }
        
        .fixed-height-400 {
            height: 400px;
        }
        
        .fixed-height-450 {
            height: 450px;
        }
        
        .scrollable {
            overflow-y: auto;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        
        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Dark mode styles */
        .dark body {
            background-color: var(--bg-dark);
            color: var(--text-dark);
        }
        
        .dark .bg-white {
            background-color: #1E293B;
        }
        
        .dark .text-gray-900 {
            color: #F1F5F9;
        }
        
        .dark .text-gray-600 {
            color: #94A3B8;
        }
        
        .dark .bg-gray-50 {
            background-color: #334155;
        }
        
        .dark .border-gray-200 {
            border-color: #475569;
        }
        
        /* Mobile sidebar styles */
        .mobile-sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }
        
        .mobile-sidebar.open {
            transform: translateX(0);
        }
        
        /* Toast notification */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 8px;
            color: white;
            z-index: 1001;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .toast.success {
            background-color: var(--success-color);
        }
        
        .toast.error {
            background-color: var(--error-color);
        }
        
        .toast.info {
            background-color: var(--primary-color);
        }
        
        /* Enhanced button styles */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.4);
        }
        
        .btn-secondary {
            background-color: #F1F5F9;
            color: var(--text-light);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .btn-secondary:hover {
            background-color: #E2E8F0;
        }
        
        /* Card enhancements */
        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #E2E8F0;
            overflow: hidden;
        }
        
        .dark .card {
            background: #1E293B;
            border-color: #334155;
        }
        
        /* Stats card enhancements */
        .stat-card {
            background: linear-gradient(135deg, #FFFFFF, #F8FAFC);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #F1F5F9;
        }
        
        .dark .stat-card {
            background: linear-gradient(135deg, #1E293B, #0F172A);
            border-color: #334155;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .mobile-hidden {
                display: none;
            }
            
            .mobile-full-width {
                width: 100%;
            }
            
            .mobile-p-4 {
                padding: 1rem;
            }
            
            .mobile-text-center {
                text-align: center;
            }
            
            .mobile-flex-col {
                flex-direction: column;
            }
            
            .mobile-space-y-4 > * + * {
                margin-top: 1rem;
            }
            
            .mobile-grid-1 {
                grid-template-columns: 1fr;
            }
            
            .mobile-chart-container {
                height: 250px;
            }
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #F1F5F9;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
        
        .dark ::-webkit-scrollbar-track {
            background: #334155;
        }
        
        .dark ::-webkit-scrollbar-thumb {
            background: #475569;
        }
        
        .dark ::-webkit-scrollbar-thumb:hover {
            background: #64748B;
        }
        
        /* Loading animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="h-full">
    <div class="flex h-full">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navbar -->
            <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 lg:px-6 py-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button class="lg:hidden text-gray-600 dark:text-gray-300" onclick="toggleMobileSidebar()">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <div class="relative mobile-hidden">
                            <input type="text" placeholder="Search cases, clients, documents..." class="w-80 pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                            <i class="fas fa-search text-gray-400 absolute left-3 top-3"></i>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <button onclick="toggleTheme()" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg transition-colors">
                            <i class="fas fa-moon text-lg"></i>
                        </button>
                        <button class="relative p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg transition-colors">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></span>
                        </button>
                        <div class="relative">
                            <button onclick="toggleUserMenu()" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                @if(auth()->user()->profile_image)
                                    <img src="{{ asset('storage/profile-images/' . auth()->user()->profile_image) }}" 
                                        alt="User" class="w-8 h-8 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700">
                                @else
                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="text-sm font-medium mobile-hidden">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs mobile-hidden"></i>
                            </button>
                            <div id="userMenu" class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-50 overflow-hidden">
                                <!-- User Info -->
                                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
                                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                        {{ auth()->user()->isAdmin() ? 'Administrator' : 'User' }}
                                    </p>
                                </div>
                                
                                <!-- Menu Items -->
                                <a href="{{ route('profile.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-user-circle mr-3 text-gray-400"></i>
                                    <span>Profile</span>
                                </a>
                                
                                <a href="{{ route('settings') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-cog mr-3 text-gray-400"></i>
                                    <span>Settings</span>
                                </a>
                                
                                <!-- Security Status -->
                                <div class="px-4 py-2 bg-gray-50 dark:bg-gray-900 border-y border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">2FA Status</span>
                                        @if(auth()->user()->two_factor_enabled || auth()->user()->google2fa_enabled)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                <i class="fas fa-shield-alt mr-1 text-xs"></i>
                                                Enabled
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                <i class="fas fa-exclamation-triangle mr-1 text-xs"></i>
                                                Disabled
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Security Quick Links -->
                                <a href="{{ route('profile.index') }}#security" class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-shield-alt mr-3 text-gray-400"></i>
                                    <span>Security Settings</span>
                                </a>
                                
                                <hr class="my-1 border-gray-200 dark:border-gray-700">
                                
                                <!-- Logout -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center px-4 py-3 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                        <i class="fas fa-sign-out-alt mr-3"></i>
                                        <span>Sign out</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-6 pb-20 lg:pb-6 bg-gray-50 dark:bg-gray-900">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Modals -->
    
    @include('components.modals.case')
    @include('components.modals.client')
    @include('components.modals.document')

    <script>
        // Navigation
        function showPage(pageId) {
            // Hide all pages
            document.querySelectorAll('.page-content').forEach(page => {
                page.classList.add('hidden');
            });
            
            // Show selected page
            document.getElementById(pageId + '-page').classList.remove('hidden');
            
            // Update active nav item
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Find and activate the clicked nav item
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => {
                if (item.getAttribute('onclick') && item.getAttribute('onclick').includes(pageId)) {
                    item.classList.add('active');
                }
            });
        }

        // Mobile sidebar toggle
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            sidebar.classList.toggle('open');
        }

        // Theme toggle
        function toggleTheme() {
            document.documentElement.classList.toggle('dark');
            showToast('Theme updated successfully', 'success');
        }

        function setTheme(theme) {
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            showToast('Theme updated successfully', 'success');
        }

        // User menu toggle
        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            menu.classList.toggle('hidden');
        }

        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('userMenu');
            const button = event.target.closest('button[onclick="toggleUserMenu()"]');
            
            if (!button && !menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        // Case Modal Functions
 function openCaseModal() {
        document.getElementById('caseModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCaseModal() {
        document.getElementById('caseModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

        function resetCaseForm() {
            document.querySelector('#caseModal form').reset();
        }

        function submitCase(event) {
            event.preventDefault();
            
            // Get form data
            const formData = new FormData(event.target);
            const caseData = Object.fromEntries(formData);
            
            // Show success message
            showToast('Case saved successfully!', 'success');
            
            closeCaseModal();
            resetCaseForm();
        }

        // Client Modal Functions
        function openClientModal() {
            document.getElementById('clientModal').classList.add('show');
        }

        function closeClientModal() {
            document.getElementById('clientModal').classList.remove('show');
        }

        function resetClientForm() {
            document.querySelector('#clientModal form').reset();
        }

        function submitClient(event) {
            event.preventDefault();
            
            // Get form data
            const formData = new FormData(event.target);
            const clientData = Object.fromEntries(formData);
            
            // Show success message
            showToast('Client saved successfully!', 'success');
            
            closeClientModal();
            resetClientForm();
        }

        // Document Upload Modal Functions
        function openDocumentUploadModal() {
            document.getElementById('documentUploadModal').classList.add('show');
        }

        function closeDocumentUploadModal() {
            document.getElementById('documentUploadModal').classList.remove('show');
        }

        function resetDocumentForm() {
            document.querySelector('#documentUploadModal form').reset();
        }

        function submitDocument(event) {
            event.preventDefault();
            
            // Get form data
            const formData = new FormData(event.target);
            const documentData = Object.fromEntries(formData);
            
            // Show success message
            showToast('Document uploaded successfully!', 'success');
            
            closeDocumentUploadModal();
            resetDocumentForm();
        }

        // Toast notification function
        function showToast(message, type = 'info') {
            // Remove existing toasts
            const existingToasts = document.querySelectorAll('.toast');
            existingToasts.forEach(toast => toast.remove());
            
            // Create new toast
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(toast);
            
            // Show toast
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);
            
            // Hide toast after 3 seconds
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3000);
        }

        // Report generation functions
        function generateReport() {
            showToast('Report generation started. You will be notified when complete.', 'info');
            // Simulate report generation
            setTimeout(() => {
                showToast('Report generated successfully!', 'success');
            }, 2000);
        }

        function exportPDF() {
            showToast('PDF export started...', 'info');
            // Simulate PDF export
            setTimeout(() => {
                showToast('PDF exported successfully!', 'success');
            }, 1500);
        }

        function exportExcel() {
            showToast('Excel export started...', 'info');
            // Simulate Excel export
            setTimeout(() => {
                showToast('Excel exported successfully!', 'success');
            }, 1500);
        }

        // Backup functions
        function createBackup() {
            showToast('Creating backup...', 'info');
            // Simulate backup creation
            setTimeout(() => {
                showToast('Backup created successfully!', 'success');
            }, 2000);
        }

        function restoreBackup() {
            showToast('Restoring backup...', 'info');
            // Simulate backup restoration
            setTimeout(() => {
                showToast('Backup restored successfully!', 'success');
            }, 2000);
        }

        // Initialize Charts
        function initializeCharts() {
            // Bar Chart
            const barCtx = document.getElementById('barChart');
            if (barCtx) {
                new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Cases',
                            data: [12, 19, 8, 15, 22, 18],
                            backgroundColor: 'rgba(37, 99, 235, 0.8)',
                            borderColor: 'rgba(37, 99, 235, 1)',
                            borderWidth: 1,
                            borderRadius: 6
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
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Pie Chart
            const pieCtx = document.getElementById('pieChart');
            if (pieCtx) {
                new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Open', 'Pending', 'Closed'],
                        datasets: [{
                            data: [89, 45, 158],
                            backgroundColor: [
                                'rgba(34, 197, 94, 0.8)',
                                'rgba(251, 191, 36, 0.8)',
                                'rgba(107, 114, 128, 0.8)'
                            ],
                            borderWidth: 2,
                            borderColor: document.documentElement.classList.contains('dark') ? '#1E293B' : '#FFFFFF'
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

            // Line Chart
            const lineCtx = document.getElementById('lineChart');
            if (lineCtx) {
                new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Hearings',
                            data: [5, 8, 12, 7, 15, 10],
                            borderColor: 'rgba(168, 85, 247, 1)',
                            backgroundColor: 'rgba(168, 85, 247, 0.1)',
                            tension: 0.4,
                            fill: true
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
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Revenue Chart (Reports page)
            const revenueCtx = document.getElementById('revenueChart');
            if (revenueCtx) {
                new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Revenue',
                            data: [45000, 52000, 48000, 61000, 55000, 67000],
                            borderColor: 'rgba(34, 197, 94, 1)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.4,
                            fill: true
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
                                    callback: function(value) {
                                        return '$' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Case Types Chart (Reports page)
            const caseTypesCtx = document.getElementById('caseTypesChart');
            if (caseTypesCtx) {
                new Chart(caseTypesCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Civil', 'Criminal', 'Corporate', 'Family'],
                        datasets: [{
                            data: [45, 25, 20, 10],
                            backgroundColor: [
                                'rgba(37, 99, 235, 0.8)',
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(34, 197, 94, 0.8)',
                                'rgba(168, 85, 247, 0.8)'
                            ],
                            borderWidth: 2,
                            borderColor: document.documentElement.classList.contains('dark') ? '#1E293B' : '#FFFFFF'
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
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize charts after a short delay to ensure elements are rendered
            setTimeout(initializeCharts, 100);
        });

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    if (modal.id === 'caseModal') {
                        closeCaseModal();
                    } else if (modal.id === 'clientModal') {
                        closeClientModal();
                    } else if (modal.id === 'documentUploadModal') {
                        closeDocumentUploadModal();
                    }
                }
            });
        });
    // Close menu when pressing Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.getElementById('userMenu').classList.add('hidden');
        }
    });
    </script>
</body>
</html>