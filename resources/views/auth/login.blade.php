<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SisaRasa - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .form-input {
            background: #FAFAFA;
            border: 1px solid #E5E5E5;
            transition: all 0.2s ease;
        }
        .form-input:focus {
            outline: none;
            border-color: #10B981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
            background: white;
        }
        .form-input.error {
            border-color: #EF4444;
            background: #FEF2F2;
        }
        .btn-primary {
            background: #065F46;
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background: #047857;
            transform: translateY(-1px);
        }
        .hero-image {
            border-radius: 50%;
            width: 320px;
            height: 320px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .error-message {
            color: #EF4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .success-message {
            background: #D1FAE5;
            border: 1px solid #A7F3D0;
            color: #065F46;
            padding: 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex flex-col lg:flex-row items-center justify-center min-h-screen p-4">
        <!-- Left Side - Form -->
        <div class="lg:w-1/2 max-w-md w-full space-y-6 bg-white p-8 rounded-2xl shadow-sm">
            <!-- Header -->
            <div class="text-center space-y-2">
                <h1 class="text-3xl font-bold text-emerald-800">SisaRasa</h1>
                <p class="text-gray-500 text-sm">Welcome back! Please enter your details.</p>
            </div>
            
            <!-- Session Status -->
            @if (session('status'))
                <div class="success-message">
                    {{ session('status') }}
                </div>
            @endif
            
            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        value="{{ old('email') }}"
                        placeholder="Enter your email"
                        class="form-input w-full px-4 py-3 rounded-lg text-sm @error('email') error @enderror"
                        required 
                        autofocus 
                        autocomplete="username"
                    />
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        placeholder="••••••••"
                        class="form-input w-full px-4 py-3 rounded-lg text-sm @error('password') error @enderror"
                        required 
                        autocomplete="current-password"
                    />
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <input 
                            type="checkbox" 
                            id="remember_me" 
                            name="remember" 
                            class="h-4 w-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500"
                        />
                        <label for="remember_me" class="text-sm text-gray-600">Remember me</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium hover:underline">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="btn-primary w-full text-white py-3 rounded-lg font-medium text-sm"
                >
                    Log in
                </button>
            </form>

            <!-- Sign Up Link -->
            <p class="text-center text-sm text-gray-500">
                Don't have an account? 
                <a href="{{ route('register') }}" class="text-emerald-600 hover:text-emerald-700 font-medium hover:underline">Sign up for free!</a>
            </p>
        </div>

        <!-- Right Side - Image -->
        <div class="lg:w-1/2 flex justify-center items-center mt-8 lg:mt-0 lg:pl-12">
            <div class="hero-image">
                <img 
                    src="{{ asset('images/webfoto.png') }}" 
                    alt="Happy person enjoying SisaRasa food" 
                    class="w-72 h-72 object-cover rounded-full"
                />
            </div>
        </div>
    </div>
</body>
</html>
