import { Navigate, Route, Routes } from 'react-router-dom';
import { useAuth } from './contexts/AuthContext';
import Home from './pages/Home';
import Login from './pages/Login';
import Register from './pages/Register';
import VerifyEmail from './pages/VerifyEmail';
import Profile from './pages/Profile';
import AnnonceShow from './pages/AnnonceShow';
import AnnonceCreate from './pages/AnnonceCreate';
import AnnonceEdit from './pages/AnnonceEdit';
import Reservations from './pages/Reservations';
import Favorites from './pages/Favorites';
import Messages from './pages/Messages';
import Admin from './pages/Admin';

function Protected({ children, requireVerified = true, requireAdmin = false }) {
    const { user, loading } = useAuth();
    if (loading) return <Loader />;
    if (!user) return <Navigate to="/login" replace />;
    if (requireVerified && !user.email_verified) return <Navigate to="/email/verify" replace />;
    if (requireAdmin && !user.is_admin) return <Navigate to="/" replace />;
    return children;
}

function Loader() {
    return (
        <div className="min-h-screen flex items-center justify-center text-gray-400">
            Chargement...
        </div>
    );
}

export default function App() {
    const { loading } = useAuth();
    if (loading) return <Loader />;

    return (
        <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/login" element={<Login />} />
            <Route path="/register" element={<Register />} />
            <Route path="/email/verify" element={<VerifyEmail />} />
            <Route path="/annonces/:id" element={<AnnonceShow />} />

            <Route path="/profile" element={<Protected><Profile /></Protected>} />
            <Route path="/annonces/create" element={<Protected><AnnonceCreate /></Protected>} />
            <Route path="/annonces/:id/edit" element={<Protected><AnnonceEdit /></Protected>} />
            <Route path="/mes-reservations" element={<Protected><Reservations /></Protected>} />
            <Route path="/favoris" element={<Protected><Favorites /></Protected>} />
            <Route path="/messages" element={<Protected><Messages /></Protected>} />
            <Route path="/admin" element={<Protected requireAdmin><Admin /></Protected>} />

            <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
    );
}
