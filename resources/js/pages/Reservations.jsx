import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import api from '../api';
import Layout from '../components/Layout';
import { ErrorAlert, SuccessAlert } from '../components/Alert';

const STATUS_COLORS = {
    pending: 'bg-yellow-100 text-yellow-700',
    accepted: 'bg-green-100 text-green-700',
    refused: 'bg-red-100 text-red-600',
    cancelled: 'bg-gray-100 text-gray-500',
};

const STATUS_LABELS = {
    pending: 'En attente',
    accepted: 'Acceptée',
    refused: 'Refusée',
    cancelled: 'Annulée',
};

const formatDate = (d) => new Date(d).toLocaleDateString('fr-FR');

export default function Reservations() {
    const [data, setData] = useState(null);
    const [success, setSuccess] = useState(null);
    const [error, setError] = useState(null);

    const load = () => api.get('/reservations').then((res) => setData(res.data));

    useEffect(() => { load(); }, []);

    const callAction = async (url, method = 'patch') => {
        setError(null);
        setSuccess(null);
        try {
            const { data: res } = await api({ url, method });
            setSuccess(res.message);
            load();
        } catch (err) {
            setError(err.response?.data?.message || 'Erreur.');
        }
    };

    if (!data) return <Layout><p className="p-10 text-gray-400">Chargement...</p></Layout>;

    const { mes_reservations, reservations_recues } = data;

    return (
        <Layout>
            <main className="max-w-5xl mx-auto px-8 py-10">
                <SuccessAlert message={success} />
                <ErrorAlert errors={error} />

                <h1 className="text-3xl font-bold mb-10">Mes Réservations</h1>

                <div className="mb-14">
                    <h2 className="text-xl font-bold mb-6 flex items-center gap-2">
                        <span>✈️</span> Mes voyages
                        <span className="text-sm font-normal text-gray-400">({mes_reservations.length})</span>
                    </h2>

                    {mes_reservations.length === 0 ? (
                        <div className="bg-white rounded-2xl border p-10 text-center text-gray-400">
                            Vous n'avez pas encore effectué de réservation.
                            <Link to="/" className="block mt-2 text-rose-500 font-semibold hover:underline">
                                Découvrir des logements
                            </Link>
                        </div>
                    ) : (
                        mes_reservations.map((r) => (
                            <ReservationCard
                                key={r.id}
                                reservation={r}
                                actions={
                                    <>
                                        {['pending', 'accepted'].includes(r.status) && (
                                            <button
                                                onClick={() => {
                                                    if (confirm('Annuler cette réservation ?')) {
                                                        callAction(`/reservations/${r.id}/cancel`);
                                                    }
                                                }}
                                                className="text-sm text-red-500 hover:text-red-700 font-semibold underline"
                                            >
                                                Annuler
                                            </button>
                                        )}
                                        {r.status === 'accepted' && !r.has_my_review && (
                                            <Link
                                                to={`/annonces/${r.annonce.id}`}
                                                className="text-sm text-rose-500 hover:text-rose-700 font-semibold underline"
                                            >
                                                Laisser un avis
                                            </Link>
                                        )}
                                        {r.has_my_review && (
                                            <span className="text-sm text-gray-400">✓ Avis publié</span>
                                        )}
                                    </>
                                }
                            />
                        ))
                    )}
                </div>

                {reservations_recues.length > 0 && (
                    <div>
                        <h2 className="text-xl font-bold mb-6 flex items-center gap-2">
                            <span>🏠</span> Réservations reçues sur mes annonces
                            <span className="text-sm font-normal text-gray-400">({reservations_recues.length})</span>
                        </h2>

                        {reservations_recues.map((r) => (
                            <ReservationCard
                                key={r.id}
                                reservation={r}
                                showTraveler
                                actions={
                                    r.status === 'pending' && (
                                        <div className="flex gap-2">
                                            <button
                                                onClick={() => callAction(`/reservations/${r.id}/accept`)}
                                                className="bg-green-500 text-white px-4 py-1 rounded-lg text-sm font-semibold hover:bg-green-600 transition"
                                            >
                                                Accepter
                                            </button>
                                            <button
                                                onClick={() => {
                                                    if (confirm('Refuser cette réservation ?')) {
                                                        callAction(`/reservations/${r.id}/refuse`);
                                                    }
                                                }}
                                                className="bg-red-100 text-red-600 px-4 py-1 rounded-lg text-sm font-semibold hover:bg-red-200 transition"
                                            >
                                                Refuser
                                            </button>
                                        </div>
                                    )
                                }
                            />
                        ))}
                    </div>
                )}
            </main>
        </Layout>
    );
}

function ReservationCard({ reservation: r, actions, showTraveler }) {
    return (
        <div className="bg-white rounded-2xl shadow-sm border mb-4 p-6 flex flex-col md:flex-row gap-6">
            <div className="w-full md:w-36 h-28 rounded-xl overflow-hidden flex-shrink-0">
                {r.annonce.image_url ? (
                    <img src={r.annonce.image_url} alt={r.annonce.titre} className="w-full h-full object-cover" />
                ) : (
                    <div className="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400 text-sm">
                        Pas d'image
                    </div>
                )}
            </div>

            <div className="flex-1">
                <Link to={`/annonces/${r.annonce.id}`} className="text-lg font-bold hover:text-rose-500 transition">
                    {r.annonce.titre}
                </Link>
                {showTraveler ? (
                    <p className="text-gray-500 text-sm">
                        Voyageur : <strong>{r.traveler_name}</strong>
                    </p>
                ) : (
                    <p className="text-gray-500 text-sm">{r.annonce.ville}</p>
                )}
                <p className="text-gray-600 text-sm mt-1">
                    Du <strong>{formatDate(r.start_date)}</strong> au <strong>{formatDate(r.end_date)}</strong>
                </p>
                <p className="text-gray-600 text-sm">
                    {r.nb_voyageurs} {r.nb_voyageurs === 1 ? 'voyageur' : 'voyageurs'} · Total : <strong>{r.total_price}$</strong>
                </p>
            </div>

            <div className="flex flex-col items-end justify-between gap-3">
                <span className={`px-3 py-1 rounded-full text-sm font-semibold ${STATUS_COLORS[r.status] || 'bg-gray-100 text-gray-500'}`}>
                    {STATUS_LABELS[r.status] || r.status}
                </span>
                {actions}
            </div>
        </div>
    );
}
