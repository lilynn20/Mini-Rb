import { useEffect, useState } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import toast from 'react-hot-toast';
import api from '../api';
import Layout from '../components/Layout';
import Map from '../components/Map';
import FavoriteButton from '../components/FavoriteButton';
import { CardGridSkeleton } from '../components/Skeleton';

export default function Home() {
    const [searchParams, setSearchParams] = useSearchParams();
    const [annonces, setAnnonces] = useState([]);
    const [loading, setLoading] = useState(true);
    const [loadingMore, setLoadingMore] = useState(false);
    const [page, setPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [total, setTotal] = useState(0);
    const [filters, setFilters] = useState({
        ville: searchParams.get('ville') || '',
        prix_max: searchParams.get('prix_max') || '',
        nb_personne: searchParams.get('nb_personne') || '',
    });
    useEffect(() => {
        if (searchParams.get('verified') === '1') {
            toast.success('Email vérifié avec succès ! Bienvenue sur Mini-Rb 🎉');
            const params = new URLSearchParams(searchParams);
            params.delete('verified');
            setSearchParams(params, { replace: true });
        }
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    const buildParams = (extraParams = {}) => {
        const params = { ...extraParams };
        if (searchParams.get('ville')) params.ville = searchParams.get('ville');
        if (searchParams.get('prix_max')) params.prix_max = searchParams.get('prix_max');
        if (searchParams.get('nb_personne')) params.nb_personne = searchParams.get('nb_personne');
        return params;
    };

    useEffect(() => {
        setLoading(true);
        setPage(1);
        api.get('/annonces', { params: buildParams({ page: 1 }) })
            .then((res) => {
                setAnnonces(res.data.data);
                setLastPage(res.data.last_page);
                setTotal(res.data.total);
            })
            .finally(() => setLoading(false));
    }, [searchParams]);

    const loadMore = async () => {
        const next = page + 1;
        setLoadingMore(true);
        try {
            const { data } = await api.get('/annonces', { params: buildParams({ page: next }) });
            setAnnonces((prev) => [...prev, ...data.data]);
            setPage(next);
            setLastPage(data.last_page);
        } finally {
            setLoadingMore(false);
        }
    };

    const handleSearch = (e) => {
        e.preventDefault();
        const params = {};
        if (filters.ville) params.ville = filters.ville;
        if (filters.prix_max) params.prix_max = filters.prix_max;
        if (filters.nb_personne) params.nb_personne = filters.nb_personne;
        setSearchParams(params);
    };

    const clearFilters = () => {
        setFilters({ ville: '', prix_max: '', nb_personne: '' });
        setSearchParams({});
    };

    const hasFilters = filters.ville || filters.prix_max || filters.nb_personne;

    return (
        <Layout>
            <main className="max-w-7xl mx-auto px-8 pb-10">
                <div className="relative mb-20">
                    <div className="relative rounded-3xl overflow-hidden h-[400px] flex items-center justify-center text-center">
                        <img
                            src="https://images.unsplash.com/photo-1501785888041-af3ef285b470?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80"
                            alt="Vacances"
                            className="absolute inset-0 w-full h-full object-cover"
                        />
                        <div className="absolute inset-0 bg-black/40"></div>
                        <div className="relative z-10 px-4 pb-12">
                            <h1 className="text-4xl md:text-5xl font-extrabold text-white mb-4 drop-shadow-lg">
                                Trouvez des locations de vacances dans le monde entier
                            </h1>
                            <p className="text-xl md:text-2xl text-white font-medium drop-shadow-md">
                                Réservez vos prochaines vacances dès maintenant !
                            </p>
                        </div>
                    </div>

                    <div className="absolute left-1/2 -translate-x-1/2 bottom-0 translate-y-1/2 w-full px-4 max-w-4xl">
                        <div className="bg-white p-2 rounded-full shadow-2xl border flex items-center">
                            <form onSubmit={handleSearch} className="flex w-full items-center">
                                <SearchField
                                    label="Destination"
                                    name="ville"
                                    placeholder="Où allez-vous ?"
                                    value={filters.ville}
                                    onChange={(v) => setFilters({ ...filters, ville: v })}
                                />
                                <SearchField
                                    label="Prix Max"
                                    name="prix_max"
                                    type="number"
                                    placeholder="Budget max"
                                    value={filters.prix_max}
                                    onChange={(v) => setFilters({ ...filters, prix_max: v })}
                                />
                                <SearchField
                                    label="Nb Personne"
                                    name="nb_personne"
                                    type="number"
                                    placeholder="Combien ?"
                                    value={filters.nb_personne}
                                    onChange={(v) => setFilters({ ...filters, nb_personne: v })}
                                    last
                                />
                                <div className="px-2">
                                    <button type="submit" className="bg-rose-500 text-white p-4 rounded-full hover:bg-rose-600 transition flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        <span className="ml-2 font-bold hidden md:inline">Rechercher</span>
                                    </button>
                                </div>
                                {hasFilters && (
                                    <button type="button" onClick={clearFilters} className="text-xs text-gray-400 hover:text-rose-500 underline ml-2 pr-4">
                                        Effacer
                                    </button>
                                )}
                            </form>
                        </div>
                    </div>
                </div>

                <h1 className="text-3xl font-bold mb-8">Découvrez votre prochain séjour</h1>

                {loading ? (
                    <CardGridSkeleton count={8} />
                ) : (
                    <>
                        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                            {annonces.length === 0 ? (
                                <p className="text-gray-500 col-span-full text-center py-10">Aucune annonce disponible pour le moment.</p>
                            ) : (
                                annonces.map((a) => <AnnonceCard key={a.id} annonce={a} />)
                            )}
                        </div>

                        {annonces.length > 0 && page < lastPage && (
                            <div className="text-center mt-12">
                                <button
                                    type="button"
                                    onClick={loadMore}
                                    disabled={loadingMore}
                                    className="bg-rose-500 text-white px-8 py-3 rounded-full font-semibold hover:bg-rose-600 transition disabled:opacity-50"
                                >
                                    {loadingMore ? 'Chargement...' : `Charger plus (${total - annonces.length} restantes)`}
                                </button>
                            </div>
                        )}
                    </>
                )}

                {!loading && annonces.length > 0 && (
                    <div className="mt-16">
                        <h2 className="text-2xl font-bold mb-6">Carte des logements</h2>
                        <Map annonces={annonces} />
                    </div>
                )}

                <div className="mt-20 border-t pt-16">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-12">
                        <div>
                            <h2 className="text-2xl font-bold mb-4">Réservez votre logement en toute simplicité</h2>
                            <p className="text-gray-600 leading-relaxed">
                                Envie de voyager sans stress ? Sur MiniRBnB, trouvez et réservez votre hébergement rapidement, en toute sécurité et sans mauvaise surprise. Découvrez un large choix d'appartements, maisons, villas et chambres adaptées à tous les budgets. Toutes les annonces sont vérifiées et les réservations sont simples et sécurisées.
                            </p>
                        </div>
                        <div>
                            <h2 className="text-2xl font-bold mb-4">Trouvez le logement qui vous correspond</h2>
                            <p className="text-gray-600 leading-relaxed">
                                Que vous cherchiez une escapade romantique, un séjour en famille ou un voyage entre amis, MiniRBnB vous aide à trouver l'hébergement idéal. Grâce à nos filtres pratiques, choisissez la ville, les dates, le nombre de voyageurs et les équipements dont vous avez besoin.
                            </p>
                        </div>
                    </div>
                </div>
            </main>
        </Layout>
    );
}

function SearchField({ label, name, type = 'text', placeholder, value, onChange, last }) {
    return (
        <div className={`flex-1 px-6 ${last ? '' : 'border-r'}`}>
            <label className="block text-[10px] font-bold uppercase text-gray-500">{label}</label>
            <input
                type={type}
                name={name}
                value={value}
                onChange={(e) => onChange(e.target.value)}
                placeholder={placeholder}
                className="w-full outline-none text-sm font-medium"
                min={type === 'number' ? '1' : undefined}
            />
        </div>
    );
}

function AnnonceCard({ annonce }) {
    return (
        <Link to={`/annonces/${annonce.id}`} className="group block">
            <div className="aspect-square overflow-hidden rounded-xl mb-3 relative">
                <img
                    src={annonce.image_url || 'https://via.placeholder.com/400x400?text=Pas+d+image'}
                    alt={annonce.titre}
                    className="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                />
                <div className="absolute top-2 right-2">
                    <FavoriteButton annonceId={annonce.id} initial={annonce.is_favorited} />
                </div>
            </div>
            <h3 className="font-bold text-gray-900">{annonce.ville}</h3>
            <p className="text-gray-500 text-sm truncate">{annonce.titre}</p>
            <p className="mt-1 font-semibold">
                <span className="text-gray-900">{annonce.prix_par_nuit}$</span>{' '}
                <span className="text-gray-500 font-normal">par nuit</span>
            </p>

            <div className="mt-2 flex items-center justify-between">
                <div className="flex items-center text-sm">
                    <svg className="w-4 h-4 text-yellow-400 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span className="ml-1 text-gray-700 font-medium">4.5</span>
                </div>
                <span className="bg-rose-500 text-white text-[10px] font-bold px-2 py-1 rounded uppercase group-hover:bg-rose-600 transition shadow-sm">
                    Détails
                </span>
            </div>
        </Link>
    );
}
