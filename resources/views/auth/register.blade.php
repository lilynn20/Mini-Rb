<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Mini-Rb</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-md">
        <a href="{{ route('home') }}" class="flex justify-center items-center space-x-2 text-rose-500 mb-6 hover:text-rose-600 transition">
            <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 fill-current"><path d="M16 1c2.008 0 3.463.963 4.751 3.269l.533 1.025c1.954 3.83 6.114 12.54 7.1 14.836l.145.353c.667 1.591.91 2.472.96 3.396l.01.415v.301c0 4.262-2.87 7.405-6.66 7.405-2.008 0-3.463-.963-4.751-3.269l-.533-1.025c-1.954-3.83-6.114-12.54-7.1-14.836l-.145-.353c-.667-1.591-.91-2.472-.96-3.396l-.01-.415v-.301c0-4.262 2.87-7.405 6.66-7.405z"></path></svg>
            <span class="font-bold text-2xl tracking-tighter">Mini-Rb</span>
        </a>
        <h3 class="text-xl mb-6 text-center font-semibold">Créer un compte</h3>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-1">Nom</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500" required>
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500" required>
                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-1">Mot de passe</label>
                <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500" required>
                @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-1">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500" required>
            </div>

            <button type="submit" class="w-full bg-rose-500 text-white py-3 rounded-lg font-bold hover:bg-rose-600 transition">
                S'inscrire
            </button>
        </form>

        <p class="mt-4 text-center text-sm text-gray-500">
            Déjà un compte ? <a href="{{ route('login') }}" class="text-rose-500 font-semibold">Se connecter</a>
        </p>
    </div>
</body>
</html>