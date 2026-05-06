import { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import DatePicker from 'react-datepicker';
import 'react-datepicker/dist/react-datepicker.css';
import api from '../api';
import { useAuth } from '../contexts/AuthContext';
import Layout from '../components/Layout';
import Gallery from '../components/Gallery';
import { ErrorAlert, SuccessAlert } from '../components/Alert';

const toIsoDate = (d) => (d ? d.toISOString().slice(0, 10) : '');

export default function AnnonceShow() {
    const { id } = useParams();
    const { user } = useAuth();
    const navigate = useNavigate();
    const [data, setData] = useState(null);
    const [success, setSuccess] = useState(null);
    const [errors, setErrors] = useState(null);

    const [startDate, setStartDate] = useState(null);
    const [endDate, setEndDate] = useState(null);
    const [nbVoyageurs, setNbVoyageurs] = useState(1);
    const [review, setReview] = useState({ rating: 5, comment: '' });

    const load = () => {
        api.get(`/annonces/${id}`).then((res) => setData(res.data));
    };

    useEffect(() => { load(); }, [id]);

    const handleReserve = async (e) => {
        e.preventDefault();
        setErrors(null);
        setSuccess(null);
        if (!startDate || !endDate) {
            setErrors({ dates: ['Choisissez une date d\'arrivée et de départ.'] });
            return;
        }
        try {
            const { data: res } = await api.post(`/annonces/${id}/reserver`, {
                start_date: toIsoDate(startDate),
                end_date: toIsoDate(endDate),
                nb_voyageurs: nbVoyageurs,
            });
            setSuccess(res.message);
            setStartDate(null);
            setEndDate(null);
            setNbVoyageurs(1);
            load();
        } catch (err) {
            setErrors(err.response?.data?.errors || err.response?.data?.message);
        }
    };

    const handleDelete = async () => {
        if (!confirm('Supprimer cette annonce ?')) return;
        try {
            await api.delete(`/annonces/${id}`);
            navigate('/');
        } catch (err) {
            setErrors(err.response?.data?.message || 'Erreur lors de la suppression.');
        }
    };

    const handleAvisSubmit = async (e) => {
        e.preventDefault();
        if (!data.eligible_reservation) return;
        setErrors(null);
        try {
            const { data: res } = await api.post(`/reservations/${data.eligible_reservation.id}/avis`, review);
            setSuccess(res.message);
            setReview({ rating: 5, comment: '' });
            load();
        } catch (err) {
            setErrors(err.response?.data?.errors || err.response?.data?.message);
        }
    };

    const handleAvisDelete = async (avisId) => {
        if (!confirm('Supprimer cet avis ?')) return;
        try {
            const { data: res } = await api.delete(`/avis/${avisId}`);
            setSuccess(res.message);
            load();
        } catch (err) {
            setErrors(err.response?.data?.message);
        }
    };

    if (!data) return <Layout><p className="p-10 text-gray-400">Chargement...</p></Layout>;

    const { annonce, avis, avg_rating, avis_count, eligible_reservation, can_update, blocked_dates } = data;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const tomorrow = new Date(today.getTime() + 86400000);
    const excludedDates = (blocked_dates || []).map((d) => new Date(d));
    const maxGuests = annonce.nombre_de_chambres * 2;

    return (
        <Layout>
            <main className="max-w-5xl mx-auto px-8 py-10">
                <SuccessAlert message={success} />
                <ErrorAlert errors={errors} />

                <h1 className="text-3xl font-bold mb-4">{annonce.titre}</h1>
                <p className="text-gray-600 mb-6 underline font-semibold">
                    {annonce.adresse}, {annonce.ville}
                </p>

                <Gallery images={annonce.images} alt={annonce.titre} />

                <div className="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div className="md:col-span-2">
                        <div className="flex justify-between items-start mb-2">
                            <h2 className="text-2xl font-bold">Logement proposé par {annonce.host_name}</h2>
                            {can_update && (
                                <div className="flex space-x-2">
                                    <Link to={`/annonces/${annonce.id}/edit`} className="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-200 transition">
                                        Modifier
                                    </Link>
                                    <button onClick={handleDelete} className="bg-red-50 text-red-600 px-4 py-2 rounded-lg font-semibold hover:bg-red-100 transition">
                                        Supprimer
                                    </button>
                                </div>
                            )}
                        </div>
                        <p className="text-gray-600 mb-6 border-b pb-6">{annonce.nombre_de_chambres} chambre(s)</p>

                        <h3 className="text-xl font-bold mb-4">À propos de ce logement</h3>
                        <p className="text-gray-700 leading-relaxed mb-10 whitespace-pre-line">{annonce.description}</p>

                        <div className="border-t pt-8">
                            <h3 className="text-xl font-bold mb-6">
                                Avis
                                {avg_rating !== null && (
                                    <span className="text-base font-normal text-gray-500 ml-2">
                                        ★ {avg_rating} · {avis_count} avis
                                    </span>
                                )}
                            </h3>

                            {avis.length === 0 ? (
                                <p className="text-gray-500">Aucun avis pour ce logement.</p>
                            ) : (
                                avis.map((a) => (
                                    <div key={a.id} className="mb-6 pb-6 border-b last:border-b-0">
                                        <div className="flex justify-between items-start">
                                            <div>
                                                <p className="font-semibold">{a.user_name}</p>
                                                <p className="text-yellow-400 text-sm">
                                                    {[1, 2, 3, 4, 5].map((i) => (i <= a.rating ? '★' : '☆')).join(' ')}
                                                </p>
                                            </div>
                                            <div className="flex items-center space-x-3">
                                                <span className="text-gray-400 text-sm">
                                                    {new Date(a.created_at).toLocaleDateString('fr-FR')}
                                                </span>
                                                {user && user.id === a.user_id && (
                                                    <button
                                                        onClick={() => handleAvisDelete(a.id)}
                                                        className="text-red-400 hover:text-red-600 text-sm"
                                                    >
                                                        Supprimer
                                                    </button>
                                                )}
                                            </div>
                                        </div>
                                        <p className="text-gray-700 mt-2">{a.comment}</p>
                                    </div>
                                ))
                            )}

                            {user && eligible_reservation && (
                                <div className="mt-8 bg-gray-50 rounded-xl p-6">
                                    <h4 className="font-bold text-lg mb-4">Laisser un avis</h4>
                                    <form onSubmit={handleAvisSubmit}>
                                        <div className="mb-4">
                                            <label className="block text-gray-700 font-semibold mb-2">Note</label>
                                            <div className="flex space-x-2">
                                                {[1, 2, 3, 4, 5].map((i) => (
                                                    <button
                                                        type="button"
                                                        key={i}
                                                        onClick={() => setReview({ ...review, rating: i })}
                                                        className={`text-2xl ${i <= review.rating ? 'text-yellow-400' : 'text-gray-300'} hover:text-yellow-300 transition`}
                                                    >
                                                        ★
                                                    </button>
                                                ))}
                                            </div>
                                        </div>
                                        <div className="mb-4">
                                            <label className="block text-gray-700 font-semibold mb-2">Commentaire</label>
                                            <textarea
                                                rows="3"
                                                value={review.comment}
                                                onChange={(e) => setReview({ ...review, comment: e.target.value })}
                                                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none"
                                                placeholder="Décrivez votre séjour..."
                                                required
                                            />
                                        </div>
                                        <button type="submit" className="bg-rose-500 text-white px-6 py-2 rounded-lg font-semibold hover:bg-rose-600 transition">
                                            Publier l'avis
                                        </button>
                                    </form>
                                </div>
                            )}
                        </div>
                    </div>

                    <div className="md:col-span-1">
                        <div className="border rounded-2xl p-6 shadow-xl sticky top-20">
                            <p className="text-2xl font-bold mb-6">
                                <span className="text-gray-900">{annonce.prix_par_nuit}$</span>
                                <span className="text-gray-500 font-normal text-base"> par nuit</span>
                            </p>

                            {user ? (
                                user.id === annonce.user_id ? (
                                    <div className="text-center py-4 text-gray-500 bg-gray-50 rounded-lg">
                                        C'est votre annonce
                                    </div>
                                ) : (
                                    <form onSubmit={handleReserve}>
                                        <div className="border rounded-lg mb-4">
                                            <div className="grid grid-cols-2 border-b">
                                                <div className="p-3 border-r">
                                                    <label className="block text-[10px] font-bold uppercase mb-1">Arrivée</label>
                                                    <DatePicker
                                                        selected={startDate}
                                                        onChange={(d) => {
                                                            setStartDate(d);
                                                            if (endDate && d && endDate <= d) setEndDate(null);
                                                        }}
                                                        selectsStart
                                                        startDate={startDate}
                                                        endDate={endDate}
                                                        minDate={today}
                                                        excludeDates={excludedDates}
                                                        dateFormat="dd/MM/yyyy"
                                                        placeholderText="JJ/MM/AAAA"
                                                        className="w-full text-sm outline-none bg-transparent"
                                                    />
                                                </div>
                                                <div className="p-3">
                                                    <label className="block text-[10px] font-bold uppercase mb-1">Départ</label>
                                                    <DatePicker
                                                        selected={endDate}
                                                        onChange={(d) => setEndDate(d)}
                                                        selectsEnd
                                                        startDate={startDate}
                                                        endDate={endDate}
                                                        minDate={startDate ? new Date(startDate.getTime() + 86400000) : tomorrow}
                                                        excludeDates={excludedDates}
                                                        dateFormat="dd/MM/yyyy"
                                                        placeholderText="JJ/MM/AAAA"
                                                        className="w-full text-sm outline-none bg-transparent"
                                                    />
                                                </div>
                                            </div>
                                            <div className="p-3">
                                                <label className="block text-[10px] font-bold uppercase mb-1">Voyageurs</label>
                                                <select
                                                    value={nbVoyageurs}
                                                    onChange={(e) => setNbVoyageurs(Number(e.target.value))}
                                                    className="w-full text-sm outline-none bg-transparent"
                                                >
                                                    {Array.from({ length: maxGuests }, (_, i) => i + 1).map((n) => (
                                                        <option key={n} value={n}>
                                                            {n} {n === 1 ? 'voyageur' : 'voyageurs'}
                                                        </option>
                                                    ))}
                                                </select>
                                                <p className="text-[10px] text-gray-400 mt-1">Max {maxGuests} ({annonce.nombre_de_chambres} chambre{annonce.nombre_de_chambres > 1 ? 's' : ''})</p>
                                            </div>
                                        </div>
                                        <button type="submit" className="w-full bg-rose-500 text-white py-3 rounded-lg font-bold hover:bg-rose-600 transition">
                                            Réserver
                                        </button>
                                    </form>
                                )
                            ) : (
                                <Link to="/login" className="block w-full bg-rose-500 text-white py-3 rounded-lg font-bold hover:bg-rose-600 transition text-center">
                                    Connectez-vous pour réserver
                                </Link>
                            )}

                            <p className="text-center text-gray-500 text-sm mt-4">
                                Aucun montant ne vous sera débité pour le moment
                            </p>
                        </div>
                    </div>
                </div>
            </main>
        </Layout>
    );
}
