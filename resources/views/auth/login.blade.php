<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Mini-Rb</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-md">
        <a href="{{ route('home') }}" class="flex justify-center items-center space-x-2 text-rose-500 mb-6 hover:text-rose-600 transition">
            <img src="{{ asset('images/logo.png') }}" class="h-8 w-8" alt="Mini-Rb Logo">
            <span class="font-bold text-2xl tracking-tighter">Mini-Rb</span>
        </a>
        <h3 class="text-xl mb-6 text-center font-semibold">Se connecter</h3>

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500" required>
                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-1">Mot de passe</label>
                <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500" required>
                @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full bg-rose-500 text-white py-3 rounded-lg font-bold hover:bg-rose-600 transition">
                Se connecter
            </button>
        </form>

        <p class="mt-4 text-center text-sm text-gray-500">
            Pas encore de compte ? <a href="{{ route('register') }}" class="text-rose-500 font-semibold">S'inscrire</a>
        </p>
    </div>
</body>
</html>