<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification email - Mini-Rb</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta http-equiv="refresh" content="5">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-md text-center">
        <div class="text-rose-500 font-bold text-2xl mb-6">Mini-Rb</div>

        <div class="text-5xl mb-4">📧</div>
        <h2 class="text-2xl font-bold mb-2">Vérifiez votre email</h2>
        <p class="text-gray-500 mb-2">
            Nous avons envoyé un lien de vérification à votre adresse email.
            Cliquez sur le lien pour activer votre compte.
        </p>
        <p class="text-gray-400 text-sm mb-6">Cette page se rafraîchit automatiquement toutes les 5 secondes.</p>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('verification.send') }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-rose-500 text-white py-3 rounded-lg font-semibold hover:bg-rose-600 transition">
                Renvoyer le lien de vérification
            </button>
        </form>

        <form action="{{ route('logout') }}" method="POST" class="mt-4">
            @csrf
            <button type="submit" class="text-gray-400 hover:text-gray-600 text-sm">
                Se déconnecter
            </button>
        </form>
    </div>
</body>
</html>