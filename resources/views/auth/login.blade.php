<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - mini rb</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center text-rose-500">mini rb</h2>
        <h3 class="text-xl mb-4">Se connecter</h3>
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Se connecter en tant que</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="role_view" value="voyageur" class="peer hidden" checked>
                        <div class="p-2 text-center border rounded-lg peer-checked:border-rose-500 peer-checked:bg-rose-50 hover:bg-gray-50 transition text-sm">Voyageur</div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="role_view" value="hote" class="peer hidden">
                        <div class="p-2 text-center border rounded-lg peer-checked:border-rose-500 peer-checked:bg-rose-50 hover:bg-gray-50 transition text-sm">Hôte</div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="role_view" value="admin" class="peer hidden">
                        <div class="p-2 text-center border rounded-lg peer-checked:border-rose-500 peer-checked:bg-rose-50 hover:bg-gray-50 transition text-sm">Admin</div>
                    </label>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500" required>
                @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
            <div class="mb-6">
                <label class="block text-gray-700">Mot de passe</label>
                <input type="password" name="password" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500" required>
                @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="w-full bg-rose-500 text-white py-2 rounded-lg font-semibold hover:bg-rose-600">Se connecter</button>
        </form>
        <p class="mt-4 text-center text-sm">Pas encore de compte ? <a href="{{ route('register') }}" class="text-rose-500">S'inscrire</a></p>
    </div>
</body>
</html>
