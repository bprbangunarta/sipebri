import { router } from '@inertiajs/react';
import { toast } from 'sonner';

/** A fix worse than this (metres) still gets saved, with a warning to mark again in the open. */
export const WEAK_GPS_METERS = 100;

/** Reads the phone's position; the browser only allows this on HTTPS or localhost. */
export function currentPosition(): Promise<GeolocationPosition> {
    return new Promise((resolve, reject) => {
        if (!window.isSecureContext) {
            reject(
                new Error(
                    'Location needs a secure connection. Open the app through https:// (or localhost), not a plain http:// address.',
                ),
            );

            return;
        }

        if (!navigator.geolocation) {
            reject(new Error('This device cannot provide a location.'));

            return;
        }

        navigator.geolocation.getCurrentPosition(
            resolve,
            (error) =>
                reject(
                    new Error(
                        error.code === error.PERMISSION_DENIED
                            ? 'Location access is blocked. Allow it for this site in the browser settings and try again.'
                            : error.code === error.TIMEOUT
                              ? 'Getting your location took too long. Move to an open area and try again.'
                              : 'Your location is unavailable. Turn on location services (GPS) and try again.',
                    ),
                ),
            { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 },
        );
    });
}

/**
 * On site: store the phone's current GPS position as the position of a place of a survey (the survey location, or one
 * collateral). A weak fix is saved with a warning; a failure to read the position is explained and nothing is saved.
 */
export async function markPosition(
    loanId: number,
    place: { target: 'survey' | 'collateral'; collateral_id: number | null },
    onFinish: () => void,
): Promise<void> {
    try {
        const position = await currentPosition();
        const accuracy = Math.round(position.coords.accuracy);

        if (accuracy > WEAK_GPS_METERS) {
            toast.warning(
                `Weak GPS signal (about ±${accuracy} m). The position is saved; mark it again in the open for a better fix.`,
            );
        }

        router.post(
            `/surveys/${loanId}/locations`,
            {
                ...place,
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                source: 'gps',
                accuracy,
            },
            { preserveScroll: true, onFinish },
        );
    } catch (e) {
        toast.error(
            e instanceof Error ? e.message : 'Unable to read your location.',
        );
        onFinish();
    }
}
