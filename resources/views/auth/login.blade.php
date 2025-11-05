<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - PGE System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 antialiased">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="w-full max-w-md mx-auto bg-white rounded-2xl shadow-xl p-8">
            <div class="mb-8 text-center">
                <span class="inline-block text-3xl mb-2">🏢</span>
                <h1 class="font-extrabold text-2xl text-gray-900">PGE System</h1>
                <p class="text-gray-500 text-base">Welcome back, please sign in.</p>
            </div>
            
            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}" id="loginForm" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input 
                        id="email" 
                        type="email" 
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-indigo-200 focus:ring-2 text-base bg-gray-50" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus
                    >
                    @error('email')
                        <span class="mt-1 block text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input 
                        id="password" 
                        type="password" 
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-indigo-200 focus:ring-2 text-base bg-gray-50" 
                        name="password" 
                        required
                    >
                    @error('password')
                        <span class="mt-1 block text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input 
                            class="mr-2 rounded" 
                            type="checkbox" 
                            name="remember" 
                            id="remember" 
                            {{ old('remember') ? 'checked' : '' }}
                        >
                        <span class="text-sm text-gray-600">Remember me</span>
                    </label>
                </div>
                
                <button 
                    type="submit" 
                    class="w-full bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg py-3 font-semibold transition text-lg shadow-lg"
                >
                    Sign in
                </button>
            </form>
            
            <!-- Quick Login Buttons -->
            <div class="mt-6 border-t pt-6">
                <p class="text-xs text-gray-500 mb-3 text-center">Quick Login (Development)</p>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="quickLogin('admin@pge.local', 'password')" 
                        class="px-3 py-2 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg text-xs font-medium text-red-700 transition-all hover:shadow-md">
                        👨‍💼 Admin
                    </button>
                    <button type="button" onclick="quickLogin('user@pge.local', 'password')" 
                        class="px-3 py-2 bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg text-xs font-medium text-green-700 transition-all hover:shadow-md">
                        👤 User
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-3 text-center">Click tombol untuk auto-fill credentials</p>
            </div>
        </div>
    </div>

    <script>
        function quickLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>


