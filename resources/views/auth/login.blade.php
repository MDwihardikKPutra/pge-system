<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - PGE System</title>
    <link rel="icon" type="image/png" href="{{ asset('logopge.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="antialiased" style="background-image: url('{{ asset('loginbg.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; min-height: 100vh;">
    <div class="min-h-screen flex items-center justify-center py-8 px-4">
        <div class="w-full max-w-5xl">
            <!-- Single Card with 2 Columns -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col lg:flex-row">
                <!-- Left Column - Login Form (1/2) -->
                <div class="flex-1 p-8 lg:p-10" style="background-color: #0a1628;">
                    <!-- Logo & Header -->
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 flex items-center justify-center">
                                <img src="{{ asset('logopge.png') }}" alt="PGE Logo" class="w-full h-full object-contain">
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-white">PGE System</h1>
                                <p class="text-sm text-gray-300">PT. PURI GANESHA ENGINEERING</p>
                            </div>
                        </div>
                        <h2 class="text-xl font-bold text-white mb-2">Log in to your Account</h2>
                        <p class="text-gray-300">Welcome back! Please enter your credentials to continue.</p>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 rounded-lg flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('login') }}" id="loginForm" class="space-y-5">
                        @csrf
                        
                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-white mb-2">Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                    </svg>
                                </div>
                                <input 
                                    id="email" 
                                    type="email" 
                                    class="w-full pl-12 pr-4 py-3 text-sm rounded-lg border border-slate-600 bg-slate-800/50 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 text-white placeholder-gray-400 transition-all" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    placeholder="Email"
                                    required 
                                    autofocus
                                >
                            </div>
                            @error('email')
                                <span class="mt-1.5 block text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-white mb-2">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input 
                                    id="password" 
                                    type="password" 
                                    class="w-full pl-12 pr-4 py-3 text-sm rounded-lg border border-slate-600 bg-slate-800/50 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 text-white placeholder-gray-400 transition-all" 
                                    name="password" 
                                    placeholder="Password"
                                    required
                                >
                            </div>
                            @error('password')
                                <span class="mt-1.5 block text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center cursor-pointer group">
                                <input 
                                    class="w-4 h-4 rounded border-slate-500 bg-slate-700 text-blue-400 focus:ring-2 focus:ring-blue-500/20 focus:ring-offset-0 cursor-pointer transition" 
                                    type="checkbox" 
                                    name="remember" 
                                    id="remember" 
                                    {{ old('remember') ? 'checked' : '' }}
                                >
                                <span class="ml-2 text-sm text-gray-300 group-hover:text-white transition">Remember me</span>
                            </label>
                        </div>
                        
                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg py-3 font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            Log In
                        </button>
                    </form>
                    
                    <!-- Quick Login (Development) -->
                    <div class="mt-6 pt-6 border-t border-slate-700">
                        <p class="text-xs text-gray-400 mb-3 text-center font-medium">Quick Login (Development)</p>
                        <div class="grid grid-cols-2 gap-3">
                            <button 
                                type="button" 
                                onclick="quickLogin('admin@pge.local', 'password')" 
                                class="px-4 py-2.5 bg-slate-700 hover:bg-red-600 border border-slate-600 hover:border-red-500 rounded-lg text-sm font-medium text-white transition-all hover:shadow-sm flex items-center justify-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>Admin</span>
                            </button>
                            <button 
                                type="button" 
                                onclick="quickLogin('user@pge.local', 'password')" 
                                class="px-4 py-2.5 bg-slate-700 hover:bg-emerald-600 border border-slate-600 hover:border-emerald-500 rounded-lg text-sm font-medium text-white transition-all hover:shadow-sm flex items-center justify-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>User</span>
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 mt-3 text-center">Click untuk auto-fill credentials</p>
                    </div>

                    <!-- Footer -->
                    <p class="text-xs text-gray-400 text-center mt-8">
                        © {{ date('Y') }} PT. PURI GANESHA ENGINEERING. All rights reserved.
                    </p>
                </div>

                <!-- Right Column - Image (Inside Card) (1/2) -->
                <div class="hidden lg:flex flex-1">
                    <img 
                        src="{{ asset('login.jpg') }}" 
                        alt="PGE System" 
                        class="w-full h-full object-cover"
                    >
                </div>
            </div>
        </div>
    </div>

    <script>
        function quickLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
            document.getElementById('email').focus();
        }
    </script>
</body>
</html>
