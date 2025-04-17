<x-guest-layout>
    <div class="flex flex-col md:flex-row items-center justify-center min-h-screen p-4 bg-white">
        <div class="md:w-1/2 max-w-md w-full space-y-6">
            <h1 class="text-3xl font-bold text-center text-emerald-800">SisaRasa</h1>
            <p class="text-center text-gray-600 text-sm">Welcome back! Please enter your details.</p>
            
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus class="w-full p-2 border rounded" />
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required class="w-full p-2 border rounded" />
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Create Password</label>
                    <input id="password" name="password" type="password" required class="w-full p-2 border rounded" />
                </div>

                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="remember_me" name="remember" class="h-4 w-4">
                    <label for="remember_me" class="text-sm text-gray-600">Remember me</label>
                </div>

                <button type="submit" class="w-full bg-emerald-800 text-white py-2 rounded hover:bg-emerald-700 transition">Sign up</button>
            </form>

            <p class="text-center text-sm text-gray-500">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-emerald-600 hover:underline">Login here!</a>
            </p>
        </div>

        <div class="hidden md:block md:w-1/2 p-8">
            <img src="{{ asset('images/webfoto.png') }}" alt="Food Lover" class="rounded-full mx-auto" />
        </div>
    </div>
</x-guest-layout>
