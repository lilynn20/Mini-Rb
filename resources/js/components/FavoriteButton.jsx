import { useState } from 'react';
import api from '../api';
import { useAuth } from '../contexts/AuthContext';

export default function FavoriteButton({ annonceId, initial = false, onChange, className = '', size = 24 }) {
    const { user } = useAuth();
    const [favorited, setFavorited] = useState(initial);
    const [busy, setBusy] = useState(false);

    if (!user) return null;

    const toggle = async (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (busy) return;
        setBusy(true);
        try {
            if (favorited) {
                await api.delete(`/favorites/${annonceId}`);
                setFavorited(false);
                onChange?.(false);
            } else {
                await api.post(`/favorites/${annonceId}`);
                setFavorited(true);
                onChange?.(true);
            }
        } catch (err) {
            // ignore
        } finally {
            setBusy(false);
        }
    };

    return (
        <button
            type="button"
            onClick={toggle}
            disabled={busy}
            aria-label={favorited ? 'Retirer des favoris' : 'Ajouter aux favoris'}
            className={`p-2 rounded-full hover:scale-110 transition disabled:opacity-50 ${className}`}
        >
            <svg
                width={size}
                height={size}
                viewBox="0 0 24 24"
                fill={favorited ? '#f43f5e' : 'rgba(0,0,0,0.4)'}
                stroke="white"
                strokeWidth="2"
            >
                <path d="M12 21s-7-4.35-7-10a4.5 4.5 0 0 1 8-2.83A4.5 4.5 0 0 1 19 11c0 5.65-7 10-7 10z" />
            </svg>
        </button>
    );
}
