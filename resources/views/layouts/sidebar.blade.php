<!-- [file name]: sidebar.blade.php -->
<div id="sidebar" class="sidebar-transition w-64 bg-gradient-to-b from-gray-900 to-gray-800 text-white flex-shrink-0 lg:block hidden flex flex-col">
    <div class="p-6 flex-shrink-0">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-balance-scale text-white"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold">AI Legal</h1>
                <p class="text-xs text-gray-400">Professional Suite</p>
            </div>
        </div>
    </div>

    <!-- Scrollable Navigation -->
    <nav class="px-4 space-y-2 mt-6 flex-1 overflow-y-auto">

        @if(auth()->user()->hasPermission('dashboard.view'))
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
            <i class="fas fa-tachometer-alt w-5 text-center"></i>
            <span>Dashboard</span>
        </a>
        @endif

        @if(auth()->user()->hasPermission('cases.view'))
        <a href="{{ route('cases.index') }}" class="nav-item {{ request()->routeIs('cases.index') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
            <i class="fas fa-folder w-5 text-center"></i>
            <span>Cases</span>
        </a>
        @endif

        @if(auth()->user()->hasPermission('clients.view'))
        <a href="{{ route('clients.index') }}" class="nav-item {{ request()->routeIs('clients.index') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
            <i class="fas fa-users w-5 text-center"></i>
            <span>Clients</span>
        </a>
        @endif

        @if(auth()->user()->hasPermission('hearings.view'))
        <a href="{{ route('hearings') }}" class="nav-item {{ request()->routeIs('hearings') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
            <i class="fas fa-calendar-alt w-5 text-center"></i>
            <span>Hearings</span>
        </a>
        @endif

        @if(auth()->user()->hasPermission('documents.view'))
        <a href="{{ route('documents') }}" class="nav-item {{ request()->routeIs('documents') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
            <i class="fas fa-file-alt w-5 text-center"></i>
            <span>Documents</span>
        </a>
        @endif

        @if(auth()->user()->hasPermission('reports.view'))
        <a href="{{ route('reports') }}" class="nav-item {{ request()->routeIs('reports') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
            <i class="fas fa-chart-bar w-5 text-center"></i>
            <span>Reports</span>
        </a>
        @endif

        @if(auth()->user()->hasPermission('settings.view'))
        <a href="{{ route('settings') }}" class="nav-item {{ request()->routeIs('settings') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
            <i class="fas fa-cog w-5 text-center"></i>
            <span>Settings</span>
        </a>
        @endif

        @auth
            @if(auth()->user()->isAdmin())
                <div class="px-4 mt-6 mb-2">
                    <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Administration</p>
                </div>

                @if(auth()->user()->hasPermission('users.view'))
                <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
                    <i class="fas fa-users w-5 text-center"></i>
                    <span>User Management</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('roles.view'))
                <a href="{{ route('admin.roles.index') }}" class="nav-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
                    <i class="fas fa-user-shield w-5 text-center"></i>
                    <span>Roles & Permissions</span>
                </a>
                @endif
            @endif

            <div class="px-4 mt-6 mb-2">
                <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Subscription</p>
            </div>

            @if(auth()->user()->hasPermission('subscriptions.view'))
            <a href="{{ route('subscriptions.plans') }}" class="nav-item {{ request()->routeIs('subscriptions.plans') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
                <i class="fas fa-credit-card w-5 text-center"></i>
                <span>Subscription Plans</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('subscriptions.manage'))
            <a href="{{ route('subscriptions.history') }}" class="nav-item {{ request()->routeIs('subscriptions.history') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
                <i class="fas fa-history w-5 text-center"></i>
                <span>Billing History</span>
            </a>
            @endif
        @endauth
    </nav>

    <div class="p-4 flex-shrink-0">
        <div class="bg-gray-800 rounded-xl p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-robot text-white"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium">AI Assistant</p>
                    <p class="text-xs text-gray-400">Ready to help</p>
                </div>
            </div>
            <button class="w-full mt-3 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm transition-colors">
                Ask AI
            </button>
        </div>
    </div>
</div>

<!-- Mobile Sidebar -->
<div id="mobileSidebar" class="mobile-sidebar fixed inset-0 z-50 lg:hidden">
    <div class="fixed inset-0 bg-black bg-opacity-70" onclick="toggleMobileSidebar()"></div>
    <div class="relative w-64 h-full bg-gradient-to-b from-gray-900 to-gray-800 text-white flex flex-col">
        <div class="p-6 flex-shrink-0">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-balance-scale text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold">AI Legal</h1>
                        <p class="text-xs text-gray-400">Professional Suite</p>
                    </div>
                </div>
                <button onclick="toggleMobileSidebar()" class="text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <nav class="px-4 space-y-2 mt-6 flex-1 overflow-y-auto">
            @if(auth()->user()->hasPermission('dashboard.view'))
            <a href="{{ route('dashboard') }}" onclick="toggleMobileSidebar()" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
                <i class="fas fa-tachometer-alt w-5 text-center"></i>
                <span>Dashboard</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('cases.view'))
            <a href="{{ route('cases.index') }}" onclick="toggleMobileSidebar()" class="nav-item {{ request()->routeIs('cases.index') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
                <i class="fas fa-folder w-5 text-center"></i>
                <span>Cases</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('clients.view'))
            <a href="{{ route('clients.index') }}" onclick="toggleMobileSidebar()" class="nav-item {{ request()->routeIs('clients.index') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
                <i class="fas fa-users w-5 text-center"></i>
                <span>Clients</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('hearings.view'))
            <a href="{{ route('hearings') }}" onclick="toggleMobileSidebar()" class="nav-item {{ request()->routeIs('hearings') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
                <i class="fas fa-calendar-alt w-5 text-center"></i>
                <span>Hearings</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('documents.view'))
            <a href="{{ route('documents') }}" onclick="toggleMobileSidebar()" class="nav-item {{ request()->routeIs('documents') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
                <i class="fas fa-file-alt w-5 text-center"></i>
                <span>Documents</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('reports.view'))
            <a href="{{ route('reports') }}" onclick="toggleMobileSidebar()" class="nav-item {{ request()->routeIs('reports') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
                <i class="fas fa-chart-bar w-5 text-center"></i>
                <span>Reports</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('settings.view'))
            <a href="{{ route('settings') }}" onclick="toggleMobileSidebar()" class="nav-item {{ request()->routeIs('settings') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
                <i class="fas fa-cog w-5 text-center"></i>
                <span>Settings</span>
            </a>
            @endif

            @auth
                @if(auth()->user()->isAdmin())
                    <div class="px-4 mt-6 mb-2">
                        <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Administration</p>
                    </div>

                    @if(auth()->user()->hasPermission('users.view'))
                    <a href="{{ route('admin.users.index') }}" onclick="toggleMobileSidebar()" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
                        <i class="fas fa-users w-5 text-center"></i>
                        <span>User Management</span>
                    </a>
                    @endif

                    @if(auth()->user()->hasPermission('roles.view'))
                    <a href="{{ route('admin.roles.index') }}" onclick="toggleMobileSidebar()" class="nav-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
                        <i class="fas fa-user-shield w-5 text-center"></i>
                        <span>Roles & Permissions</span>
                    </a>
                    @endif
                @endif

                <div class="px-4 mt-6 mb-2">
                    <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Subscription</p>
                </div>

                @if(auth()->user()->hasPermission('subscriptions.view'))
                <a href="{{ route('subscriptions.plans') }}" onclick="toggleMobileSidebar()" class="nav-item {{ request()->routeIs('subscriptions.plans') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
                    <i class="fas fa-credit-card w-5 text-center"></i>
                    <span>Subscription Plans</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('subscriptions.manage'))
                <a href="{{ route('subscriptions.history') }}" onclick="toggleMobileSidebar()" class="nav-item {{ request()->routeIs('subscriptions.history') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg">
                    <i class="fas fa-history w-5 text-center"></i>
                    <span>Billing History</span>
                </a>
                @endif
            @endauth
        </nav>

        <div class="p-4 flex-shrink-0">
            <div class="bg-gray-800 rounded-xl p-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-robot text-white"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium">AI Assistant</p>
                        <p class="text-xs text-gray-400">Ready to help</p>
                    </div>
                </div>
                <button class="w-full mt-3 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm transition-colors">
                    Ask AI
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Bottom Navigation -->
<div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 z-50">
    <div class="flex justify-around py-2">
        @if(auth()->user()->hasPermission('dashboard.view'))
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center p-2 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'text-blue-600' : '' }}">
            <i class="fas fa-tachometer-alt text-lg mb-1"></i>
            <span class="text-xs">Dashboard</span>
        </a>
        @endif

        @if(auth()->user()->hasPermission('cases.view'))
        <a href="{{ route('cases.index') }}" class="flex flex-col items-center p-2 rounded-lg transition-colors {{ request()->routeIs('cases.index') ? 'text-blue-600' : '' }}">
            <i class="fas fa-folder text-lg mb-1"></i>
            <span class="text-xs">Cases</span>
        </a>
        @endif

        @if(auth()->user()->hasPermission('clients.view'))
        <a href="{{ route('clients.index') }}" class="flex flex-col items-center p-2 rounded-lg transition-colors {{ request()->routeIs('clients.index') ? 'text-blue-600' : '' }}">
            <i class="fas fa-users text-lg mb-1"></i>
            <span class="text-xs">Clients</span>
        </a>
        @endif

        @if(auth()->user()->hasPermission('documents.view'))
        <a href="{{ route('documents') }}" class="flex flex-col items-center p-2 rounded-lg transition-colors {{ request()->routeIs('documents') ? 'text-blue-600' : '' }}">
            <i class="fas fa-file-alt text-lg mb-1"></i>
            <span class="text-xs">Docs</span>
        </a>
        @endif

        <button onclick="toggleMobileSidebar()" class="flex flex-col items-center p-2 rounded-lg transition-colors">
            <i class="fas fa-bars text-lg mb-1"></i>
            <span class="text-xs">More</span>
        </button>
    </div>
</div>
