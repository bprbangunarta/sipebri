/** Definisi kolom Analisa 5C & Analisa Kualitatif (mengikuti form sistem lama). */

const s = (label, key, options) => ({ label, key, options });
const opt = (pairs) => pairs.map(([value, label]) => ({ value, label }));

const BAIK = opt([
    [3, 'Baik'],
    [2, 'Cukup Baik'],
    [1, 'Kurang Baik'],
]);

const MILIK = opt([
    [3, 'Milik Sendiri'],
    [1, 'Orang Lain / Milik Sendiri dan Orang Lain (Warisan)'],
]);

export const FIVE_C = [
    {
        key: 'character',
        title: 'Character',
        fields: [
            s('Gaya Hidup', 'gaya_hidup', BAIK),
            s('Pengendalian Emosi', 'pengendalian_emosi', BAIK),
            s('Melakukan Tindakan Tercela', 'perbuatan_tercela', BAIK),
            s('Keharmonisan Keluarga', 'harmonis', BAIK),
            s('Terbuka dan Konsisten', 'konsisten', BAIK),
            s('Kepatuhan Kewajiban', 'kepatuhan', BAIK),
            s('Hubungan Sosial', 'hubungan_sosial', BAIK),
        ],
    },
    {
        key: 'capacity',
        title: 'Capacity',
        fields: [
            s('Kontinuitas Usaha', 'kontinuitas', opt([[3, 'Terus Menerus'], [2, 'Kadang-Kadang'], [1, 'Tidak Tentu']])),
            s(
                'Pengalaman Usaha',
                'pengalaman_usaha',
                opt([[5, '> 5 Tahun'], [4, '> 3 – 5 Tahun'], [3, '1 – 3 Tahun'], [2, '< 1 Tahun'], [1, '0 Tahun']]),
            ),
            s('Pertumbuhan Usaha', 'pertumbuhan_usaha', opt([[3, 'Meningkat'], [2, 'Tetap'], [1, 'Turun']])),
            s(
                'Catatan Laporan Keuangan',
                'laporan_keuangan',
                opt([[3, 'Mengumpulkan Bukti'], [2, 'Transaksi Harian'], [1, 'Tidak Ada']]),
            ),
            s(
                'Catatan Kredit Masa Lalu',
                'catatan_kredit',
                opt([[3, 'Lancar'], [2, 'Menunggak > 2 Bulan'], [1, 'Menunggak 2 Bulan']]),
            ),
            s('Kondisi SLIK', 'kondisi_slik', opt([[3, 'Lancar'], [2, 'Tidak Ada'], [1, 'Tidak Baik']])),
            s(
                'Aset Diluar Usaha',
                'aset_diluar_usaha',
                opt([[3, 'Liquid'], [2, 'Cukup Liquid'], [1, 'Tidak Liquid']]),
            ),
            s(
                'Aset Terkait Usaha',
                'aset_terkait_usaha',
                opt([[3, 'Mengcover'], [2, 'Cukup Mengcover'], [1, 'Tidak Mengcover']]),
            ),
        ],
    },
    {
        key: 'capital',
        title: 'Capital',
        fields: [
            s('Sumber Modal', 'sumber_modal', opt([[3, 'Modal Sendiri'], [2, 'Kerjasama'], [1, 'Pihak Lain']])),
        ],
    },
    {
        key: 'collateral',
        title: 'Collateral',
        fields: [
            s('Kepemilikan Agunan Utama', 'agunan_utama', MILIK),
            s('Legalitas Agunan Utama', 'legalitas_agunan', MILIK),
            s(
                'Mudah Diuangkan',
                'mudah_diuangkan',
                opt([[3, 'Deposito, Tabungan, Emas'], [2, 'BPKB, SHM'], [1, 'Lainnya']]),
            ),
            s(
                'Kondisi Kendaraan',
                'kondisi_kendaraan',
                opt([
                    [3, 'Original, Lengkap, Tidak Cacat'],
                    [2, 'Original, Tidak Lengkap'],
                    [1, 'Tidak Original, Tidak Lengkap, Cacat'],
                ]),
            ),
            s(
                'Pengikatan / Aspek Hukum',
                'aspek_hukum',
                opt([
                    [4, 'Emas / deposito diblokir & diikat sempurna'],
                    [3, 'SHM + SPPT (pengikatan tidak sempurna) / BPKB'],
                    [2, 'AJB / SPOP + SPPT'],
                    [1, 'Agunan lain yang tidak memenuhi syarat'],
                ]),
            ),
            s('Kepemilikan Agunan Tambahan', 'agunan_tambahan', MILIK),
            s('Legalitas Agunan Tambahan', 'legalitas_agunan_tambahan', MILIK),
            s(
                'Stabilitas Harga',
                'stabilitas_harga',
                opt([[3, 'SHM'], [2, 'Deposito, Tabungan, Emas'], [1, 'BPKB'], [0, 'Lainnya']]),
            ),
            s(
                'Lokasi SHM',
                'lokasi_shm',
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
            s(
                'Kondisi Alam',
                'kondisi_alam',
                opt([
                    [5, 'Resiko Sangat Rendah'],
                    [4, 'Resiko Rendah'],
                    [3, 'Resiko Sedang'],
                    [2, 'Resiko Tinggi'],
                    [1, 'Resiko Sangat Tinggi'],
                ]),
            ),
            s(
                'Persaingan Usaha',
                'persaingan_usaha',
                opt([[3, 'Tidak Ketat'], [2, 'Kurang Ketat'], [1, 'Ketat']]),
            ),
            s(
                'Regulasi Pemerintah',
                'regulasi_pemerintah',
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

export const FIVE_C_KEYS = FIVE_C.flatMap((group) => group.fields.map((f) => f.key));

export const QUALITATIVE_SCORES = [
    s(
        'SLIK (SID Bank Indonesia)',
        'bi_checking',
        opt([[4, 'Lancar'], [3, 'Kurang Lancar'], [2, 'Diragukan'], [1, 'Macet']]),
    ),
    s('Berurusan dgn Pihak Berwajib', 'pihak_berwajib', opt([[2, 'Pernah'], [1, 'Tidak Pernah']])),
];

/** Kolom teks per kartu: [label, key, panjang/jenis]. */
export const QUALITATIVE_CHARACTER_TEXTS = [
    ['Pemohon Waktu di Rumah', 'pemohon_ada'],
    ['Pendamping Waktu di Rumah', 'pendamping_ada'],
    ['Info Masyarakat', 'info_masyarakat'],
];

export const QUALITATIVE_BUSINESS = [
    ['Sumber Bahan Baku', 'bahan_baku'],
    ['Proses Pengolahan', 'proses_olah'],
    ['Market Wilayah', 'target_market'],
    ['Sistem Pembayaran', 'pembayaran'],
    ['Faktor Pendukung Usaha', 'pendukung_usaha'],
    ['Faktor Pengurang Usaha', 'pengurang_usaha'],
];

export const QUALITATIVE_SWOT = [
    ['Kekuatan (Strength)', 'kekuatan'],
    ['Kelemahan (Weakness)', 'kelemahan'],
    ['Peluang (Opportunities)', 'peluang'],
    ['Ancaman (Threats)', 'ancaman'],
];

export const QUALITATIVE_CHOICE_FIELDS = [
    ['Hubungan dengan Tetangga', 'hubungan_tetangga'],
    ['Pengalaman Menjadi TKI', 'pengalaman_tki'],
    ['Keterangan Pengalaman', 'ket_pengalaman'],
];

export const QUALITATIVE_KEYS = [
    ...QUALITATIVE_SCORES.map((f) => f.key),
    ...QUALITATIVE_CHOICE_FIELDS.map(([, key]) => key),
    ...QUALITATIVE_CHARACTER_TEXTS.map(([, key]) => key),
    ...QUALITATIVE_BUSINESS.map(([, key]) => key),
    ...QUALITATIVE_SWOT.map(([, key]) => key),
    ...[1, 2, 3].flatMap((i) => [`kewajiban${i}`, `ket_kewajiban${i}`, `status${i}`]),
    'trade_checking',
    'catatan',
    'trade_checking_usaha',
];
