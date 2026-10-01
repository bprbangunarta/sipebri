/** The 5C and qualitative questions of the credit analysis worksheet, with the choices and the score of each choice. */
type Choice = { value: number; label: string };
type Aspect = { label: string; key: string; options: Choice[] };

const opt = (pairs: [number, string][]): Choice[] =>
    pairs.map(([value, label]) => ({ value, label }));

const GOOD = opt([
    [3, 'Baik'],
    [2, 'Cukup Baik'],
    [1, 'Kurang Baik'],
]);
const OWNED = opt([
    [3, 'Milik Sendiri'],
    [1, 'Orang Lain / Milik Sendiri dan Orang Lain (Warisan)'],
]);

const a = (label: string, key: string, options: Choice[]): Aspect => ({
    label,
    key,
    options,
});

export const FIVE_C: { key: string; title: string; fields: Aspect[] }[] = [
    {
        key: 'character',
        title: 'Character',
        fields: [
            a('Gaya Hidup', 'lifestyle', GOOD),
            a('Pengendalian Emosi', 'emotional_control', GOOD),
            a('Melakukan Tindakan Tercela', 'disreputable_acts', GOOD),
            a('Keharmonisan Keluarga', 'family_harmony', GOOD),
            a('Terbuka dan Konsisten', 'consistency', GOOD),
            a('Kepatuhan Kewajiban', 'compliance', GOOD),
            a('Hubungan Sosial', 'social_relations', GOOD),
        ],
    },
    {
        key: 'capacity',
        title: 'Capacity',
        fields: [
            a(
                'Kontinuitas Usaha',
                'continuity',
                opt([
                    [3, 'Terus Menerus'],
                    [2, 'Kadang-Kadang'],
                    [1, 'Tidak Tentu'],
                ]),
            ),
            a(
                'Pengalaman Usaha',
                'business_experience',
                opt([
                    [5, '> 5 Tahun'],
                    [4, '> 3 – 5 Tahun'],
                    [3, '1 – 3 Tahun'],
                    [2, '< 1 Tahun'],
                    [1, '0 Tahun'],
                ]),
            ),
            a(
                'Pertumbuhan Usaha',
                'business_growth',
                opt([
                    [3, 'Meningkat'],
                    [2, 'Tetap'],
                    [1, 'Turun'],
                ]),
            ),
            a(
                'Catatan Laporan Keuangan',
                'financial_records',
                opt([
                    [3, 'Mengumpulkan Bukti'],
                    [2, 'Transaksi Harian'],
                    [1, 'Tidak Ada'],
                ]),
            ),
            a(
                'Catatan Kredit Masa Lalu',
                'credit_history',
                opt([
                    [3, 'Lancar'],
                    [2, 'Menunggak > 2 Bulan'],
                    [1, 'Menunggak 2 Bulan'],
                ]),
            ),
            a(
                'Kondisi SLIK',
                'slik_condition',
                opt([
                    [3, 'Lancar'],
                    [2, 'Tidak Ada'],
                    [1, 'Tidak Baik'],
                ]),
            ),
            a(
                'Aset Diluar Usaha',
                'non_business_assets',
                opt([
                    [3, 'Liquid'],
                    [2, 'Cukup Liquid'],
                    [1, 'Tidak Liquid'],
                ]),
            ),
            a(
                'Aset Terkait Usaha',
                'business_assets',
                opt([
                    [3, 'Mengcover'],
                    [2, 'Cukup Mengcover'],
                    [1, 'Tidak Mengcover'],
                ]),
            ),
        ],
    },
    {
        key: 'capital',
        title: 'Capital',
        fields: [
            a(
                'Sumber Modal',
                'capital_source',
                opt([
                    [3, 'Modal Sendiri'],
                    [2, 'Kerjasama'],
                    [1, 'Pihak Lain'],
                ]),
            ),
        ],
    },
    {
        key: 'collateral',
        title: 'Collateral',
        fields: [
            a('Kepemilikan Agunan Utama', 'main_collateral_ownership', OWNED),
            a('Legalitas Agunan Utama', 'main_collateral_legality', OWNED),
            a(
                'Mudah Diuangkan',
                'liquidity',
                opt([
                    [3, 'Deposito, Tabungan, Emas'],
                    [2, 'BPKB, SHM'],
                    [1, 'Lainnya'],
                ]),
            ),
            a(
                'Kondisi Kendaraan',
                'vehicle_condition',
                opt([
                    [3, 'Original, Lengkap, Tidak Cacat'],
                    [2, 'Original, Tidak Lengkap'],
                    [1, 'Tidak Original, Tidak Lengkap, Cacat'],
                ]),
            ),
            a(
                'Pengikatan / Aspek Hukum',
                'legal_binding',
                opt([
                    [4, 'Emas / deposito diblokir & diikat sempurna'],
                    [3, 'SHM + SPPT (pengikatan tidak sempurna) / BPKB'],
                    [2, 'AJB / SPOP + SPPT'],
                    [1, 'Agunan lain yang tidak memenuhi syarat'],
                ]),
            ),
            a(
                'Kepemilikan Agunan Tambahan',
                'additional_collateral_ownership',
                OWNED,
            ),
            a(
                'Legalitas Agunan Tambahan',
                'additional_collateral_legality',
                OWNED,
            ),
            a(
                'Stabilitas Harga',
                'price_stability',
                opt([
                    [3, 'SHM'],
                    [2, 'Deposito, Tabungan, Emas'],
                    [1, 'BPKB'],
                    [0, 'Lainnya'],
                ]),
            ),
            a(
                'Lokasi SHM',
                'shm_location',
                opt([
                    [3, 'Strategis dan atau Produktif'],
                    [2, 'Strategis dan Produktif (atau sebaliknya)'],
                    [1, 'Kurang Strategis dan Kurang Produktif'],
                ]),
            ),
        ],
    },
    {
        key: 'condition',
        title: 'Condition',
        fields: [
            a(
                'Kondisi Alam',
                'natural_conditions',
                opt([
                    [5, 'Resiko Sangat Rendah'],
                    [4, 'Resiko Rendah'],
                    [3, 'Resiko Sedang'],
                    [2, 'Resiko Tinggi'],
                    [1, 'Resiko Sangat Tinggi'],
                ]),
            ),
            a(
                'Persaingan Usaha',
                'competition',
                opt([
                    [3, 'Tidak Ketat'],
                    [2, 'Kurang Ketat'],
                    [1, 'Ketat'],
                ]),
            ),
            a(
                'Regulasi Pemerintah',
                'regulations',
                opt([
                    [4, 'Sangat Mendukung'],
                    [3, 'Mendukung'],
                    [2, 'Kurang Mendukung'],
                    [1, 'Tidak Mendukung'],
                ]),
            ),
        ],
    },
];

export const QUALITATIVE_SCORES: Aspect[] = [
    a(
        'SLIK (SID Bank Indonesia)',
        'slik_check',
        opt([
            [4, 'Lancar'],
            [3, 'Kurang Lancar'],
            [2, 'Diragukan'],
            [1, 'Macet'],
        ]),
    ),
    a(
        'Berurusan dgn Pihak Berwajib',
        'police_record',
        opt([
            [2, 'Pernah'],
            [1, 'Tidak Pernah'],
        ]),
    ),
];

/** Free text per card: [label, key]. */
export const QUALITATIVE_CHARACTER_TEXTS: [string, string][] = [
    ['Pemohon Waktu di Rumah', 'applicant_at_home'],
    ['Pendamping Waktu di Rumah', 'companion_at_home'],
    ['Info Masyarakat', 'community_info'],
];

export const QUALITATIVE_BUSINESS: [string, string][] = [
    ['Sumber Bahan Baku', 'raw_materials'],
    ['Proses Pengolahan', 'processing'],
    ['Market Wilayah', 'market_area'],
    ['Sistem Pembayaran', 'payment_system'],
    ['Faktor Pendukung Usaha', 'business_supporters'],
    ['Faktor Pengurang Usaha', 'business_detractors'],
];

export const QUALITATIVE_SWOT: [string, string][] = [
    ['Kekuatan (Strength)', 'strength'],
    ['Kelemahan (Weakness)', 'weakness'],
    ['Peluang (Opportunities)', 'opportunity'],
    ['Ancaman (Threats)', 'threat'],
];

export const QUALITATIVE_CHOICE_FIELDS: [string, string][] = [
    ['Hubungan dengan Tetangga', 'neighbor_relations'],
    ['Pengalaman Menjadi TKI', 'migrant_worker_experience'],
    ['Keterangan Pengalaman', 'experience_by'],
];
