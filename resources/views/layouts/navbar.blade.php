<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SisaRasa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>
    @stack('styles')
</head>
<body class="bg-white min-h-screen font-sans text-gray-900">
    <!-- Navbar -->    
    <nav x-data="{ open: false }" class="w-full bg-white border-b border-gray-100">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="/" class="text-2xl font-bold text-secondary px-2 py-1">
                            SisaRasa
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <a href="/" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->is('/') ? 'border-secondary text-gray-900' : 'border-transparent text-gray-500' }} text-sm font-medium leading-5 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                            {{ __('Beranda') }}
                        </a>
                        <a href="/menu" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->is('menu') ? 'border-secondary text-gray-900' : 'border-transparent text-gray-500' }} text-sm font-medium leading-5 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                            {{ __('Menu') }}
                        </a>
                        <a href="/lokasi" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->is('lokasi') ? 'border-secondary text-gray-900' : 'border-transparent text-gray-500' }} text-sm font-medium leading-5 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                            {{ __('Lokasi') }}
                        </a>
                        <a href="{{ route('riwayat.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('riwayat.index') ? 'border-secondary text-gray-900' : 'border-transparent text-gray-500' }} text-sm font-medium leading-5 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                            {{ __('Riwayat') }}
                        </a>
                    </div>
                </div>

                <!-- Settings Dropdown -->
                <div class="hidden sm:flex items-center space-x-4">
                    <a href="/chatify" class="text-xl text-gray-500 hover:text-gray-700"><i class="far fa-comment-alt"></i></a>
                    <a href="{{ route('cart.index') }}" class="text-xl text-gray-500 hover:text-gray-700"><i class="fas fa-shopping-bag"></i></a>
                    <a href="{{ route('notifications.index') }}" class="text-xl text-gray-500 hover:text-gray-700 relative">
                        <i class="far fa-bell"></i>
                        @auth
                            @php
                                $unreadCount = \App\Models\Notification::where('user_id', Auth::id())->where('status', 'unread')->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        @endauth
                    </a>
                    <img src="{{ asset('img/id.png') }}" alt="Indonesian Flag" class="w-8 h-5 shadow-md rounded-sm">
                    
                    @auth
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
                                <div @click="open = ! open">
                                    <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                        <img class="h-8 w-8 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=7F9CF5&background=EBF4FF" alt="{{ Auth::user()->name }}" />
                                    </button>
                                </div>

                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 z-50 mt-2 w-48 rounded-md shadow-lg origin-top-right bg-white ring-1 ring-black ring-opacity-5 py-1"
                                     style="display: none;"
                                     @click="open = false">
                                    <div class="block px-4 py-2 text-xs text-gray-400">{{ Auth::user()->name }}</div>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            {{ __('Log Out') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="hidden md:block px-4 py-2 rounded bg-secondary text-white font-semibold hover:bg-secondary/90">Login</a>
                        <a href="{{ route('register') }}" class="hidden md:block ml-2 px-4 py-2 rounded border border-secondary text-secondary font-semibold hover:bg-secondary/10">Daftar</a>
                    @endauth
                </div>

                <!-- Mobile Navigation -->
                <div class="flex items-center sm:hidden">
                    <div class="flex items-center space-x-1">
                        <a href="/chatify" class="text-lg text-gray-500 hover:text-gray-700 px-1"><i class="far fa-comment-alt"></i></a>
                        <a href="{{ route('cart.index') }}" class="text-lg text-gray-500 hover:text-gray-700 px-1"><i class="fas fa-shopping-bag"></i></a>
                        <a href="{{ route('notifications.index') }}" class="text-lg text-gray-500 hover:text-gray-700 px-1 relative">
                            <i class="far fa-bell"></i>
                            @auth
                                @php
                                    $unreadCount = \App\Models\Notification::where('user_id', Auth::id())->where('status', 'unread')->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">
                                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                    </span>
                                @endif
                            @endauth
                        </a>
                        <img src="{{ asset('img/id.png') }}" alt="Indonesian Flag" class="w-6 h-4 shadow-md rounded-sm mx-1">
                    </div>
                    
                    <!-- Hamburger -->
                    <div class="flex items-center">
                        <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <a href="/" class="block w-full ps-3 pe-4 py-2 border-l-4 border-secondary text-start text-base font-medium text-gray-700 bg-secondary/10 focus:outline-none focus:text-gray-800 focus:bg-secondary/10 focus:border-secondary transition duration-150 ease-in-out">
                    {{ __('Beranda') }}
                </a>
                <a href="/menu" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                    {{ __('Menu') }}
                </a>
                <a href="/lokasi" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                    {{ __('Lokasi') }}
                </a>
                <a href="{{ route('riwayat.index') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                    {{ __('Riwayat') }}
                </a>
            </div>

            <!-- Responsive Settings Options -->
            @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-start ps-3 pe-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="mt-3 space-y-1">
                    <a href="{{ route('login') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                        {{ __('Login') }}
                    </a>
                    <a href="{{ route('register') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                        {{ __('Register') }}
                    </a>
                </div>
            </div>
            @endauth
        </div>
    </nav>

    <!-- Page Content -->
    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>
    @stack('scripts')
</body>
</html>
