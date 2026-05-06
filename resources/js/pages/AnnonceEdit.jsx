import { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import toast from 'react-hot-toast';
import api from '../api';
import Logo from '../components/Logo';
import { ErrorAlert } from '../components/Alert';

export default function AnnonceEdit() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [form, setForm] = useState(null);
    const [existingImages, setExistingImages] = useState([]);
    const [deletedIds, setDeletedIds] = useState([]);
    const [newImages, setNewImages] = useState([]);
    const [errors, setErrors] = useState(null);
    const [submitting, setSubmitting] = useState(false);

    useEffect(() => {
        api.get(`/annonces/${id}`).then((res) => {
            const a = res.data.annonce;
            setForm({
                titre: a.titre,
                description: a.description,
                ville: a.ville,
                adresse: a.adresse,
                prix_par_nuit: a.prix_par_nuit,
                nombre_de_chambres: a.nombre_de_chambres,
            });
            setExistingImages(a.images || []);
        });
    }, [id]);

    const handleChange = (e) => setForm({ ...form, [e.target.name]: e.target.value });

    const handleNewImages = (e) => {
        setNewImages([...newImages, ...Array.from(e.target.files)]);
        e.target.value = '';
    };

    const removeNewImage = (idx) => {
        setNewImages(newImages.filter((_, i) => i !== idx));
    };

    const markExistingDeleted = (imgId) => {
        setDeletedIds([...deletedIds, imgId]);
        setExistingImages(existingImages.filter((img) => img.id !== imgId));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setErrors(null);
        setSubmitting(true);

        const fd = new FormData();
        Object.entries(form).forEach(([k, v]) => fd.append(k, v));
        fd.append('_method', 'PUT');
        newImages.forEach((file) => fd.append('images[]', file));
        deletedIds.forEach((delId) => fd.append('deleted_image_ids[]', delId));

        try {
            const { data: res } = await api.post(`/annonces/${id}`, fd);
            toast.success(res.message);
            navigate(`/annonces/${id}`);
        } catch (err) {
            const data = err.response?.data;
            if (data?.errors) setErrors(data.errors);
            else toast.error(data?.message || 'Erreur.');
        } finally {
            setSubmitting(false);
        }
    };

    if (!form) return <p className="p-10 text-gray-400 text-center">Chargement...</p>;

    return (
        <div className="bg-gray-50 min-h-screen">
            <nav className="bg-white shadow-sm py-4 px-8 flex justify-between items-center border-b sticky top-0 z-50">
                <Logo />
            </nav>

            <main className="max-w-2xl mx-auto px-8 py-10 bg-white shadow-md rounded-xl mt-10">
                <h1 className="text-2xl font-bold mb-6">Modifier votre annonce</h1>

                <ErrorAlert errors={errors} />

                <form onSubmit={handleSubmit} encType="multipart/form-data">
                    <div className="space-y-4">
                        <Field label="Titre de l'annonce" name="titre" value={form.titre} onChange={handleChange} required />

                        <div>
                            <label className="block text-gray-700 font-semibold mb-1">Description</label>
                            <textarea
                                name="description" rows="4" value={form.description} onChange={handleChange}
                                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none"
                                required
                            />
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <Field label="Ville" name="ville" value={form.ville} onChange={handleChange} required />
                            <Field label="Adresse" name="adresse" value={form.adresse} onChange={handleChange} required />
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <Field label="Prix par nuit (MAD)" name="prix_par_nuit" type="number" value={form.prix_par_nuit} onChange={handleChange} min="0" required />
                            <Field label="Nombre de chambres" name="nombre_de_chambres" type="number" value={form.nombre_de_chambres} onChange={handleChange} min="1" required />
                        </div>

                        <div>
                            <label className="block text-gray-700 font-semibold mb-1">Photos du logement</label>

                            {(existingImages.length > 0 || newImages.length > 0) && (
                                <div className="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-3">
                                    {existingImages.map((img) => (
                                        <div key={`existing-${img.id}`} className="relative group">
                                            <img src={img.url} alt="" className="w-full h-24 object-cover rounded-lg" />
                                            <button
                                                type="button"
                                                onClick={() => markExistingDeleted(img.id)}
                                                className="absolute top-1 right-1 bg-black/60 text-white w-6 h-6 rounded-full text-xs hover:bg-black/80"
                                            >
                                                ✕
                                            </button>
                                        </div>
                                    ))}
                                    {newImages.map((file, idx) => (
                                        <div key={`new-${idx}`} className="relative group">
                                            <img
                                                src={URL.createObjectURL(file)}
                                                alt=""
                                                className="w-full h-24 object-cover rounded-lg"
                                            />
                                            <span className="absolute bottom-1 left-1 bg-rose-500 text-white text-[10px] font-bold px-2 rounded">
                                                Nouveau
                                            </span>
                                            <button
                                                type="button"
                                                onClick={() => removeNewImage(idx)}
                                                className="absolute top-1 right-1 bg-black/60 text-white w-6 h-6 rounded-full text-xs hover:bg-black/80"
                                            >
                                                ✕
                                            </button>
                                        </div>
                                    ))}
                                </div>
                            )}

                            <input
                                type="file" accept="image/*" multiple
                                onChange={handleNewImages}
                                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none"
                            />
                            <p className="text-xs text-gray-500 mt-1">Cliquez sur ✕ pour supprimer une photo. Ajoutez-en de nouvelles avec le sélecteur.</p>
                        </div>
                    </div>

                    <div className="flex space-x-4 mt-8">
                        <Link
                            to={`/annonces/${id}`}
                            className="w-1/3 bg-gray-100 text-center py-3 rounded-lg font-bold hover:bg-gray-200 transition"
                        >
                            Annuler
                        </Link>
                        <button
                            type="submit"
                            disabled={submitting}
                            className="w-2/3 bg-rose-500 text-white py-3 rounded-lg font-bold hover:bg-rose-600 transition disabled:opacity-50"
                        >
                            {submitting ? 'Enregistrement...' : 'Enregistrer les modifications'}
                        </button>
                    </div>
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
