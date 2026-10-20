<?php

namespace App\Support\Appraisal;

/**
 * "Form Penilaian Agunan Tanah dan Bangunan" of the management, sections A to R. The facts that do not change between
 * appraisals (certificate, permit, address) are kept on the collateral; this form records what is observed and decided at the date.
 */
final class LandBuildingTemplate implements AppraisalTemplate
{
    public const KEY = 'land_building';

    /** Land and building types: certificate with and without a mortgage right, customary papers, place of business. */
    public const TYPES = ['05', '06', '07', '08'];

    public const STRESS = ['s10' => 10.0, 's15' => 15.0, 's20' => 20.0];

    public function key(): string
    {
        return self::KEY;
    }

    public function version(): int
    {
        return 1;
    }

    public function variant(): ?string
    {
        return null;
    }

    public function missingFacts(): ?string
    {
        return null;
    }

    public function hardStops(): array
    {
        return ['legal.original_documents', 'legal.identity', 'legal.no_dispute', 'legal.no_blockade', 'legal.can_be_encumbered', 'legal.access_basis'];
    }

    public function sections(): array
    {
        return [
            ['key' => 'identity', 'label' => 'A. Identitas penilaian', 'blocks' => [['layout' => 'fields', 'items' => CommonSections::identity(['full' => 'Penuh', 'external' => 'Eksternal', 'desktop' => 'Desktop'])]]],
            ['key' => 'object', 'label' => 'B. Objek dan lokasi', 'blocks' => [['layout' => 'fields', 'items' => Fields::many('text', [
                'current_use' => 'Penggunaan saat ini', 'best_use' => 'Penggunaan terbaik dan tertinggi', 'occupant' => 'Penghuni/penguasaan fisik', 'distance' => 'Jarak dari kantor Bank',
            ], 'object.')]]],
            ['key' => 'land_documents', 'label' => 'C. Dokumen hak atas tanah', 'blocks' => [['layout' => 'fields', 'items' => [
                Fields::item('land_documents.measured_area', 'Luas menurut pengukuran/observasi', 'integer', ['unit' => 'm²']),
                Fields::item('land_documents.certificate_check', 'Status pengecekan sertipikat', 'text'),
                Fields::item('land_documents.encumbrance', 'Hak tanggungan/blokir/sita/catatan', 'text'),
                Fields::item('land_documents.tax', 'PBB/NOP dan status pembayaran', 'text'),
                Fields::item('land_documents.name_match', 'Kesesuaian nama dengan identitas', 'text'),
            ]]]],
            ['key' => 'building_documents', 'label' => 'D. Dokumen dan legalitas bangunan', 'blocks' => [['layout' => 'fields', 'items' => [
                Fields::item('building_documents.actual_area', 'Luas bangunan aktual', 'integer', ['unit' => 'm²']),
                Fields::item('building_documents.permit_match', 'Kesesuaian bangunan dengan izin', 'text'),
                Fields::item('building_documents.plans', 'Gambar/site plan/bestek', 'text'),
                Fields::item('building_documents.taxes', 'Pajak/retribusi terkait', 'text'),
                Fields::item('building_documents.insurance', 'Asuransi kebakaran/gempa', 'text'),
                Fields::item('building_documents.violations', 'Pelanggaran/teguran pemerintah', 'text'),
            ]]]],
            ['key' => 'legal', 'label' => 'E. Pemeriksaan legal dan penguasaan', 'blocks' => [['layout' => 'checks', 'items' => self::legalChecks()]]],
            ['key' => 'land_physical', 'label' => 'F. Data fisik tanah', 'blocks' => [['layout' => 'fields', 'items' => self::landPhysical()]]],
            ['key' => 'building_physical', 'label' => 'G. Data fisik bangunan', 'blocks' => [self::buildingPhysical()]],
            ['key' => 'zoning', 'label' => 'H. Tata ruang, lingkungan, marketability', 'blocks' => [['layout' => 'fields', 'items' => Fields::many('text', [
                'plan' => 'Peruntukan/rencana tata ruang', 'zoning_match' => 'Kesesuaian penggunaan dengan zoning', 'setbacks' => 'Garis sempadan jalan/sungai/pantai',
                'protected_area' => 'Kawasan lindung/resapan/cagar budaya', 'projects' => 'Rencana jalan/proyek pemerintah', 'density' => 'Kepadatan dan tren pembangunan',
                'facilities' => 'Fasilitas umum dan transportasi', 'security' => 'Keamanan dan penerimaan lingkungan', 'demand' => 'Permintaan pasar untuk objek sejenis',
                'normal_marketing' => 'Perkiraan waktu pemasaran normal', 'forced_marketing' => 'Perkiraan waktu penjualan paksa', 'buyers' => 'Segmen pembeli potensial',
                'limits' => 'Faktor yang membatasi marketability',
            ], 'zoning.')]]],
            ['key' => 'comparables', 'label' => 'I. Data pembanding pasar', 'blocks' => [self::comparables()]],
            ['key' => 'adjustments', 'label' => 'J. Penyesuaian pembanding', 'blocks' => self::adjustments()],
            ['key' => 'cost', 'label' => 'K. Pendekatan biaya bangunan', 'blocks' => self::cost()],
            ['key' => 'reconciliation', 'label' => 'L. Rekonsiliasi nilai', 'blocks' => self::reconciliation()],
            ['key' => 'ltv', 'label' => 'M. LTV dan kecukupan agunan', 'blocks' => CommonSections::loanToValue(['s10' => 'Nilai turun 10%', 's15' => 'Nilai turun 15%', 's20' => 'Nilai turun 20%'])],
            ['key' => 'risks', 'label' => 'N. Penilaian risiko agunan', 'blocks' => [['layout' => 'ratings', 'items' => self::risks()]]],
            ['key' => 'binding', 'label' => 'O. Pengikatan, asuransi, dan syarat', 'blocks' => [['layout' => 'fields', 'items' => Fields::many('text', [
                'kind' => 'Jenis pengikatan: APHT/SKMHT/lainnya', 'ht_value' => 'Nilai Hak Tanggungan', 'ht_rank' => 'Peringkat Hak Tanggungan', 'notary' => 'Notaris/PPAT',
                'roya' => 'Pengecekan dan roya existing', 'consent' => 'Persetujuan pasangan/pemilik', 'fire_insurance' => 'Asuransi kebakaran',
                'other_insurance' => 'Asuransi gempa/banjir bila relevan', 'bankers_clause' => 'Banker’s clause', 'document_fixes' => 'Perbaikan dokumen/izin',
                'pre_disbursement' => 'Syarat pra-pencairan', 'monitoring' => 'Syarat monitoring berkala',
            ], 'binding.')]]],
            ['key' => 'photos', 'label' => 'P. Dokumentasi inspeksi', 'blocks' => [['layout' => 'photos', 'items' => Fields::many('photo', [
                'front' => 'Tampak depan dan papan alamat', 'access' => 'Akses/jalan menuju objek', 'sides' => 'Tampak kiri, kanan, dan belakang', 'borders' => 'Batas/patok setiap sisi',
                'interior' => 'Interior setiap lantai/ruang utama', 'damage' => 'Kerusakan/kondisi khusus', 'meters' => 'Meter listrik/air dan utilitas', 'surroundings' => 'Lingkungan sekitar',
                'map' => 'Koordinat/screenshot peta', 'documents' => 'Dokumen asli saat pemeriksaan',
            ], 'photos.')]]],
            ['key' => 'conclusion', 'label' => 'Q. Kesimpulan penilai', 'blocks' => CommonSections::conclusion()],
            ['key' => 'independence', 'label' => 'R. Pernyataan independensi', 'blocks' => [['layout' => 'checks', 'items' => Fields::many('yesno', [
                'no_interest' => 'Tidak memiliki kepentingan atas objek/pemilik/debitur', 'no_benefit' => 'Tidak menerima manfaat terkait hasil nilai',
                'scope' => 'Inspeksi dilakukan sesuai ruang lingkup', 'comparables_verified' => 'Data pembanding telah diverifikasi',
                'assumptions' => 'Asumsi dan keterbatasan telah diungkap', 'not_by_ceiling' => 'Nilai tidak ditentukan oleh kebutuhan plafon',
            ], 'independence.')]]],
        ];
    }

    public function compute(array $values, AppraisalContext $context): Computation
    {
        return LandBuildingCalculator::run($values, $context);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function legalChecks(): array
    {
        return Fields::many('yesno', [
            'original_documents' => 'Dokumen asli telah dilihat', 'identity' => 'Identitas pemegang hak sesuai', 'boundaries' => 'Objek dan batas sesuai dokumen',
            'no_dispute' => 'Tidak terdapat sengketa', 'no_blockade' => 'Tidak terdapat blokir/sita', 'not_pledged' => 'Tidak dijaminkan kepada pihak lain tanpa persetujuan',
            'spouse_consent' => 'Persetujuan pasangan tersedia', 'owners_consent' => 'Persetujuan seluruh pemilik tersedia', 'third_party' => 'Agunan pihak ketiga dianalisa',
            'can_be_encumbered' => 'Dapat dibebani Hak Tanggungan', 'access_basis' => 'Akses objek mempunyai dasar hukum', 'not_public_land' => 'Tidak berada pada tanah negara/fasum/fasos',
            'no_acquisition_plan' => 'Tidak terkena rencana pengadaan tanah yang diketahui', 'no_obstructing_tenant' => 'Tidak ada sewa/penghuni yang menghambat eksekusi',
        ], 'legal.');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function landPhysical(): array
    {
        return [
            Fields::item('land_physical.area', 'Luas tanah aktual', 'integer', ['unit' => 'm²', 'hint' => 'Dipakai untuk menghitung nilai tanah; bila kosong dipakai luas menurut sertipikat.']),
            Fields::choice('land_physical.shape', 'Bentuk', ['regular' => 'Beraturan', 'irregular' => 'Tidak beraturan']),
            Fields::item('land_physical.dimensions', 'Dimensi muka × panjang'),
            Fields::item('land_physical.road_width', 'Lebar jalan depan objek'),
            Fields::item('land_physical.road_surface', 'Jenis/perkerasan jalan'),
            Fields::choice('land_physical.position', 'Posisi', ['middle' => 'Tengah', 'corner' => 'Sudut', 'cul_de_sac' => 'Tusuk sate', 'other' => 'Lainnya']),
            Fields::item('land_physical.elevation', 'Elevasi terhadap jalan'),
            Fields::item('land_physical.topography', 'Topografi/kemiringan'),
            Fields::item('land_physical.drainage', 'Drainase/risiko genangan'),
            Fields::item('land_physical.border_north', 'Batas Utara'),
            Fields::item('land_physical.border_east', 'Batas Timur'),
            Fields::item('land_physical.border_south', 'Batas Selatan'),
            Fields::item('land_physical.border_west', 'Batas Barat'),
            Fields::item('land_physical.fence', 'Pagar/patok batas'),
            Fields::item('land_physical.utilities', 'Utilitas: listrik/air/telepon/internet'),
            Fields::item('land_physical.surroundings', 'Kondisi lingkungan sekitar'),
            Fields::item('land_physical.hazards', 'Potensi banjir/longsor/abrasi/gempa'),
            Fields::item('land_physical.contamination', 'Kontaminasi/limbah/gangguan'),
            Fields::item('land_physical.vehicle_access', 'Kemudahan akses kendaraan'),
            Fields::item('land_physical.marketability', 'Marketability lokasi'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function buildingPhysical(): array
    {
        $rows = [];

        foreach ([
            'function' => 'Jenis/fungsi bangunan', 'floors' => ['Jumlah lantai', 'integer'], 'area' => ['Luas bangunan', 'integer'], 'built' => 'Tahun dibangun/renovasi',
            'structure' => 'Struktur/fondasi', 'walls' => 'Dinding', 'floor' => 'Lantai', 'roof' => 'Atap/rangka', 'ceiling' => 'Plafon', 'doors' => 'Pintu/jendela',
            'electricity' => 'Instalasi listrik', 'water' => 'Air/sanitasi', 'upkeep' => 'Kondisi pemeliharaan', 'materials' => 'Kualitas bahan', 'life' => 'Sisa umur ekonomis',
            'damage' => 'Kerusakan/biaya perbaikan', 'fit' => 'Kesesuaian fungsi dan izin',
        ] as $key => $definition) {
            $rows[] = is_array($definition) ? ['key' => $key, 'label' => $definition[0], 'type' => $definition[1]] : ['key' => $key, 'label' => $definition];
        }

        return ['layout' => 'grid', 'rows' => $rows, 'columns' => [['key' => 'main', 'label' => 'Bangunan utama'], ['key' => 'other', 'label' => 'Bangunan lain/keterangan']]];
    }

    /**
     * @return array<string, mixed>
     */
    private static function comparables(): array
    {
        return ['layout' => 'grid', 'columns' => CommonSections::comparableColumns(), 'rows' => [
            ['key' => 'address', 'label' => 'Alamat/lokasi'], ['key' => 'source', 'label' => 'Sumber/tanggal data'],
            ['key' => 'status', 'label' => 'Status: penawaran/transaksi'], ['key' => 'land_area', 'label' => 'Luas tanah (m²)', 'type' => 'integer'],
            ['key' => 'building_area', 'label' => 'Luas bangunan (m²)', 'type' => 'integer'], ['key' => 'total_price', 'label' => 'Harga total', 'type' => 'money'],
            ['key' => 'land_per_m2', 'label' => 'Harga tanah/m²', 'type' => 'money'], ['key' => 'building_per_m2', 'label' => 'Harga bangunan/m²', 'type' => 'money'],
            ['key' => 'road', 'label' => 'Lebar jalan/akses'], ['key' => 'right', 'label' => 'Jenis hak'], ['key' => 'building_condition', 'label' => 'Kondisi bangunan'],
            ['key' => 'distance', 'label' => 'Jarak dari objek'], ['key' => 'contact', 'label' => 'Telepon/bukti verifikasi'],
        ]];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function adjustments(): array
    {
        $factors = [
            'time' => 'Waktu transaksi', 'location' => 'Lokasi/akses', 'shape' => 'Luas/bentuk/elevasi', 'right' => 'Jenis dan jangka hak', 'zoning' => 'Peruntukan/zoning',
            'building' => 'Kondisi/usia bangunan', 'position' => 'Posisi/lingkungan', 'offer' => 'Status penawaran',
        ];
        $rows = [];

        foreach ($factors as $key => $label) {
            $rows[] = ['key' => $key, 'label' => $label.' (%)', 'type' => 'adjustment'];
        }

        $rows[] = ['key' => 'total', 'label' => 'Total penyesuaian (%)', 'computed' => true, 'format' => 'percent'];
        $rows[] = ['key' => 'indicative', 'label' => 'Nilai indikasi tanah/m² setelah penyesuaian', 'computed' => true, 'format' => 'money'];

        return [
            ['layout' => 'grid', 'columns' => CommonSections::comparableColumns(), 'rows' => $rows],
            ['layout' => 'fields', 'items' => [
                Fields::item('adjustments.range_min', 'Rentang nilai tanah terendah per m²', 'computed', ['format' => 'money']),
                Fields::item('adjustments.range_max', 'Rentang nilai tanah tertinggi per m²', 'computed', ['format' => 'money']),
                Fields::item('adjustments.suggested', 'Rata-rata nilai indikasi per m²', 'computed', ['format' => 'money']),
                Fields::item('adjustments.selected', 'Nilai terpilih per m²', 'money', ['hint' => 'Dipakai untuk menghitung nilai tanah dengan pendekatan pasar.']),
            ]],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function cost(): array
    {
        $rows = [];

        foreach (['main' => 'Bangunan utama', 'additional' => 'Bangunan tambahan', 'canopy' => 'Kanopi/garasi', 'fence' => 'Pagar/gerbang', 'facilities' => 'Sarana pelengkap', 'other' => 'Lainnya'] as $key => $label) {
            $rows[] = ['key' => $key, 'label' => $label];
        }

        return [
            ['layout' => 'grid', 'rows' => $rows, 'columns' => [
                ['key' => 'area', 'label' => 'Luas (m²)', 'type' => 'integer'], ['key' => 'unit_cost', 'label' => 'Biaya baru/m²', 'type' => 'money'],
                ['key' => 'new_cost', 'label' => 'Biaya baru', 'computed' => true, 'format' => 'money'],
                ['key' => 'depreciation', 'label' => 'Depresiasi (%)', 'type' => 'percent', 'hint' => 'Bila kosong dipakai total depresiasi di bawah.'],
                ['key' => 'present', 'label' => 'Nilai sekarang', 'computed' => true, 'format' => 'money'],
            ]],
            ['layout' => 'fields', 'items' => [
                Fields::item('cost.physical', 'Depresiasi fisik (%): usia, kondisi, kerusakan, pemeliharaan', 'percent'),
                Fields::item('cost.functional', 'Depresiasi fungsional (%): desain, efisiensi, spesifikasi, keusangan', 'percent'),
                Fields::item('cost.external', 'Depresiasi eksternal/ekonomis (%): lingkungan, akses, pasar, regulasi', 'percent'),
                Fields::item('cost.total', 'Total depresiasi (%)', 'computed', ['format' => 'percent', 'hint' => 'Jumlah ketiganya, paling besar 100%. Hindari penghitungan ganda.']),
            ]],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function reconciliation(): array
    {
        return [
            ['layout' => 'grid', 'rows' => [
                ['key' => 'land', 'label' => 'Tanah'], ['key' => 'building', 'label' => 'Bangunan'], ['key' => 'facilities', 'label' => 'Sarana pelengkap'],
                ['key' => 'deductions', 'label' => 'Pengurang biaya perbaikan/legal'], ['key' => 'total', 'label' => 'Nilai pasar indikatif', 'computed' => true],
            ], 'columns' => [
                ['key' => 'market', 'label' => 'Pendekatan pasar', 'computed' => true, 'format' => 'money'],
                ['key' => 'cost', 'label' => 'Pendekatan biaya', 'computed' => true, 'format' => 'money'],
                ['key' => 'selected', 'label' => 'Nilai terpilih', 'type' => 'money'],
            ]],
            ['layout' => 'fields', 'items' => [
                Fields::item('reconciliation.market_value', 'Nilai pasar', 'computed', ['format' => 'money', 'hint' => 'Jumlah nilai terpilih dikurangi pengurang.']),
                Fields::item('h:haircut_percent', 'Haircut nilai likuidasi (%)', 'percent'),
                Fields::item('reconciliation.haircut_basis', 'Dasar haircut', 'text'),
                Fields::item('reconciliation.liquidation_value', 'Nilai likuidasi', 'computed', ['format' => 'money']),
                Fields::item('h:forced_sale_value', 'Nilai jual cepat/forced sale (jika digunakan)', 'money'),
                Fields::item('h:rounded_value', 'Pembulatan nilai', 'money'),
            ]],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function risks(): array
    {
        return Fields::many('risk', [
            'legal' => 'Legalitas hak dan pengikatan', 'right_term' => 'Sisa jangka waktu hak', 'access' => 'Akses dan lokasi', 'shape' => 'Bentuk/topografi/elevasi',
            'disaster' => 'Banjir/bencana/lingkungan', 'permit_zoning' => 'Kesesuaian izin dan tata ruang', 'building' => 'Kondisi dan usia bangunan', 'occupancy' => 'Penguasaan/penghuni/sewa',
            'marketability' => 'Marketability dan waktu jual', 'volatility' => 'Volatilitas harga', 'insurance' => 'Kelengkapan asuransi', 'third_party' => 'Agunan pihak ketiga',
        ], 'risks.');
    }
}
