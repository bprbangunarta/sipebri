import type {
    DivIcon,
    LatLngExpression,
    Map as LeafletMap,
    Marker,
} from 'leaflet';
import { useEffect, useRef } from 'react';
import { cn } from '@/lib/utils';

export type MapPin = {
    key: string;
    label: string;
    latitude: number;
    longitude: number;
    /** `survey` pins are drawn in the primary colour, collateral pins in amber, a draft pin (being placed) in red. */
    tone: 'survey' | 'collateral' | 'draft';
};

const COLORS: Record<MapPin['tone'], string> = {
    survey: '#2f4aa8',
    collateral: '#d97706',
    draft: '#dc2626',
};

// Indonesia as a whole, shown when there is nothing to pin yet.
const DEFAULT_CENTER: LatLngExpression = [-2.5, 118];

const mapsUrl = (lat: number, lng: number) =>
    `https://www.google.com/maps?q=${lat},${lng}`;

type Props = {
    pins: MapPin[];
    height?: number;
    /** When set, a click on the map places a pin and reports it (the map then shows a crosshair). */
    onPick?: (latitude: number, longitude: number) => void;
    className?: string;
};

/**
 * A simple map (OpenStreetMap tiles through Leaflet, loaded only when a map is shown): one pin per place, fitted to view,
 * with a popup carrying the label and a link to open the place in Google Maps. No API key is needed.
 */
export function LocationMap({ pins, height = 260, onPick, className }: Props) {
    const element = useRef<HTMLDivElement>(null);
    const map = useRef<LeafletMap | null>(null);
    const markers = useRef<Marker[]>([]);
    const leaflet = useRef<typeof import('leaflet') | null>(null);
    const pick = useRef(onPick);
    pick.current = onPick;

    // Create the map once.
    useEffect(() => {
        let cancelled = false;

        void (async () => {
            const [L] = await Promise.all([
                import('leaflet'),
                import('leaflet/dist/leaflet.css'),
            ]);

            if (cancelled || !element.current) {
                return;
            }

            leaflet.current = L;
            const created = L.map(element.current, {
                center: DEFAULT_CENTER,
                zoom: 5,
                scrollWheelZoom: false,
            });
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution:
                    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            }).addTo(created);
            created.on('click', (e) =>
                pick.current?.(e.latlng.lat, e.latlng.lng),
            );
            map.current = created;
            draw();
        })();

        return () => {
            cancelled = true;
            map.current?.remove();
            map.current = null;
            markers.current = [];
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    const draw = () => {
        const L = leaflet.current;
        const current = map.current;

        if (!L || !current) {
            return;
        }

        markers.current.forEach((m) => m.remove());
        markers.current = pins.map((pin) => {
            const marker = L.marker([pin.latitude, pin.longitude], {
                icon: pinIcon(L, COLORS[pin.tone]),
                title: pin.label,
            }).addTo(current);

            if (pin.tone !== 'draft') {
                marker.bindPopup(popup(pin));
            }

            return marker;
        });

        if (pins.length === 1) {
            current.setView([pins[0].latitude, pins[0].longitude], 17);
        } else if (pins.length > 1) {
            current.fitBounds(
                L.latLngBounds(
                    pins.map(
                        (p) => [p.latitude, p.longitude] as [number, number],
                    ),
                ),
                { padding: [32, 32], maxZoom: 17 },
            );
        }

        current.invalidateSize();
    };

    useEffect(draw, [pins]);

    useEffect(() => {
        if (element.current) {
            element.current.style.cursor = onPick ? 'crosshair' : '';
        }
    }, [onPick]);

    return (
        <div
            ref={element}
            style={{ height }}
            className={cn(
                'z-0 w-full overflow-hidden rounded-md border border-line bg-canvas',
                className,
            )}
            role="region"
            aria-label="Peta"
        />
    );
}

function pinIcon(L: typeof import('leaflet'), color: string): DivIcon {
    return L.divIcon({
        className: '',
        iconSize: [26, 34],
        iconAnchor: [13, 34],
        popupAnchor: [0, -30],
        html: `<svg width="26" height="34" viewBox="0 0 26 34" xmlns="http://www.w3.org/2000/svg"><path d="M13 0C5.8 0 0 5.6 0 12.6 0 22 13 34 13 34s13-12 13-21.4C26 5.6 20.2 0 13 0z" fill="${color}" stroke="white" stroke-width="2"/><circle cx="13" cy="12.5" r="4.5" fill="white"/></svg>`,
    });
}

/** Popup content is built with DOM nodes so a label can never inject markup. */
function popup(pin: MapPin): HTMLElement {
    const box = document.createElement('div');
    const title = document.createElement('strong');
    title.textContent = pin.label;
    const coords = document.createElement('div');
    coords.textContent = `${pin.latitude.toFixed(6)}, ${pin.longitude.toFixed(6)}`;
    const link = document.createElement('a');
    link.href = mapsUrl(pin.latitude, pin.longitude);
    link.target = '_blank';
    link.rel = 'noopener noreferrer';
    link.textContent = 'Buka di Google Maps';
    box.append(title, coords, link);

    return box;
}
