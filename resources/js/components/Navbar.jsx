import { Link } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import Logo from './Logo';

export default function Navbar() {
    const { user, logout } = useAuth();

    return (
        <nav className="bg-white shadow-sm py-4 px-8 flex justify-between items-center border-b sticky top-0 z-50">
            <div className="flex items-center space-x-8">
                <Logo />
                <div className="hidden md:flex items-center space-x-6 text-sm font-semibold text-gray-600">
                    <DropdownMenu
                        label="Pays populaires"
                        items={[
                            { label: 'France', value: 'France' },
                            { label: 'Maroc', value: 'Maroc' },
                            { label: 'Espagne', value: 'Espagne' },
                            { label: 'Italie', value: 'Italie' },
                            { label: 'États-Unis', value: 'USA' },
                        ]}
                    />
                    <DropdownMenu
                        label="Villes populaires"
                        items={[
                            { label: 'Paris', value: 'Paris' },
                            { label: 'Casablanca', value: 'Casablanca' },
                            { label: 'Marrakech', value: 'Marrakech' },
                            { label: 'Londres', value: 'Londres' },
                            { label: 'Barcelone', value: 'Barcelone' },
                        ]}
                    />
                </div>
            </div>

            <div className="flex items-center space-x-4">
                {user ? (
                    <>
                        {user.is_admin && (
                            <Link to="/admin" className="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm font-semibold hover:bg-purple-200 transition">
                                Dashboard Admin
                            </Link>
                        )}
                        <Link to="/favoris" className="text-gray-700 font-semibold hover:text-rose-500 transition">
                            Favoris
                        </Link>
                        <Link to="/mes-reservations" className="text-gray-700 font-semibold hover:text-rose-500 transition">
                            Mes Réservations
                        </Link>
                        <Link to="/annonces/create" className="text-gray-700 font-semibold hover:text-rose-500 transition">
                            Mettre mon logement sur Mini-Rb
                        </Link>
                        <span className="text-gray-400">|</span>
                        <Link to="/profile" className="text-gray-700 font-semibold hover:text-rose-500 transition">
                            {user.name}
                        </Link>
                        <button
                            onClick={logout}
                            className="text-gray-500 hover:text-rose-500 transition font-semibold text-sm focus:outline-none"
                        >
                            Déconnexion
                        </button>
                    </>
                ) : (
                    <>
                        <Link to="/login" className="text-gray-700 font-semibold hover:text-rose-500 transition">
                            Connexion
                        </Link>
                        <Link to="/register" className="bg-rose-500 text-white px-4 py-2 rounded-full font-semibold hover:bg-rose-600 transition">
                            Inscription
                        </Link>
                    </>
                )}
            </div>
        </nav>
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
