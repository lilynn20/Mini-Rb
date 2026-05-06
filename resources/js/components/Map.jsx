import { MapContainer, Marker, Popup, TileLayer } from 'react-leaflet';
import L from 'leaflet';
import iconUrl from 'leaflet/dist/images/marker-icon.png';
import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';
import shadowUrl from 'leaflet/dist/images/marker-shadow.png';

L.Icon.Default.mergeOptions({ iconUrl, iconRetinaUrl, shadowUrl });

export default function Map({ annonces, height = 'h-[400px]', center, zoom }) {
    const withCoords = (annonces || []).filter((a) => a.latitude && a.longitude);

    if (withCoords.length === 0 && !center) {
        return (
            <div className={`${height} w-full bg-gray-100 rounded-2xl flex items-center justify-center text-gray-400`}>
                Aucune localisation disponible
            </div>
        );
    }

    const fallback = withCoords[0] || { latitude: 31.7917, longitude: -7.0926 };
    const mapCenter = center || [fallback.latitude, fallback.longitude];
    const mapZoom = zoom ?? (withCoords.length === 1 ? 13 : 5);

    return (
        <div className={`${height} w-full rounded-2xl overflow-hidden`}>
            <MapContainer center={mapCenter} zoom={mapZoom} style={{ height: '100%', width: '100%' }}>
                <TileLayer
                    attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                />
                {withCoords.map((a) => (
                    <Marker key={a.id} position={[a.latitude, a.longitude]}>
                        <Popup>
                            <div className="text-sm">
                                <p className="font-bold">{a.titre}</p>
                                <p className="text-gray-500">{a.ville}</p>
                                <p className="font-semibold mt-1">{a.prix_par_nuit} MAD / nuit</p>
                                <a href={`/annonces/${a.id}`} className="text-rose-500 underline">
                                    Voir l'annonce
                                </a>
                            </div>
                        </Popup>
                    </Marker>
                ))}
            </MapContainer>
        </div>
    );
}
