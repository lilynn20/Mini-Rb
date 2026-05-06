import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import toast from 'react-hot-toast';
import api from '../api';
import { useAuth } from '../contexts/AuthContext';
import Logo from '../components/Logo';
import Footer from '../components/Footer';

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

const ROLE_BADGE = {
    admin: 'bg-purple-100 text-purple-700',
    hote: 'bg-blue-100 text-blue-700',
};

const formatDate = (d) => new Date(d).toLocaleDateString('fr-FR');

export default function Admin() {
    const { user, logout } = useAuth();
    const [data, setData] = useState(null);

    const load = () => api.get('/admin').then((res) => setData(res.data));

    useEffect(() => { load(); }, []);

    const handleDeleteUser = async (id) => {
        if (!confirm('Supprimer cet utilisateur ?')) return;
        try {
            const { data: res } = await api.delete(`/admin/users/${id}`);
            toast.success(res.message);
            load();
        } catch (err) {
            toast.error(err.response?.data?.message || 'Erreur.');
        }
    };

    const handleDeleteAnnonce = async (id) => {
        if (!confirm('Supprimer cette annonce ?')) return;
        try {
            const { data: res } = await api.delete(`/admin/annonces/${id}`);
            toast.success(res.message);
            load();
        } catch (err) {
            toast.error(err.response?.data?.message || 'Erreur.');
        }
    };

    if (!data) return <p className="p-10 text-gray-400 text-center">Chargement...</p>;

    return (
        <div className="bg-gray-50 min-h-screen flex flex-col">
            <nav className="bg-white shadow-sm py-4 px-8 flex justify-between items-center border-b">
                <Logo />
                <div className="flex items-center space-x-4">
                    <span className="bg-rose-100 text-rose-600 px-3 py-1 rounded-full text-sm font-semibold">Admin</span>
                    <Link to="/profile" className="text-gray-700 font-semibold hover:text-rose-500 transition">
                        {user?.name}
                    </Link>
                    <button onClick={logout} className="text-gray-500 hover:text-rose-500 font-semibold text-sm">
                        Déconnexion
                    </button>
                </div>
            </nav>

            <main className="max-w-7xl mx-auto px-8 py-10 flex-1 w-full">
                <div className="grid grid-cols-3 gap-6 mb-10">
                    <StatCard value={data.users.length} label="Utilisateurs" />
                    <StatCard value={data.annonces.length} label="Annonces" />
                    <StatCard value={data.reservations.length} label="Réservations" />
                </div>

                <Section title="👥 Utilisateurs">
                    <Table headers={['Nom', 'Email', 'Rôle', 'Inscrit le', 'Action']}>
                        {data.users.map((u) => (
                            <tr key={u.id} className="hover:bg-gray-50">
                                <td className="px-6 py-4 font-semibold">{u.name}</td>
                                <td className="px-6 py-4 text-gray-500">{u.email}</td>
                                <td className="px-6 py-4">
                                    <span className={`px-2 py-1 rounded-full text-xs font-semibold ${ROLE_BADGE[u.role] || 'bg-green-100 text-green-700'}`}>
                                        {u.role.charAt(0).toUpperCase() + u.role.slice(1)}
                                    </span>
                                </td>
                                <td className="px-6 py-4 text-gray-400">{formatDate(u.created_at)}</td>
                                <td className="px-6 py-4">
                                    {u.id !== user?.id ? (
                                        <button onClick={() => handleDeleteUser(u.id)} className="text-red-500 hover:text-red-700 font-semibold">
                                            Supprimer
                                        </button>
                                    ) : (
                                        <span className="text-gray-300 text-xs">Vous</span>
                                    )}
                                </td>
                            </tr>
                        ))}
                    </Table>
                </Section>

                <Section title="🏠 Annonces">
                    <Table headers={['Titre', 'Ville', 'Prix/nuit', 'Propriétaire', 'Action']}>
                        {data.annonces.map((a) => (
                            <tr key={a.id} className="hover:bg-gray-50">
                                <td className="px-6 py-4 font-semibold">
                                    <Link to={`/annonces/${a.id}`} className="hover:text-rose-500">{a.titre}</Link>
                                </td>
                                <td className="px-6 py-4 text-gray-500">{a.ville}</td>
                                <td className="px-6 py-4">{a.prix_par_nuit}$</td>
                                <td className="px-6 py-4 text-gray-500">{a.host_name}</td>
                                <td className="px-6 py-4">
                                    <button onClick={() => handleDeleteAnnonce(a.id)} className="text-red-500 hover:text-red-700 font-semibold">
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </Table>
                </Section>

                <Section title="📅 Réservations">
                    <Table headers={['Annonce', 'Voyageur', 'Dates', 'Total', 'Statut']}>
                        {data.reservations.map((r) => (
                            <tr key={r.id} className="hover:bg-gray-50">
                                <td className="px-6 py-4 font-semibold">{r.annonce_titre}</td>
                                <td className="px-6 py-4 text-gray-500">{r.traveler_name}</td>
                                <td className="px-6 py-4 text-gray-500">
                                    {formatDate(r.start_date)} → {formatDate(r.end_date)}
                                </td>
                                <td className="px-6 py-4">{r.total_price}$</td>
                                <td className="px-6 py-4">
                                    <span className={`px-2 py-1 rounded-full text-xs font-semibold ${STATUS_COLORS[r.status] || ''}`}>
                                        {STATUS_LABELS[r.status] || r.status}
                                    </span>
                                </td>
                            </tr>
                        ))}
                    </Table>
                </Section>
            </main>

            <Footer />
        </div>
    );
}

function StatCard({ value, label }) {
    return (
        <div className="bg-white rounded-2xl p-6 shadow-sm border text-center">
            <p className="text-4xl font-bold text-rose-500">{value}</p>
            <p className="text-gray-500 mt-1">{label}</p>
        </div>
    );
}

function Section({ title, children }) {
    return (
        <div className="bg-white rounded-2xl shadow-sm border mb-10">
            <div className="p-6 border-b">
                <h2 className="text-xl font-bold">{title}</h2>
            </div>
            <div className="overflow-x-auto">{children}</div>
        </div>
    );
}

function Table({ headers, children }) {
    return (
        <table className="w-full text-sm">
            <thead className="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    {headers.map((h) => (
                        <th key={h} className="px-6 py-3 text-left">{h}</th>
                    ))}
                </tr>
            </thead>
            <tbody className="divide-y">{children}</tbody>
        </table>
    );
}
