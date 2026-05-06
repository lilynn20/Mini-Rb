import { useEffect, useState } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import Logo from './Logo';

export default function Navbar() {
    const { user, logout } = useAuth();
    const [open, setOpen] = useState(false);
    const location = useLocation();

    useEffect(() => { setOpen(false); }, [location.pathname]);

    return (
        <nav className="bg-white shadow-sm border-b sticky top-0 z-50">
            <div className="py-4 px-4 sm:px-8 flex justify-between items-center">
                <div className="flex items-center space-x-8">
                    <Logo />
                    <div className="hidden md:flex items-center space-x-6 text-sm font-semibold text-gray-600">
                        <DropdownMenu
                            label="Villes populaires"
                            items={[
                                { label: 'Casablanca', value: 'Casablanca' },
                                { label: 'Marrakech', value: 'Marrakech' },
                                { label: 'Rabat', value: 'Rabat' },
                                { label: 'Fès', value: 'Fès' },
                                { label: 'Tanger', value: 'Tanger' },
                                { label: 'Agadir', value: 'Agadir' },
                            ]}
                        />
                        <DropdownMenu
                            label="Régions touristiques"
                            items={[
                                { label: 'Chefchaouen', value: 'Chefchaouen' },
                                { label: 'Essaouira', value: 'Essaouira' },
                                { label: 'Ouarzazate', value: 'Ouarzazate' },
                                { label: 'Merzouga', value: 'Merzouga' },
                                { label: 'Ifrane', value: 'Ifrane' },
                            ]}
                        />
                    </div>
                </div>

                <div className="hidden md:flex items-center space-x-4">
                    {user ? <UserLinks user={user} logout={logout} /> : <GuestLinks />}
                </div>

                <button
                    type="button"
                    onClick={() => setOpen((v) => !v)}
                    className="md:hidden p-2 rounded-lg hover:bg-gray-100 transition"
                    aria-label="Menu"
                >
                    {open ? (
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    ) : (
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    )}
                </button>
            </div>

            {open && (
                <div className="md:hidden border-t bg-white px-4 py-3 space-y-1">
                    {user ? <UserLinks user={user} logout={logout} mobile /> : <GuestLinks mobile />}
                </div>
            )}
        </nav>
    );
}

function UserLinks({ user, logout, mobile = false }) {
    const linkClass = mobile
        ? 'block py-2 text-gray-700 font-semibold hover:text-rose-500 transition'
        : 'text-gray-700 font-semibold hover:text-rose-500 transition';

    return (
        <>
            {user.is_admin && (
                <Link to="/admin" className={mobile ? 'block py-2 text-purple-700 font-semibold' : 'bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm font-semibold hover:bg-purple-200 transition'}>
                    Dashboard Admin
                </Link>
            )}
            <Link to="/favoris" className={linkClass}>Favoris</Link>
            <Link to="/messages" className={linkClass}>Messages</Link>
            <Link to="/mes-reservations" className={linkClass}>Mes Réservations</Link>
            <Link to="/annonces/create" className={linkClass}>
                {mobile ? 'Publier une annonce' : 'Mettre mon logement sur Mini-Rb'}
            </Link>
            {!mobile && <span className="text-gray-400">|</span>}
            <Link to="/profile" className={`${mobile ? 'flex items-center gap-2 py-2 text-gray-700 font-semibold' : 'flex items-center gap-2 text-gray-700 font-semibold hover:text-rose-500 transition'}`}>
                <span className="w-8 h-8 rounded-full overflow-hidden bg-rose-100 flex items-center justify-center text-rose-500 text-sm font-bold">
                    {user.avatar_url ? (
                        <img src={user.avatar_url} alt="" className="w-full h-full object-cover" />
                    ) : (
                        user.name.charAt(0).toUpperCase()
                    )}
                </span>
                {user.name}
            </Link>
            <button
                onClick={logout}
                className={mobile
                    ? 'block py-2 text-left text-gray-500 hover:text-rose-500 font-semibold'
                    : 'text-gray-500 hover:text-rose-500 transition font-semibold text-sm focus:outline-none'}
            >
                Déconnexion
            </button>
        </>
    );
}

function GuestLinks({ mobile = false }) {
    if (mobile) {
        return (
            <>
                <Link to="/login" className="block py-2 text-gray-700 font-semibold">Connexion</Link>
                <Link to="/register" className="block py-2 text-rose-500 font-semibold">Inscription</Link>
            </>
        );
    }
    return (
        <>
            <Link to="/login" className="text-gray-700 font-semibold hover:text-rose-500 transition">Connexion</Link>
            <Link to="/register" className="bg-rose-500 text-white px-4 py-2 rounded-full font-semibold hover:bg-rose-600 transition">Inscription</Link>
        </>
    );
}

function DropdownMenu({ label, items }) {
    return (
        <div className="group relative py-4">
            <button className="hover:text-rose-500 transition flex items-center">
                {label}
                <svg className="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div className="absolute top-full left-0 w-48 bg-white shadow-xl rounded-xl py-2 border opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                {items.map((it) => (
                    <Link
                        key={it.value}
                        to={`/?ville=${encodeURIComponent(it.value)}`}
                        className="block px-4 py-2 hover:bg-gray-50 hover:text-rose-500"
                    >
                        {it.label}
                    </Link>
                ))}
            </div>
        </div>
    );
}
