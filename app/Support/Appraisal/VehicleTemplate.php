<?php

namespace App\Support\Appraisal;

use App\Models\Collateral;

/**
 * "Form Penilaian Agunan Kendaraan Roda 2 dan Roda 4" of the management, sections A to R. The facts that do not change between
 * appraisals (what the BPKB says, the chassis and engine numbers) are kept on the collateral; this form records what is observed and
 * decided at the date. A motorcycle shows section G and a car shows section F, never both.
 */
final class VehicleTemplate implements AppraisalTemplate
{
    public const KEY = 'vehicle';

    /** Vehicle or vessel with a fiduciary binding, with a notarial one, and with another. */
    public const TYPES = ['10', '11', '12'];

    public const STRESS = ['s10' => 10.0, 's20' => 20.0, 's30' => 30.0];

    public const FACTORS = ['time', 'location', 'age', 'variant', 'odometer', 'condition', 'accident', 'documents', 'accessories', 'offer'];

    public const REPAIR_ROWS = ['r1', 'r2', 'r3', 'r4', 'r5', 'r6', 'r7'];

    public const HAIRCUT_ROWS = ['h_age', 'h_odometer', 'h_technical', 'h_history', 'h_market', 'h_documents', 'h_costs'];

    public function __construct(private readonly ?string $variant = null) {}

    public static function forCollateral(Collateral $collateral): self
    {
        return new self($collateral->vehicle_kind);
    }

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
        return $this->variant;
    }

    public function missingFacts(): ?string
    {
        return array_key_exists((string) $this->variant, Collateral::VEHICLE_KINDS) ? null : 'Isi jenis kendaraan (roda 2 atau roda 4) di data jaminan sebelum menilainya.';
    }

    public function hardStops(): array
    {
        return ['legal.original_bpkb', 'legal.original_stnk', 'legal.names', 'legal.chassis', 'legal.engine', 'legal.not_altered', 'legal.no_dispute', 'legal.fiduciary'];
    }

    public function sections(): array
    {
        $sections = [...self::opening(), ...self::inspection(), ...self::valuation(), ...self::closing()];

        return array_values(array_filter($sections, fn (array $section): bool => match ($section['key']) {
            'car' => $this->variant !== 'wheel2',
            'motorcycle' => $this->variant !== 'wheel4',
            default => true,
        }));
    }

    public function compute(array $values, AppraisalContext $context): Computation
    {
        return VehicleCalculator::run($values, $context);
    }

    /**
     * @return list<array{key: string, label: string, blocks: list<array<string, mixed>>}>
     */
    private static function opening(): array
    {
        $identity = [...CommonSections::identity(null), Fields::item('identity.place', 'Lokasi pemeriksaan', 'text')];

        return [
            ['key' => 'identity', 'label' => 'A. Identitas penilaian', 'blocks' => [['layout' => 'fields', 'items' => $identity]]],
            ['key' => 'vehicle', 'label' => 'B. Identitas kendaraan', 'blocks' => [['layout' => 'fields', 'items' => [
                Fields::item('vehicle.stnk_valid_until', 'Masa berlaku STNK', 'date'),
                Fields::item('vehicle.odometer', 'Odometer', 'integer', ['unit' => 'km']),
                Fields::item('vehicle.keys', 'Jumlah kunci', 'integer'),
                Fields::choice('vehicle.usage', 'Penggunaan', ['personal' => 'Pribadi', 'business' => 'Usaha', 'rental' => 'Sewa']),
                Fields::item('vehicle.parking', 'Lokasi parkir'),
                Fields::item('vehicle.accessories', 'Aksesori/modifikasi'),
            ]]]],
            ['key' => 'documents', 'label' => 'C1. Dokumen dan kepemilikan', 'blocks' => [
                ['layout' => 'fields', 'items' => Fields::many('text', [
                    'bpkb' => 'Nomor BPKB dan tanggal penerbitan', 'bpkb_name' => 'Nama pada BPKB', 'bpkb_address' => 'Alamat pada BPKB', 'stnk' => 'Nomor STNK dan masa berlaku',
                    'stnk_name' => 'Nama pada STNK', 'invoice' => 'Faktur pembelian', 'receipt' => 'Kwitansi/perjanjian jual beli', 'id_card' => 'KTP pemilik',
                    'tax_paid' => 'Bukti pembayaran PKB/SWDKLLJ', 'kir' => 'Bukti uji KIR (jika wajib)', 'service_book' => 'Buku servis/manual', 'policy' => 'Polis asuransi',
                    'ownership_changes' => 'Riwayat perubahan kepemilikan', 'import_documents' => 'Dokumen impor/karoseri (jika relevan)',
                ], 'documents.')],
            ]],
            ['key' => 'legal', 'label' => 'C2. Pemeriksaan legal (ya/tidak)', 'blocks' => [['layout' => 'checks', 'items' => Fields::many('yesno', [
                'original_bpkb' => 'BPKB asli telah dilihat', 'original_stnk' => 'STNK asli telah dilihat', 'names' => 'Nama/identitas konsisten', 'chassis' => 'Nomor rangka sesuai dokumen',
                'engine' => 'Nomor mesin sesuai dokumen', 'not_altered' => 'Tidak terdapat bekas perubahan nomor', 'no_dispute' => 'Tidak dalam blokir/sita/sengketa',
                'not_pledged' => 'Tidak dijaminkan kepada pihak lain', 'consent' => 'Persetujuan pasangan/pemilik tersedia', 'third_party' => 'Agunan pihak ketiga dianalisa',
                'tax_kir' => 'Pajak dan KIR tidak bermasalah material', 'fiduciary' => 'Dapat diikat fidusia', 'not_stolen' => 'Tidak terindikasi kendaraan curian',
                'no_hidden_financing' => 'Tidak ada pembiayaan aktif yang disembunyikan',
            ], 'legal.')]]],
        ];
    }

    /**
     * @return list<array{key: string, label: string, blocks: list<array<string, mixed>>}>
     */
    private static function inspection(): array
    {
        return [
            ['key' => 'history', 'label' => 'D. Riwayat dan penggunaan kendaraan', 'blocks' => [['layout' => 'fields', 'items' => Fields::many('text', [
                'owner_number' => 'Pemilik ke-', 'acquired' => 'Tanggal/perkiraan perolehan', 'price' => 'Harga perolehan', 'source' => 'Sumber perolehan', 'daily_use' => 'Penggunaan harian dan jarak tempuh',
                'service' => 'Riwayat servis berkala', 'workshop' => 'Bengkel servis terakhir', 'accident' => 'Riwayat kecelakaan', 'flood' => 'Riwayat banjir/terendam', 'fire' => 'Riwayat kebakaran',
                'overhaul' => 'Riwayat turun mesin', 'modifications' => 'Riwayat modifikasi', 'heavy_use' => 'Penggunaan angkutan/sewa/operasional berat', 'idle' => 'Lama tidak digunakan',
                'holder' => 'Keterangan pihak yang menguasai',
            ], 'history.')]]],
            ['key' => 'condition', 'label' => 'E. Pemeriksaan kondisi fisik: umum', 'blocks' => [['layout' => 'ratings', 'items' => Fields::many('rating', [
                'chassis_match' => 'Kesesuaian nomor rangka', 'engine_match' => 'Kesesuaian nomor mesin', 'frame' => 'Kondisi rangka/chassis', 'body' => 'Kondisi bodi dan cat',
                'filler' => 'Indikasi dempul/las/perbaikan berat', 'glass_lights' => 'Kaca/lampu/spion', 'tyres' => 'Ban dan velg', 'brakes' => 'Rem', 'suspension' => 'Suspensi', 'steering' => 'Kemudi',
                'electrics' => 'Kelistrikan dan aki', 'panel' => 'Panel/instrumen', 'leaks' => 'Kebocoran oli/cairan', 'noise' => 'Suara/getaran mesin', 'cooling' => 'Sistem pendingin',
                'exhaust' => 'Knalpot/emisi', 'cleanliness' => 'Kebersihan dan pemeliharaan', 'toolkit' => 'Kelengkapan toolkit/ban serep',
            ], 'condition.')]]],
            ['key' => 'car', 'label' => 'F. Pemeriksaan khusus roda 4', 'blocks' => [['layout' => 'ratings', 'items' => Fields::many('rating', [
                'engine_transmission' => 'Mesin dan transmisi', 'clutch' => 'Kopling/torque converter', 'four_wheel_drive' => 'Sistem 4WD/AWD (jika ada)', 'air_conditioning' => 'AC dan blower',
                'interior' => 'Interior/jok/dashboard', 'power_windows' => 'Power window/central lock', 'airbag' => 'Airbag dan indikator keselamatan', 'abs' => 'ABS/EBD dan indikator',
                'doors' => 'Pintu/bagasi/kap mesin', 'underbody' => 'Chassis bawah dan korosi', 'spare_wheel' => 'Ban serep/dongkrak/kunci roda', 'bodywork' => 'Karoseri/bak/chassis komersial',
                'special_equipment' => 'KIR/tachometer/peralatan khusus',
            ], 'car.')]]],
            ['key' => 'motorcycle', 'label' => 'G. Pemeriksaan khusus roda 2', 'blocks' => [['layout' => 'ratings', 'items' => Fields::many('rating', [
                'engine_transmission' => 'Mesin dan transmisi/CVT', 'chain' => 'Rantai/sprocket atau belt', 'fork' => 'Garpu depan dan komstir', 'swing_arm' => 'Swing arm dan shock belakang',
                'brakes' => 'Rem depan/belakang', 'speedometer' => 'Panel speedometer/odometer', 'tank_fairing' => 'Tangki/bodi/fairing', 'seat' => 'Jok dan footstep', 'stands' => 'Standar samping/tengah',
                'lights' => 'Lampu/sein/klakson', 'ignition' => 'Kunci kontak/immobilizer', 'accessories' => 'Helm/boks/aksesori yang menjadi satu kesatuan',
            ], 'motorcycle.')]]],
            ['key' => 'diagnostics', 'label' => 'H. Test fungsi dan diagnostik', 'blocks' => [['layout' => 'fields', 'items' => Fields::many('text', [
                'cold_start' => 'Mesin hidup dari kondisi dingin', 'idle' => 'Idle dan akselerasi', 'gears' => 'Perpindahan transmisi', 'braking' => 'Pengereman', 'steering' => 'Kemudi dan kestabilan',
                'noise' => 'Suara/getaran abnormal', 'warnings' => 'Indikator peringatan pada panel', 'road_test' => 'Test jalan (jika aman dan diizinkan)', 'scanner' => 'Hasil scanner/diagnostik (jika tersedia)',
                'operating' => 'Kesimpulan kelayakan operasi',
            ], 'diagnostics.')]]],
        ];
    }

    /**
     * @return list<array{key: string, label: string, blocks: list<array<string, mixed>>}>
     */
    private static function valuation(): array
    {
        return [
            ['key' => 'comparables', 'label' => 'I. Data pembanding pasar', 'blocks' => [self::comparables()]],
            ['key' => 'adjustments', 'label' => 'J. Penyesuaian pembanding', 'blocks' => [self::adjustments()]],
            ['key' => 'repair', 'label' => 'K. Estimasi biaya perbaikan dan haircut', 'blocks' => self::repair()],
            ['key' => 'reconciliation', 'label' => 'L. Rekonsiliasi dan penetapan nilai', 'blocks' => self::reconciliation()],
            ['key' => 'ltv', 'label' => 'M. Analisa LTV dan stress value', 'blocks' => CommonSections::loanToValue(['s10' => 'Nilai turun 10%', 's20' => 'Nilai turun 20%', 's30' => 'Nilai turun 30%'])],
        ];
    }

    /**
     * @return list<array{key: string, label: string, blocks: list<array<string, mixed>>}>
     */
    private static function closing(): array
    {
        return [
            ['key' => 'risks', 'label' => 'N. Penilaian risiko agunan', 'blocks' => [['layout' => 'ratings', 'items' => Fields::many('risk', [
                'legal' => 'Legalitas dan kepemilikan', 'numbers' => 'Keaslian nomor rangka/mesin', 'age' => 'Umur kendaraan', 'engine' => 'Kondisi mesin/transmisi', 'frame' => 'Kondisi rangka/bodi',
                'history' => 'Riwayat kecelakaan/banjir', 'use' => 'Odometer dan intensitas pemakaian', 'tax_kir' => 'Pajak/KIR', 'marketability' => 'Marketability dan suku cadang',
                'volatility' => 'Volatilitas harga', 'loss' => 'Risiko kehilangan/pengalihan', 'insurance' => 'Kecukupan asuransi',
            ], 'risks.')]]],
            ['key' => 'binding', 'label' => 'O. Fidusia, asuransi, dan pengamanan', 'blocks' => [['layout' => 'fields', 'items' => Fields::many('text', [
                'fiduciary' => 'Jenis pengikatan fidusia', 'guarantee_value' => 'Nilai penjaminan', 'notary' => 'Notaris/pendaftaran fidusia', 'bpkb_kept' => 'BPKB disimpan Bank',
                'document_protection' => 'Blokir/perlindungan dokumen jika berlaku', 'insurance' => 'Asuransi: TLO/Comprehensive', 'extended_insurance' => 'Perluasan banjir/gempa/huru-hara',
                'bankers_clause' => 'Banker’s clause', 'tracker' => 'GPS/tracker (jika disyaratkan)', 'spare_key' => 'Kunci cadangan', 'no_transfer' => 'Larangan pengalihan/modifikasi',
                'reinspection' => 'Jadwal inspeksi ulang',
            ], 'binding.')]]],
            ['key' => 'photos', 'label' => 'P. Dokumentasi pemeriksaan', 'blocks' => [['layout' => 'photos', 'items' => Fields::many('photo', [
                'front' => 'Tampak depan', 'rear' => 'Tampak belakang', 'left' => 'Sisi kiri', 'right' => 'Sisi kanan', 'interior' => 'Interior/dashboard/odometer', 'engine_bay' => 'Ruang mesin (roda 4)',
                'chassis_number' => 'Nomor rangka', 'engine_number' => 'Nomor mesin', 'tyres_underbody' => 'Ban/velg dan chassis bawah', 'damage' => 'Kerusakan/kondisi khusus',
                'documents' => 'BPKB dan STNK asli', 'keys_accessories' => 'Kunci dan aksesori',
            ], 'photos.')]]],
            ['key' => 'conclusion', 'label' => 'Q. Kesimpulan penilai', 'blocks' => CommonSections::conclusion()],
            ['key' => 'independence', 'label' => 'R. Pernyataan independensi', 'blocks' => [['layout' => 'checks', 'items' => Fields::many('yesno', [
                'no_interest' => 'Tidak memiliki kepentingan atau menerima manfaat terkait', 'scope' => 'Pemeriksaan fisik dan dokumen dilakukan sesuai ruang lingkup',
                'comparables_verified' => 'Data pembanding telah diverifikasi', 'not_by_ceiling' => 'Nilai tidak ditentukan oleh kebutuhan plafon',
            ], 'independence.')]]],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function comparables(): array
    {
        return ['layout' => 'grid', 'columns' => CommonSections::comparableColumns(), 'rows' => [
            ['key' => 'source', 'label' => 'Sumber/tanggal data'], ['key' => 'model', 'label' => 'Merek/tipe/varian'], ['key' => 'year', 'label' => 'Tahun'],
            ['key' => 'transmission', 'label' => 'Transmisi/CC'], ['key' => 'odometer', 'label' => 'Odometer'], ['key' => 'condition', 'label' => 'Kondisi'], ['key' => 'location', 'label' => 'Lokasi'],
            ['key' => 'price', 'label' => 'Harga penawaran/transaksi', 'type' => 'money'], ['key' => 'document_status', 'label' => 'Status dokumen'], ['key' => 'contact', 'label' => 'Nomor kontak/bukti'],
        ]];
    }

    /**
     * @return array<string, mixed>
     */
    private static function adjustments(): array
    {
        $labels = [
            'time' => 'Waktu data', 'location' => 'Lokasi', 'age' => 'Tahun/umur', 'variant' => 'Varian/transmisi', 'odometer' => 'Odometer', 'condition' => 'Kondisi fisik/mesin',
            'accident' => 'Riwayat kecelakaan/banjir', 'documents' => 'Dokumen/pajak/KIR', 'accessories' => 'Aksesori/modifikasi', 'offer' => 'Status penawaran',
        ];
        $rows = [];

        foreach ($labels as $key => $label) {
            $rows[] = ['key' => $key, 'label' => $label.' (%)', 'type' => 'adjustment'];
        }

        $rows[] = ['key' => 'total', 'label' => 'Total penyesuaian (%)', 'computed' => true, 'format' => 'percent'];
        $rows[] = ['key' => 'indicative', 'label' => 'Nilai indikasi', 'computed' => true, 'format' => 'money'];

        return ['layout' => 'grid', 'columns' => CommonSections::comparableColumns(), 'rows' => $rows];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function repair(): array
    {
        $repairs = [];

        foreach (self::REPAIR_ROWS as $index => $key) {
            $repairs[] = ['key' => $key, 'label' => 'Temuan '.($index + 1)];
        }

        $haircuts = [];

        foreach (['h_age' => 'Umur dan depresiasi', 'h_odometer' => 'Odometer/pemakaian', 'h_technical' => 'Kondisi teknis', 'h_history' => 'Riwayat kecelakaan/banjir',
            'h_market' => 'Marketability dan waktu jual', 'h_documents' => 'Pajak/KIR/dokumen', 'h_costs' => 'Biaya penarikan/penyimpanan/penjualan'] as $key => $label) {
            $haircuts[] = ['key' => $key, 'label' => $label];
        }

        return [
            ['layout' => 'grid', 'rows' => $repairs, 'columns' => [
                ['key' => 'finding', 'label' => 'Komponen/temuan', 'type' => 'text'], ['key' => 'cost', 'label' => 'Estimasi biaya', 'type' => 'money'], ['key' => 'basis', 'label' => 'Dasar/bukti', 'type' => 'text'],
            ]],
            ['layout' => 'fields', 'items' => [Fields::item('repair.total_cost', 'Total estimasi biaya perbaikan', 'computed', ['format' => 'money'])]],
            ['layout' => 'grid', 'rows' => $haircuts, 'columns' => [
                ['key' => 'percent', 'label' => 'Persentase (%)', 'type' => 'percent'], ['key' => 'basis', 'label' => 'Dasar', 'type' => 'text'],
            ]],
            ['layout' => 'fields', 'items' => [Fields::item('repair.total_haircut', 'Total haircut (%)', 'computed', ['format' => 'percent', 'hint' => 'Jumlah faktor di atas, paling besar 100%. Hindari penghitungan ganda.'])]],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function reconciliation(): array
    {
        return [['layout' => 'fields', 'items' => [
            Fields::item('reconciliation.indicative_1', 'Harga indikasi pembanding 1', 'computed', ['format' => 'money']),
            Fields::item('reconciliation.indicative_2', 'Harga indikasi pembanding 2', 'computed', ['format' => 'money']),
            Fields::item('reconciliation.indicative_3', 'Harga indikasi pembanding 3', 'computed', ['format' => 'money']),
            Fields::item('reconciliation.suggested', 'Rata-rata harga indikasi', 'computed', ['format' => 'money']),
            Fields::item('reconciliation.selected', 'Nilai pasar sebelum perbaikan', 'money', ['hint' => 'Dipilih penilai dari harga indikasi pembanding.']),
            Fields::item('reconciliation.repair', 'Estimasi biaya perbaikan', 'computed', ['format' => 'money']),
            Fields::item('reconciliation.market_value', 'Nilai pasar', 'computed', ['format' => 'money', 'hint' => 'Nilai pasar sebelum perbaikan dikurangi biaya perbaikan.']),
            Fields::item('reconciliation.haircut', 'Haircut nilai likuidasi (%)', 'computed', ['format' => 'percent']),
            Fields::item('reconciliation.liquidation_value', 'Nilai likuidasi', 'computed', ['format' => 'money']),
            Fields::item('h:forced_sale_value', 'Nilai jual cepat/forced sale', 'money'),
        ]]];
    }
}
