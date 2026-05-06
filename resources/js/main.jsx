import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import { Toaster } from 'react-hot-toast';
import 'leaflet/dist/leaflet.css';
import { AuthProvider } from './contexts/AuthContext';
import App from './App';

createRoot(document.getElementById('app')).render(
    <StrictMode>
        <BrowserRouter>
            <AuthProvider>
                <App />
                <Toaster
                    position="top-right"
                    toastOptions={{
                        success: { style: { background: '#10b981', color: '#fff' } },
                        error: { style: { background: '#ef4444', color: '#fff' } },
                    }}
                />
            </AuthProvider>
        </BrowserRouter>
    </StrictMode>
);
