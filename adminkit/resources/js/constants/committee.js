/** Leksikon modul Komite Kredit (cermin App\Models\CommitteePath::MECHANISMS). */
export const MECHANISM_OPTIONS = [
    { value: 'plafon', label: 'Kewenangan Plafon' },
    { value: 'hierarki', label: 'Hierarki Komite' },
];

/** Keputusan yang dapat diizinkan pada sebuah jenjang. */
export const TIER_DECISIONS = [
    { key: 'can_escalate', label: 'Naik Komite' },
    { key: 'can_approve', label: 'Disetujui' },
    { key: 'can_cancel', label: 'Dibatalkan' },
    { key: 'can_reject', label: 'Ditolak' },
];

export const rupiah = (value) =>
    value === null || value === undefined || value === ''
        ? '—'
        : `Rp ${new Intl.NumberFormat('id-ID').format(value)}`;

/** Kondisi/kategori disimpan HURUF BESAR, ditampilkan Capitalize (RELOAN → Reloan). */
export const conditionLabel = (value) =>
    String(value ?? '')
        .toLowerCase()
        .replace(/\b\p{L}/gu, (c) => c.toUpperCase());

export const digitsOnly = (value) => String(value ?? '').replace(/\D/g, '');
