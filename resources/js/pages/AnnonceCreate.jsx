import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import toast from 'react-hot-toast';
import api from '../api';
import Logo from '../components/Logo';
import { ErrorAlert } from '../components/Alert';

export default function AnnonceCreate() {
    const navigate = useNavigate();
    const [form, setForm] = useState({
        titre: '',
        description: '',
        ville: '',
        adresse: '',
        prix_par_nuit: '',
        nombre_de_chambres: 1,
    });
    const [images, setImages] = useState([]);
    const [errors, setErrors] = useState(null);
    const [submitting, setSubmitting] = useState(false);

    const handleChange = (e) => setForm({ ...form, [e.target.name]: e.target.value });

    const handleImageChange = (e) => {
        setImages(Array.from(e.target.files));
    };

    const removePreview = (idx) => {
        setImages(images.filter((_, i) => i !== idx));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setErrors(null);
        setSubmitting(true);

        const fd = new FormData();
        Object.entries(form).forEach(([k, v]) => fd.append(k, v));
        images.forEach((file) => fd.append('images[]', file));

        try {
            const { data: res } = await api.post('/annonces', fd);
            toast.success(res.message);
            navigate('/');
        } catch (err) {
            const data = err.response?.data;
            if (data?.errors) setErrors(data.errors);
            else toast.error(data?.message || 'Erreur.');
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <div className="bg-gray-50 min-h-screen">
            <nav className="bg-white shadow-sm py-4 px-8 flex justify-between items-center border-b sticky top-0 z-50">
                <Logo />
            </nav>

            <main className="max-w-2xl mx-auto px-8 py-10 bg-white shadow-md rounded-xl mt-10">
                <h1 className="text-2xl font-bold mb-6">Mettez votre logement sur Mini-Rb</h1>

                <ErrorAlert errors={errors} />

                <form onSubmit={handleSubmit} encType="multipart/form-data">
                    <div className="space-y-4">
                        <Field label="Titre de l'annonce" name="titre" value={form.titre} onChange={handleChange} placeholder="Ex: Riad au cœur de la médina" required />

                        <div>
                            <label className="block text-gray-700 font-semibold mb-1">Description</label>
                            <textarea
                                name="description" rows="4" value={form.description} onChange={handleChange}
                                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none"
                                placeholder="Décrivez votre logement..." required
                            />
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <Field label="Ville" name="ville" value={form.ville} onChange={handleChange} placeholder="Ex: Casablanca" required />
                            <Field label="Adresse" name="adresse" value={form.adresse} onChange={handleChange} placeholder="Ex: 12 Bd Mohammed V" required />
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <Field label="Prix par nuit (MAD)" name="prix_par_nuit" type="number" value={form.prix_par_nuit} onChange={handleChange} min="0" required />
                            <Field label="Nombre de chambres" name="nombre_de_chambres" type="number" value={form.nombre_de_chambres} onChange={handleChange} min="1" required />
                        </div>

                        <div>
                            <label className="block text-gray-700 font-semibold mb-1">Photos du logement (jusqu'à 10)</label>
                            <input
                                type="file" accept="image/*" multiple
                                onChange={handleImageChange}
                                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none"
                            />
                            <p className="text-xs text-gray-500 mt-1">JPEG, PNG, GIF — 2 Mo max par image.</p>

                            {images.length > 0 && (
                                <div className="grid grid-cols-3 sm:grid-cols-4 gap-3 mt-4">
                                    {images.map((file, idx) => (
                                        <div key={idx} className="relative group">
                                            <img
                                                src={URL.createObjectURL(file)}
                                                alt=""
                                                className="w-full h-24 object-cover rounded-lg"
                                            />
                                            <button
                                                type="button"
                                                onClick={() => removePreview(idx)}
                                                className="absolute top-1 right-1 bg-black/60 text-white w-6 h-6 rounded-full text-xs hover:bg-black/80"
                                            >
                                                ✕
                                            </button>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>

                    <button
                        type="submit"
                        disabled={submitting}
                        className="w-full bg-rose-500 text-white py-3 rounded-lg font-bold mt-8 hover:bg-rose-600 transition disabled:opacity-50"
                    >
                        {submitting ? 'Publication...' : 'Publier mon annonce'}
                    </button>
                </form>
            </main>
        </div>
    );
}

function Field({ label, ...props }) {
    return (
        <div>
            <label className="block text-gray-700 font-semibold mb-1">{label}</label>
            <input
                {...props}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none"
            />
        </div>
    );
}
