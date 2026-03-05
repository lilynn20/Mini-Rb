<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publier une annonce - Mini-Rb</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm py-4 px-8 flex justify-between items-center border-b">
        <a href="{{ route('home') }}" class="text-rose-500 font-bold text-2xl">Mini-Rb</a>
    </nav>

    <main class="max-w-2xl mx-auto px-8 py-10 bg-white shadow-md rounded-xl mt-10">
        <h1 class="text-2xl font-bold mb-6">Mettez votre logement sur Mini-Rb</h1>

        <form action="{{ route('annonces.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Titre de l'annonce</label>
                    <input type="text" name="titre" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" placeholder="Ex: Bel appartement au centre ville" required>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Description</label>
                    <textarea name="description" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" placeholder="Décrivez votre logement..." required></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Ville</label>
                        <input type="text" name="ville" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" placeholder="Ex: Paris" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Adresse</label>
                        <input type="text" name="adresse" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" placeholder="Ex: 12 rue de la Paix" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Prix par nuit ($)</label>
                        <input type="number" name="prix_par_nuit" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" min="0" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Nombre de chambres</label>
                        <input type="number" name="nombre_de_chambres" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" min="1" value="1" required>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-1">URL de l'image</label>
                    <input type="url" name="image_url" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" placeholder="https://exemple.com/image.jpg">
                    <p class="text-xs text-gray-500 mt-1">Collez l'URL d'une image pour illustrer votre logement.</p>
                </div>
            </div>

            <button type="submit" class="w-full bg-rose-500 text-white py-3 rounded-lg font-bold mt-8 hover:bg-rose-600 transition">Publier mon annonce</button>
        </form>
    </main>
</body>
</html>
