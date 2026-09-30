import { ExternalLink, MapPin } from 'lucide-react';
import { useMemo } from 'react';
import { LocationMap } from '@/components/location-map';
import type { MapPin as Pin } from '@/components/location-map';
import { Badge } from '@/components/ui/misc';

export type LocationPlace = {
    key: string;
    type: 'survey' | 'collateral';
    label: string;
    detail: string;
    location: {
        latitude: number;
        longitude: number;
        source: string | null;
        located_at: string | null;
        located_by: string | null;
        maps_url: string;
    } | null;
};

const SOURCES: Record<string, string> = {
    gps: 'GPS',
    paste: 'Pasted',
    map: 'Map pin',
    photo: 'Photo GPS',
};

/** Read-only view of where a file was surveyed and where its collaterals are: a map plus one line per place. */
export function LocationsPanel({ places }: { places: LocationPlace[] }) {
    const pins: Pin[] = useMemo(
        () =>
            places.flatMap((p) =>
                p.location
                    ? [
                          {
                              key: p.key,
                              label: p.label,
                              latitude: p.location.latitude,
                              longitude: p.location.longitude,
                              tone: p.type,
                          },
                      ]
                    : [],
            ),
        [places],
    );

    return (
        <div className="flex flex-col gap-3 p-3">
            <LocationMap pins={pins} />
            <ul className="divide-y divide-line rounded-md border border-line text-sm">
                {places.map((p) => (
                    <li
                        key={p.key}
                        className="flex flex-wrap items-center justify-between gap-2 px-3 py-2"
                    >
                        <div className="min-w-0">
                            <p className="font-medium">{p.label}</p>
                            {p.detail && (
                                <p className="text-xs text-muted">{p.detail}</p>
                            )}
                        </div>
                        {p.location ? (
                            <p className="flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs">
                                <MapPin className="size-3.5 text-primary" />
                                <span className="font-mono tabular-nums">
                                    {p.location.latitude.toFixed(6)},{' '}
                                    {p.location.longitude.toFixed(6)}
                                </span>
                                {p.location.source && (
                                    <Badge>
                                        {SOURCES[p.location.source] ??
                                            p.location.source}
                                    </Badge>
                                )}
                                <a
                                    href={p.location.maps_url}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="inline-flex items-center gap-1 text-primary hover:underline"
                                >
                                    Google Maps{' '}
                                    <ExternalLink className="size-3" />
                                </a>
                            </p>
                        ) : (
                            <span className="text-xs text-muted">
                                No position recorded
                            </span>
                        )}
                    </li>
                ))}
            </ul>
        </div>
    );
}
