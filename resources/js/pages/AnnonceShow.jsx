import { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import DatePicker from 'react-datepicker';
import 'react-datepicker/dist/react-datepicker.css';
import toast from 'react-hot-toast';
import api from '../api';
import { useAuth } from '../contexts/AuthContext';
import Layout from '../components/Layout';
import Gallery from '../components/Gallery';
import Map from '../components/Map';
import FavoriteButton from '../components/FavoriteButton';
import { ErrorAlert } from '../components/Alert';

const toIsoDate = (d) => (d ? d.toISOString().slice(0, 10) : '');

const CLEANING_FEE = 20;
const SERVICE_FEE_RATE = 0.10;

function computeBreakdown(start, end, pricePerNight) {
    if (!start || !end) return null;
    const nights = Math.round((end - start) / 86400000);
    if (nights <= 0) return null;
    const subtotal = nights * pricePerNight;
    const service = Math.round(subtotal * SERVICE_FEE_RATE * 100) / 100;
    return {
        nights,
        subtotal,
        cleaning: CLEANING_FEE,
        service,
        total: subtotal + CLEANING_FEE + service,
    };
}

export default function AnnonceShow() {
    const { id } = useParams();
    const { user } = useAuth();
    const navigate = useNavigate();
    const [data, setData] = useState(null);
    const [errors, setErrors] = useState(null);

    const [startDate, setStartDate] = useState(null);
    const [endDate, setEndDate] = useState(null);
    const [nbVoyageurs, setNbVoyageurs] = useState(1);
    const [review, setReview] = useState({
        rating_cleanliness: 5,
        rating_communication: 5,
        rating_location: 5,
        rating_value: 5,
        comment: '',
    });

    const load = () => {
        api.get(`/annonces/${id}`).then((res) => setData(res.data));
    };

    useEffect(() => { load(); }, [id]);

    const handleReserve = async (e) => {
        e.preventDefault();
        setErrors(null);
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
            toast.success(res.message);
            setStartDate(null);
            setEndDate(null);
            setNbVoyageurs(1);
            load();
        } catch (err) {
            const data = err.response?.data;
            if (data?.errors) setErrors(data.errors);
            else toast.error(data?.message || 'Erreur lors de la réservation.');
        }
    };

    const handleDelete = async () => {
        if (!confirm('Supprimer cette annonce ?')) return;
        try {
            const { data: res } = await api.delete(`/annonces/${id}`);
            toast.success(res.message);
            navigate('/');
        } catch (err) {
            toast.error(err.response?.data?.message || 'Erreur lors de la suppression.');
        }
    };

    const handleAvisSubmit = async (e) => {
        e.preventDefault();
        if (!data.eligible_reservation) return;
        setErrors(null);
        try {
            const { data: res } = await api.post(`/reservations/${data.eligible_reservation.id}/avis`, review);
            toast.success(res.message);
            setReview({
                rating_cleanliness: 5,
                rating_communication: 5,
                rating_location: 5,
                rating_value: 5,
                comment: '',
            });
            load();
        } catch (err) {
            const errData = err.response?.data;
            if (errData?.errors) setErrors(errData.errors);
            else toast.error(errData?.message || 'Erreur.');
        }
    };

    const handleAvisDelete = async (avisId) => {
        if (!confirm('Supprimer cet avis ?')) return;
        try {
            const { data: res } = await api.delete(`/avis/${avisId}`);
            toast.success(res.message);
            load();
        } catch (err) {
            toast.error(err.response?.data?.message || 'Erreur.');
        }
    };

    if (!data) return <Layout><p className="p-10 text-gray-400">Chargement...</p></Layout>;

    const { annonce, avis, avg_rating, criteria_averages, avis_count, eligible_reservation, can_update, blocked_dates } = data;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const tomorrow = new Date(today.getTime() + 86400000);
    const excludedDates = (blocked_dates || []).map((d) => new Date(d));
    const maxGuests = annonce.nombre_de_chambres * 2;
    const breakdown = computeBreakdown(startDate, endDate, Number(annonce.prix_par_nuit));

    return (
        <Layout>
            <main className="max-w-5xl mx-auto px-8 py-10">
                <ErrorAlert errors={errors} />

                <div className="flex justify-between items-start mb-4">
                    <h1 className="text-3xl font-bold">{annonce.titre}</h1>
                    <FavoriteButton annonceId={annonce.id} initial={annonce.is_favorited} size={28} />
                </div>
                <p className="text-gray-600 mb-6 underline font-semibold">
                    {annonce.adresse}, {annonce.ville}
                </p>

                <Gallery images={annonce.images} alt={annonce.titre} />

                <div className="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div className="md:col-span-2">
                        <div className="flex justify-between items-start mb-2">
                            <h2 className="text-2xl font-bold">Logement proposé par {annonce.host_name}</h2>
                            {can_update ? (
                                <div className="flex space-x-2">
                                    <Link to={`/annonces/${annonce.id}/edit`} className="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-200 transition">
                                        Modifier
                                    </Link>
                                    <button onClick={handleDelete} className="bg-red-50 text-red-600 px-4 py-2 rounded-lg font-semibold hover:bg-red-100 transition">
                                        Supprimer
                                    </button>
                                </div>
                            ) : user && (
                                <Link
                                    to={`/messages?to=${annonce.user_id}`}
                                    className="bg-rose-50 text-rose-600 px-4 py-2 rounded-lg font-semibold hover:bg-rose-100 transition text-sm"
                                >
                                    Contacter l'hôte
                                </Link>
                            )}
                        </div>
                        <p className="text-gray-600 mb-6 border-b pb-6">{annonce.nombre_de_chambres} chambre(s)</p>

                        <h3 className="text-xl font-bold mb-4">À propos de ce logement</h3>
                        <p className="text-gray-700 leading-relaxed mb-10 whitespace-pre-line">{annonce.description}</p>

                        {annonce.latitude && annonce.longitude && (
                            <div className="mb-10">
                                <h3 className="text-xl font-bold mb-4">Localisation</h3>
                                <Map annonces={[annonce]} height="h-[350px]" />
                                <p className="text-gray-500 text-sm mt-2">{annonce.adresse}, {annonce.ville}</p>
                            </div>
                        )}

                        <div className="border-t pt-8">
                            <h3 className="text-xl font-bold mb-6">
                                Avis
                                {avg_rating !== null && (
                                    <span className="text-base font-normal text-gray-500 ml-2">
                                        ★ {avg_rating} · {avis_count} avis
                                    </span>
                                )}
                            </h3>

                            {criteria_averages && (
                                <div className="grid grid-cols-2 gap-4 mb-8 pb-8 border-b">
                                    <CriterionRow label="Propreté" value={criteria_averages.cleanliness} />
                                    <CriterionRow label="Communication" value={criteria_averages.communication} />
                                    <CriterionRow label="Emplacement" value={criteria_averages.location} />
                                    <CriterionRow label="Qualité-prix" value={criteria_averages.value} />
                                </div>
                            )}

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
                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                            <RatingPicker label="Propreté" value={review.rating_cleanliness} onChange={(v) => setReview({ ...review, rating_cleanliness: v })} />
                                            <RatingPicker label="Communication" value={review.rating_communication} onChange={(v) => setReview({ ...review, rating_communication: v })} />
                                            <RatingPicker label="Emplacement" value={review.rating_location} onChange={(v) => setReview({ ...review, rating_location: v })} />
                                            <RatingPicker label="Qualité-prix" value={review.rating_value} onChange={(v) => setReview({ ...review, rating_value: v })} />
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
                                        {breakdown && (
                                            <div className="text-sm text-gray-700 space-y-2 mb-4 pb-4 border-b">
                                                <div className="flex justify-between">
                                                    <span className="underline">{annonce.prix_par_nuit}$ × {breakdown.nights} nuit{breakdown.nights > 1 ? 's' : ''}</span>
                                                    <span>{breakdown.subtotal}$</span>
                                                </div>
                                                <div className="flex justify-between">
                                                    <span className="underline">Frais de ménage</span>
                                                    <span>{breakdown.cleaning}$</span>
                                                </div>
                                                <div className="flex justify-between">
                                                    <span className="underline">Frais de service</span>
                                                    <span>{breakdown.service}$</span>
                                                </div>
                                                <div className="flex justify-between font-bold text-base pt-2">
                                                    <span>Total</span>
                                                    <span>{breakdown.total}$</span>
                                                </div>
                                            </div>
                                        )}
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

function RatingPicker({ label, value, onChange }) {
    return (
        <div>
            <label className="block text-gray-700 font-semibold mb-1 text-sm">{label}</label>
            <div className="flex space-x-1">
                {[1, 2, 3, 4, 5].map((i) => (
                    <button
                        type="button"
                        key={i}
                        onClick={() => onChange(i)}
                        className={`text-xl ${i <= value ? 'text-yellow-400' : 'text-gray-300'} hover:text-yellow-300 transition`}
                    >
                        ★
                    </button>
                ))}
            </div>
        </div>
    );
}

function CriterionRow({ label, value }) {
    return (
        <div className="flex justify-between items-center text-sm">
            <span className="text-gray-700">{label}</span>
            <span className="flex items-center gap-2">
                <div className="w-24 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                    <div className="h-full bg-rose-500" style={{ width: `${(value / 5) * 100}%` }} />
                </div>
                <span className="font-semibold text-gray-700 w-8 text-right">{value}</span>
            </span>
        </div>
    );
}
