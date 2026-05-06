import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import api from '../api';
import Layout from '../components/Layout';
import FavoriteButton from '../components/FavoriteButton';
import { CardGridSkeleton } from '../components/Skeleton';

export default function Favorites() {
    const [favorites, setFavorites] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        api.get('/favorites')
            .then((res) => setFavorites(res.data))
            .finally(() => setLoading(false));
    }, []);

    const removeFromList = (id) => {
        setFavorites((prev) => prev.filter((a) => a.id !== id));
    };

    return (
        <Layout>
            <main className="max-w-7xl mx-auto px-8 py-10">
                <h1 className="text-3xl font-bold mb-10">Mes favoris</h1>

                {loading ? (
                    <CardGridSkeleton count={4} />
                ) : favorites.length === 0 ? (
                    <div className="bg-white rounded-2xl border p-10 text-center text-gray-400">
                        Vous n'avez pas encore de favoris.
                        <Link to="/" className="block mt-2 text-rose-500 font-semibold hover:underline">
                            Découvrir des logements
                        </Link>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                        {favorites.map((a) => (
                            <Link key={a.id} to={`/annonces/${a.id}`} className="group block">
                                <div className="aspect-square overflow-hidden rounded-xl mb-3 relative">
                                    <img
                                        src={a.image_url || 'https://via.placeholder.com/400x400?text=Pas+d+image'}
                                        alt={a.titre}
                                        className="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                    />
                                    <div className="absolute top-2 right-2">
                                        <FavoriteButton
                                            annonceId={a.id}
                                            initial={true}
                                            onChange={(fav) => !fav && removeFromList(a.id)}
                                        />
                                    </div>
                                </div>
                                <h3 className="font-bold text-gray-900">{a.ville}</h3>
                                <p className="text-gray-500 text-sm truncate">{a.titre}</p>
                                <p className="mt-1 font-semibold">
                                    <span className="text-gray-900">{a.prix_par_nuit}$</span>{' '}
                                    <span className="text-gray-500 font-normal">par nuit</span>
                                </p>
                            </Link>
                        ))}
                    </div>
                )}
            </main>
        </Layout>
    );
}
