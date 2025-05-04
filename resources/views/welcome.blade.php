<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SisaRasa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                        <a href="/" class="inline-flex items-center px-1 pt-1 border-b-2 border-secondary text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-secondary transition duration-150 ease-in-out">
                            {{ __('Beranda') }}
                        </a>
                        <a href="#menu" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                            {{ __('Menu') }}
                        </a>
                        <a href="#lokasi" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                            {{ __('Lokasi') }}
                        </a>
                        <a href="#riwayat" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                            {{ __('Riwayat') }}
                        </a>
                    </div>
                </div>

                <!-- Settings Dropdown -->
                <div class="hidden sm:flex items-center space-x-4">
                    <a href="/chatify" class="text-xl text-gray-500 hover:text-gray-700"><i class="far fa-comment-alt"></i></a>
                    <a href="{{ route('cart.index') }}" class="text-xl text-gray-500 hover:text-gray-700"><i class="fas fa-shopping-bag"></i></a>
                    <a href="#" class="text-xl text-gray-500 hover:text-gray-700"><i class="far fa-bell"></i></a>
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
                        <a href="#" class="text-lg text-gray-500 hover:text-gray-700 px-1"><i class="far fa-comment-alt"></i></a>
                        <a href="#" class="text-lg text-gray-500 hover:text-gray-700 px-1"><i class="fas fa-shopping-bag"></i></a>
                        <a href="#" class="text-lg text-gray-500 hover:text-gray-700 px-1"><i class="far fa-bell"></i></a>
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
                <a href="#menu" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                    {{ __('Menu') }}
                </a>
                <a href="#lokasi" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                    {{ __('Lokasi') }}
                </a>
                <a href="#riwayat" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
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

    <!-- Hero Section -->
    <section class="w-full py-12 px-6 md:px-12 lg:px-24 flex flex-col md:flex-row items-center justify-between">
        <div class="md:w-1/2 mb-8 md:mb-0">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                <p class="text-lg mb-6">Lindungi Bumi, Mulai <span class="text-secondary">Food Waste</span> Sekarang!</p>
                Save <span class="text-secondary">Food</span><br>
                Save Budget<br>
                Save Planet<br>
            </h1>
            <p class="text-lg mb-6">Sisa Rasa: Selamatkan Rasa, Kurangi Sisa!</p>
        </div>
        
        <div class="md:w-1/2 relative">
            <!-- Circular image container -->
            <div class="relative w-full max-w-md mx-auto">
                <div class="rounded-full bg-secondary overflow-hidden aspect-square">
                    <img src="{{ asset('img/hero.png') }}" alt="Food Image" class="w-full h-full object-cover">
                </div>
                
                <!-- Hot spicy food label -->
                <div class="absolute top-10 left-0 bg-white px-4 py-2 rounded-full shadow-md">
                    <p class="text-accent font-medium">Hot spicy Food 🌶️</p>
                </div>
            </div>
            
            <!-- Food cards -->
            <div class="absolute -bottom-5 w-full flex justify-center space-x-4">
                <!-- Spicy noodles card -->
                <div class="bg-white rounded-lg shadow-md p-3 w-64 flex items-center">
                    <img src="{{ asset('images/spicy-noodles.png') }}" alt="Spicy noodles" class="w-16 h-16 object-cover rounded-md mr-3">
                    <div>
                        <h3 class="font-medium">Spicy noodles</h3>
                        <div class="flex items-center mb-1">
                            <div class="flex text-yellow-400 text-xs">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                            <span class="text-xs text-gray-500 ml-1">3.5</span>
                        </div>
                        <p class="text-accent font-bold">Rp.6k</p>
                    </div>
                </div>
                
                <!-- Vegetarian salad card -->
                <div class="bg-white rounded-lg shadow-md p-3 w-64 flex items-center">
                    <img src="{{ asset('images/vegetarian-salad.png') }}" alt="Vegetarian salad" class="w-16 h-16 object-cover rounded-md mr-3">
                    <div>
                        <h3 class="font-medium">Vegetarian salad</h3>
                        <div class="flex items-center mb-1">
                            <div class="flex text-yellow-400 text-xs">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                            <span class="text-xs text-gray-500 ml-1">4.5</span>
                        </div>
                        <p class="text-accent font-bold">Rp.9k</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Categories Section -->
    <section class="py-16 px-6 bg-white">
        <h3 class="text-sm font-medium text-accent text-center mb-2">CUSTOMER FAVORITES</h3>
        <h2 class="text-2xl md:text-3xl font-bold text-center mb-8">Popular Catagories</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-5xl mx-auto">
            <!-- Category 1 - Main Dish -->
            <div class="bg-white rounded-lg p-6 flex flex-col items-center text-center shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer">
                <div class="bg-[#C1F1C6] rounded-full p-4 mb-4 w-28 h-28 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('images/main-dish-icon.png') }}" alt="Main Dish" class="w-20 h-20 object-contain">
                </div>
                <h3 class="font-semibold mb-1">Main Dish</h3>
                <p class="text-xs text-gray-500">(86 dishes)</p>
            </div>
            
            <!-- Category 2 - Breakfast -->
            <div class="bg-white rounded-lg p-6 flex flex-col items-center text-center shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer">
                <div class="bg-[#C1F1C6] rounded-full p-4 mb-4 w-28 h-28 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('images/breakfast-icon.png') }}" alt="Breakfast" class="w-20 h-20 object-contain">
                </div>
                <h3 class="font-semibold mb-1">Break Fast</h3>
                <p class="text-xs text-gray-500">(12 break fast)</p>
            </div>
            
            <!-- Category 3 - Dessert -->
            <div class="bg-white rounded-lg p-6 flex flex-col items-center text-center shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer">
                <div class="bg-[#C1F1C6] rounded-full p-4 mb-4 w-28 h-28 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('images/dessert-icon.png') }}" alt="Dessert" class="w-20 h-20 object-contain">
                </div>
                <h3 class="font-semibold mb-1">Dessert</h3>
                <p class="text-xs text-gray-500">(48 dessert)</p>
            </div>
            
            <!-- Category 4 - Browse All -->
            <div class="bg-white rounded-lg p-6 flex flex-col items-center text-center shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer">
                <div class="bg-[#C1F1C6] rounded-full p-4 mb-4 w-28 h-28 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('images/browse-all-icon.png') }}" alt="Browse All" class="w-20 h-20 object-contain">
                </div>
                <h3 class="font-semibold mb-1">Browse All</h3>
                <p class="text-xs text-gray-500">(255 items)</p>
            </div>
        </div>
    </section>
    
    <!-- Special Food Section -->
    <section class="py-16 px-6 bg-white">
        <h3 class="text-sm font-medium text-accent text-center mb-2">SPECIAL FOOD</h3>
        <h2 class="text-2xl md:text-3xl font-bold text-center mb-8">Special food from<br>our menu</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-5xl mx-auto">
            <!-- Food 1 - Fattoush salad -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 p-4 flex flex-col items-center cursor-pointer">
                <div class="mb-3">
                    <img src="{{ asset('images/fattoush-salad.png') }}" alt="Fattoush salad" class="w-24 h-24 md:w-32 md:h-32 object-cover rounded-full">
                </div>
                <h3 class="font-semibold text-sm md:text-lg mb-1">Fattoush salad</h3>
                <p class="text-xs text-gray-500 mb-2 hidden md:block">Description of the item</p>
                <div class="flex justify-between w-full items-center">
                    <span class="font-medium text-sm md:text-lg">Rp.11k</span>
                    <div class="flex items-center">
                        <span class="text-yellow-400 mr-1">⭐</span>
                        <span class="text-xs md:text-sm">4.5</span>
                    </div>
                </div>
            </div>
            
            <!-- Food 2 - Vegetable salad -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 p-4 flex flex-col items-center cursor-pointer">
                <div class="mb-3">
                    <img src="{{ asset('images/vegetable-salad.png') }}" alt="Vegetable salad" class="w-24 h-24 md:w-32 md:h-32 object-cover rounded-full">
                </div>
                <h3 class="font-semibold text-sm md:text-lg mb-1">Vegetable salad</h3>
                <p class="text-xs text-gray-500 mb-2 hidden md:block">Description of the item</p>
                <div class="flex justify-between w-full items-center">
                    <span class="font-medium text-sm md:text-lg">Rp.9k</span>
                    <div class="flex items-center">
                        <span class="text-yellow-400 mr-1">⭐</span>
                        <span class="text-xs md:text-sm">4.8</span>
                    </div>
                </div>
            </div>
            
            <!-- Food 3 - Egg Vegie salad -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 p-4 flex flex-col items-center cursor-pointer">
                <div class="mb-3">
                    <img src="{{ asset('images/egg-vegie-salad.png') }}" alt="Egg Vegie salad" class="w-24 h-24 md:w-32 md:h-32 object-cover rounded-full">
                </div>
                <h3 class="font-semibold text-sm md:text-lg mb-1">Egg Vegie salad</h3>
                <p class="text-xs text-gray-500 mb-2 hidden md:block">Description of the item</p>
                <div class="flex justify-between w-full items-center">
                    <span class="font-medium text-sm md:text-lg">Rp.11k</span>
                    <div class="flex items-center">
                        <span class="text-yellow-400 mr-1">⭐</span>
                        <span class="text-xs md:text-sm">4.2</span>
                    </div>
                </div>
            </div>
            
            <!-- Food 4 - Browse All -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 p-4 flex flex-col items-center cursor-pointer">
                <div class="mb-3">
                    <img src="{{ asset('images/fattoush-salad.png') }}" alt="Browse All" class="w-24 h-24 md:w-32 md:h-32 object-cover rounded-full">
                </div>
                <h3 class="font-semibold text-sm md:text-lg mb-1">Browse All</h3>
                <p class="text-xs text-gray-500 mb-2 hidden md:block">View all special foods</p>
                <div class="flex justify-between w-full items-center">
                    <span class="font-medium text-sm md:text-lg">20+ items</span>
                    <div class="flex items-center">
                        <i class="fas fa-arrow-right text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Testimonial Section -->
    <section class="py-16 px-6 bg-white">
        <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center">
            <!-- Chef Image -->
            <div class="md:w-1/3 mb-8 md:mb-0">
                <img src="{{ asset('images/chef.png') }}" alt="Our Best Chef" class="w-full max-w-xs mx-auto">
            </div>
            
            <!-- Testimonials Content -->
            <div class="md:w-2/3 md:pl-12">
                <h3 class="text-sm font-medium text-accent mb-2">TESTIMONIALS</h3>
                <h2 class="text-2xl md:text-3xl font-bold mb-4">What Our Customers<br>Say About Us</h2>
                <p class="text-gray-600 mb-6">Sisa Rasa benar-benar solusi hemat buat aku! Bisa dapetin makanan enak dari restoran favorit dengan harga miring, sekaligus bantu kurangi food waste.</p>
                
                <!-- Customer Feedback -->
                <div class="mb-4">
                    <h4 class="font-medium mb-2">Customer Feedback</h4>
                    <div class="flex items-center">
                        <!-- Customer Avatars -->
                        <div class="flex -space-x-2 mr-3">
                            <img src="{{ asset('images/customer1.png') }}" alt="Customer 1" class="w-8 h-8 rounded-full border-2 border-white">
                            <img src="{{ asset('images/customer2.png') }}" alt="Customer 2" class="w-8 h-8 rounded-full border-2 border-white">
                            <img src="{{ asset('images/customer3.png') }}" alt="Customer 3" class="w-8 h-8 rounded-full border-2 border-white">
                        </div>
                        <!-- Rating -->
                        <div class="flex items-center">
                            <span class="text-yellow-400 mr-1">⭐</span>
                            <span class="font-medium">4.8</span>
                            <span class="text-gray-500 text-sm ml-1">(12k Reviews)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="w-full bg-white border-t py-8">
        <div class="container mx-auto px-6 md:px-12">
            <div class="flex flex-row justify-center items-center space-x-8">
                <div class="flex space-x-4">
                    <a href="#" class="bg-[#EDFFEF] w-10 h-10 flex items-center justify-center rounded-full text-secondary hover:text-secondary"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="bg-[#EDFFEF] w-10 h-10 flex items-center justify-center rounded-full text-secondary hover:text-secondary"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="bg-[#EDFFEF] w-10 h-10 flex items-center justify-center rounded-full text-secondary hover:text-secondary"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="bg-[#EDFFEF] w-10 h-10 flex items-center justify-center rounded-full text-secondary hover:text-secondary"><i class="fab fa-youtube"></i></a>
                </div>
                
                <div class="text-sm text-gray-600">
                    <p>Copyright &copy; 2025 Sisa Rasa | All rights reserved</p>
                </div>
            </div>
        </div>
    </footer>

    @if (Route::has('login'))
        <div class="h-14.5 hidden lg:block"></div>
    @endif
</body>
</html>
