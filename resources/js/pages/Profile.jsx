import { useEffect, useRef, useState } from 'react';
import { Link } from 'react-router-dom';
import api from '../api';
import { useAuth } from '../contexts/AuthContext';
import Layout from '../components/Layout';
import { ErrorAlert, SuccessAlert } from '../components/Alert';

export default function Profile() {
    const { refreshUser } = useAuth();
    const [data, setData] = useState(null);
    const [form, setForm] = useState({
        name: '', email: '', phone: '', bio: '',
        password: '', password_confirmation: '',
    });
    const [avatarFile, setAvatarFile] = useState(null);
    const [removeAvatar, setRemoveAvatar] = useState(false);
    const [success, setSuccess] = useState(null);
    const [errors, setErrors] = useState(null);
    const [submitting, setSubmitting] = useState(false);
    const fileInputRef = useRef(null);

    const load = () =>
        api.get('/profile').then((res) => {
            setData(res.data);
            setForm({
                name: res.data.user.name,
                email: res.data.user.email,
                phone: res.data.user.phone || '',
                bio: res.data.user.bio || '',
                password: '',
                password_confirmation: '',
            });
            setAvatarFile(null);
            setRemoveAvatar(false);
        });

    useEffect(() => { load(); }, []);

    const handleChange = (e) => setForm({ ...form, [e.target.name]: e.target.value });

    const handleSubmit = async (e) => {
        e.preventDefault();
        setErrors(null);
        setSuccess(null);
        setSubmitting(true);

        const fd = new FormData();
        fd.append('_method', 'PUT');
        Object.entries(form).forEach(([k, v]) => fd.append(k, v ?? ''));
        if (avatarFile) fd.append('avatar', avatarFile);
        if (removeAvatar) fd.append('remove_avatar', '1');

        try {
            const { data: res } = await api.post('/profile', fd);
            setSuccess(res.message);
            await refreshUser();
            await load();
            if (fileInputRef.current) fileInputRef.current.value = '';
        } catch (err) {
            setErrors(err.response?.data?.errors);
        } finally {
            setSubmitting(false);
        }
    };

    if (!data) return <Layout><p className="p-10 text-gray-400">Chargement...</p></Layout>;

    const { user, stats, annonces } = data;
    const previewAvatar = avatarFile ? URL.createObjectURL(avatarFile) : (removeAvatar ? null : user.avatar_url);

    return (
        <Layout>
            <main className="max-w-3xl mx-auto px-8 py-10">
                <SuccessAlert message={success} />
                <ErrorAlert errors={errors} />

                <h1 className="text-3xl font-bold mb-10">Mon Profil</h1>

                <div className="bg-white rounded-2xl shadow-sm border p-8 mb-8">
                    <div className="flex items-center gap-6 mb-8">
                        <div className="relative">
                            <div className="w-24 h-24 rounded-full overflow-hidden bg-rose-100 flex items-center justify-center text-rose-500 text-4xl font-bold">
                                {previewAvatar ? (
                                    <img src={previewAvatar} alt="" className="w-full h-full object-cover" />
                                ) : (
                                    user.name.charAt(0).toUpperCase()
                                )}
                            </div>
                            <button
                                type="button"
                                onClick={() => fileInputRef.current?.click()}
                                className="absolute -bottom-1 -right-1 bg-rose-500 hover:bg-rose-600 text-white rounded-full w-8 h-8 flex items-center justify-center shadow"
                                aria-label="Changer la photo"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                            <input
                                ref={fileInputRef}
                                type="file"
                                accept="image/*"
                                onChange={(e) => {
                                    const f = e.target.files?.[0];
                                    if (f) {
                                        setAvatarFile(f);
                                        setRemoveAvatar(false);
                                    }
                                }}
                                className="hidden"
                            />
                        </div>
                        <div className="flex-1">
                            <h2 className="text-2xl font-bold">{user.name}</h2>
                            <p className="text-gray-500">{user.email}</p>
                            <div className="flex items-center gap-2 mt-1 flex-wrap">
                                {user.email_verified ? (
                                    <span className="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">✓ Email vérifié</span>
                                ) : (
                                    <span className="bg-yellow-100 text-yellow-700 text-xs font-semibold px-2 py-1 rounded-full">⚠ Email non vérifié</span>
                                )}
                                <span className="bg-gray-100 text-gray-600 text-xs font-semibold px-2 py-1 rounded-full">
                                    {user.role.charAt(0).toUpperCase() + user.role.slice(1)}
                                </span>
                            </div>
                            {(avatarFile || (user.avatar_url && !removeAvatar)) && (
                                <button
                                    type="button"
                                    onClick={() => {
                                        setAvatarFile(null);
                                        if (user.avatar_url) setRemoveAvatar(true);
                                        if (fileInputRef.current) fileInputRef.current.value = '';
                                    }}
                                    className="text-xs text-gray-400 hover:text-rose-500 mt-2 underline"
                                >
                                    Supprimer la photo
                                </button>
                            )}
                        </div>
                    </div>

                    <div className="grid grid-cols-3 gap-4 mb-8 border-t pt-6">
                        <Stat value={stats.annonces} label="Annonces publiées" />
                        <Stat value={stats.reservations} label="Réservations effectuées" />
                        <Stat value={new Date(user.created_at).getFullYear()} label="Membre depuis" />
                    </div>

                    <form onSubmit={handleSubmit} encType="multipart/form-data">
                        <h3 className="text-lg font-bold mb-4">Modifier mes informations</h3>

                        <div className="space-y-4">
                            <Field label="Nom" name="name" value={form.name} onChange={handleChange} required />
                            <div>
                                <Field label="Email" name="email" type="email" value={form.email} onChange={handleChange} required />
                                <p className="text-xs text-gray-400 mt-1">Si vous changez votre email, vous devrez le vérifier à nouveau.</p>
                            </div>
                            <Field label="Téléphone" name="phone" value={form.phone} onChange={handleChange} placeholder="+212 6XX XXX XXX" />
                            <div>
                                <label className="block text-gray-700 font-semibold mb-1">Bio</label>
                                <textarea
                                    name="bio" rows="3" value={form.bio} onChange={handleChange}
                                    className="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500"
                                    placeholder="Parlez-nous de vous..."
                                    maxLength={1000}
                                />
                                <p className="text-xs text-gray-400 mt-1">{form.bio.length} / 1000 caractères</p>
                            </div>

                            <div className="border-t pt-4">
                                <Field label="Nouveau mot de passe" name="password" type="password" value={form.password} onChange={handleChange} placeholder="(laisser vide pour ne pas changer)" />
                            </div>
                            <Field label="Confirmer le mot de passe" name="password_confirmation" type="password" value={form.password_confirmation} onChange={handleChange} />
                        </div>

                        <button
                            type="submit"
                            disabled={submitting}
                            className="mt-6 bg-rose-500 text-white px-8 py-3 rounded-lg font-bold hover:bg-rose-600 transition disabled:opacity-50"
                        >
                            {submitting ? 'Enregistrement...' : 'Enregistrer les modifications'}
                        </button>
                    </form>
                </div>

                {annonces.length > 0 && (
                    <div className="bg-white rounded-2xl shadow-sm border p-8">
                        <h3 className="text-lg font-bold mb-6">Mes annonces</h3>
                        <div className="space-y-4">
                            {annonces.map((a) => (
                                <div key={a.id} className="flex items-center justify-between border rounded-xl p-4">
                                    <div className="flex items-center gap-4">
                                        <div className="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0">
                                            {a.image_url ? (
                                                <img src={a.image_url} className="w-full h-full object-cover" alt={a.titre} />
                                            ) : (
                                                <div className="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400 text-xs">No img</div>
                                            )}
                                        </div>
                                        <div>
                                            <Link to={`/annonces/${a.id}`} className="font-semibold hover:text-rose-500">
                                                {a.titre}
                                            </Link>
                                            <p className="text-gray-500 text-sm">{a.ville} · {a.prix_par_nuit}$/nuit</p>
                                        </div>
                                    </div>
                                    <div className="flex gap-2">
                                        <Link to={`/annonces/${a.id}/edit`} className="text-sm bg-gray-100 px-3 py-1 rounded-lg hover:bg-gray-200 font-semibold">
                                            Modifier
                                        </Link>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </main>
        </Layout>
    );
}

function Field({ label, ...props }) {
    return (
        <div>
            <label className="block text-gray-700 font-semibold mb-1">{label}</label>
            <input
                {...props}
                className="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500"
            />
        </div>
    );
}

function Stat({ value, label }) {
    return (
        <div className="text-center">
            <p className="text-2xl font-bold text-rose-500">{value}</p>
            <p className="text-gray-500 text-sm">{label}</p>
        </div>
    );
}
