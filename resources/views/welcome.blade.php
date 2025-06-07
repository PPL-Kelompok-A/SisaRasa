@extends('layouts.navbar')

@section('content')
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
@endsection
