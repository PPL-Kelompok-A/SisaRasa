<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Mitra Dashboard</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <nav x-data="{ open: false }" class="bg-white border-b border-gray-200 fixed w-full top-0 z-50">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <div class="flex-shrink-0">
                            <a href="{{ route('mitra.dashboard') }}" class="text-2xl font-bold text-secondary">
                                SisaRasa
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden sm:ml-16 sm:flex sm:space-x-8">
                            <a href="{{ route('mitra.dashboard') }}" 
                               class="{{ request()->routeIs('mitra.dashboard') ? 'border-secondary text-secondary' : 'border-transparent text-gray-500 hover:text-secondary hover:border-secondary' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Dashboard
                            </a>
                            <a href="{{ route('mitra.foods.index') }}" 
                               class="{{ request()->routeIs('mitra.foods.*') ? 'border-secondary text-secondary' : 'border-transparent text-gray-500 hover:text-secondary hover:border-secondary' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Menu
                            </a>
                            <a href="{{ route('mitra.orders.index') }}" 
                               class="{{ request()->routeIs('mitra.orders.index') ? 'border-secondary text-secondary' : 'border-transparent text-gray-500 hover:text-secondary hover:border-secondary' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Pesanan
                            </a>
                            <a href="{{ route('mitra.orders.history.index') }}" 
                               class="{{ request()->routeIs('mitra.orders.history.*') ? 'border-secondary text-secondary' : 'border-transparent text-gray-500 hover:text-secondary hover:border-secondary' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Riwayat Pesanan
                            </a>
                        </div>
                    </div>
                    
                    <!-- Mobile menu button -->
                    <div class="flex items-center sm:hidden">
                        <button @click="open = !open" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-secondary hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-secondary" aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <!-- Icon when menu is closed -->
                            <svg x-show="!open" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <!-- Icon when menu is open -->
                            <svg x-show="open" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Right side icons -->
                    <div class="hidden sm:flex items-center space-x-4">
                        <a href="/chatify" class="text-xl text-gray-500 hover:text-gray-700"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4-4-4z" />
                        </svg>
                        </a>
                        <button class="text-gray-500 hover:text-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </button>
                        <button class="text-gray-500 hover:text-secondary">
                            <img src="{{ asset('img/id.png') }}" alt="ID Flag" class="h-4 w-6 object-cover">
                        </button>
                        <div class="relative">
    <button type="button" class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary" id="user-menu-button">
        <span class="sr-only">Open user menu</span>
        <div class="w-8 h-8 rounded-full bg-secondary text-white flex items-center justify-center">
            {{ substr(Auth::user()->name, 0, 1) }}
        </div>
    </button>
    <!-- Desktop user dropdown -->
    <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1 z-50">
        <div class="px-4 py-2 text-sm text-gray-700 border-b">{{ Auth::user()->name }}</div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
        </form>
    </div>
</div>
                    </div>
                </div>
                
                <!-- Mobile menu, show/hide based on menu state. -->
                <div x-show="open" class="sm:hidden" id="mobile-menu" style="display: none;">
                    <div class="pt-2 pb-3 space-y-1">
                        <a href="{{ route('mitra.dashboard') }}" 
                           class="{{ request()->routeIs('mitra.dashboard') ? 'bg-secondary text-white' : 'text-gray-500 hover:bg-gray-100 hover:text-secondary' }} block px-3 py-2 rounded-md text-base font-medium">
                            Dashboard
                        </a>
                        <a href="{{ route('mitra.foods.index') }}" 
                           class="{{ request()->routeIs('mitra.foods.*') ? 'bg-secondary text-white' : 'text-gray-500 hover:bg-gray-100 hover:text-secondary' }} block px-3 py-2 rounded-md text-base font-medium">
                            Menu
                        </a>
                        <a href="{{ route('mitra.orders.index') }}" 
                           class="{{ request()->routeIs('mitra.orders.index') ? 'bg-secondary text-white' : 'text-gray-500 hover:bg-gray-100 hover:text-secondary' }} block px-3 py-2 rounded-md text-base font-medium">
                            Pesanan
                        </a>
                        <a href="{{ route('mitra.orders.history.index') }}" 
                           class="{{ request()->routeIs('mitra.orders.history.*') ? 'bg-secondary text-white' : 'text-gray-500 hover:bg-gray-100 hover:text-secondary' }} block px-3 py-2 rounded-md text-base font-medium">
                            Riwayat Pesanan
                        </a>
                    </div>
                    
                    <!-- Mobile user menu -->
                    <div class="pt-4 pb-3 border-t border-gray-200">
                        <div class="flex items-center px-3">
                            <div class="w-10 h-10 rounded-full bg-secondary text-white flex items-center justify-center">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="ml-3">
                                <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                                <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                            </div>
                        </div>
                        <div class="mt-3 space-y-1">
                            <div class="flex space-x-4 px-3">
                                <button class="text-gray-500 hover:text-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4-4-4z" />
                                    </svg>
                                </button>
                                <button class="text-gray-500 hover:text-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </button>
                                <button class="text-gray-500 hover:text-secondary">
                                    <img src="{{ asset('img/id.png') }}" alt="ID Flag" class="h-4 w-6 object-cover">
                                </button>
                            </div>
                            <!-- Mobile logout -->
                            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <div class="py-6 mt-16">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    @if (session('success'))
                        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </div>
        </main>
    </div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userBtn = document.getElementById('user-menu-button');
        const dropdown = document.getElementById('user-dropdown');
        if (userBtn && dropdown) {
            userBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', function() {
                dropdown.classList.add('hidden');
            });
        }
    });
</script>
@stack('scripts')
</body>
</html>
