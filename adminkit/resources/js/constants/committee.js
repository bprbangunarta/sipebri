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

/** Persentase gaya Indonesia: 12.75 → "12,75%" (koma desimal). */
export const persen = (value, decimals = 2) =>
    value === null || value === undefined || value === ''
        ? '—'
        : `${new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: decimals }).format(Number(value))}%`;

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

/**
 * Plafon maksimum dari kemampuan angsuran: (keuangan per bulan × ambang RC)
 * dikapitalisasi memakai metode bunga & jangka waktu. Cermin
 * ApprovalController::maxPlafon() — dipakai agar RC bergerak langsung di layar.
 */
export const maxPlafon = ({ capacity = 0, rcThreshold = 0, rate = 0, tenor = 0, method = '' }) => {
    const installment = Number(capacity) * (Number(rcThreshold) / 100);
    const months = Number(tenor);
    if (installment <= 0 || months <= 0) return 0;

    const i = Number(rate) / 100 / 12;
    if (i <= 0) return Math.round(installment * months);

    const effective = /ANUITAS|EFEKTIF/.test(String(method).toUpperCase());
    return Math.round(
        effective ? (installment * (1 - (1 + i) ** -months)) / i : installment / (1 / months + i),
    );
};

/** RC sistem lama: porsi usulan plafon terhadap max plafon (%). */
export const rcRatio = (amount, max) =>
    Number(max) > 0 ? Math.round((Number(amount) / Number(max)) * 10000) / 100 : 0;
