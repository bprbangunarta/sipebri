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
                    'Lokasi butuh koneksi aman. Buka aplikasi lewat https:// (atau localhost), bukan alamat http:// biasa.',
                ),
            );

            return;
        }

        if (!navigator.geolocation) {
            reject(new Error('Perangkat ini tidak bisa memberikan lokasi.'));

            return;
        }

        navigator.geolocation.getCurrentPosition(
            resolve,
            (error) =>
                reject(
                    new Error(
                        error.code === error.PERMISSION_DENIED
                            ? 'Akses lokasi diblokir. Izinkan untuk situs ini di pengaturan peramban lalu coba lagi.'
                            : error.code === error.TIMEOUT
                              ? 'Mengambil lokasi terlalu lama. Pindah ke area terbuka lalu coba lagi.'
                              : 'Lokasi Anda tidak tersedia. Nyalakan layanan lokasi (GPS) lalu coba lagi.',
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
                `Sinyal GPS lemah (sekitar ±${accuracy} m). Posisi tersimpan; tandai ulang di area terbuka untuk hasil lebih baik.`,
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
            e instanceof Error ? e.message : 'Tidak bisa membaca lokasi Anda.',
        );
        onFinish();
    }
}
