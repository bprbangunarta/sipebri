<?php

namespace Database\Seeders\Examples;

/**
 * Answers of the two sample vehicle appraisals (a car and a motorcycle), by item key. A value is the answer, or [answer, note] for an item
 * that has a note. Every observation is invented to show how the form is filled; none describes a real vehicle.
 */
final class VehicleExamples
{
    private const SAMPLE = '[CONTOH PENGISIAN] Penilaian ini diisi oleh seeder untuk menunjukkan cara memakai form; bukan penilaian atas kendaraan nyata. ';

    /**
     * @return array<string, string|array{0: string, 1: string}>
     */
    private static function base(): array
    {
        return [
            'h:purpose' => 'new', 'h:owner_contact' => '0812-0000-0000', 'h:appraiser_name' => 'PENILAI CONTOH', 'h:reviewer_name' => 'REVIEWER CONTOH', 'identity.place' => 'Rumah pemilik',
            'vehicle.keys' => '2', 'vehicle.usage' => 'personal', 'vehicle.parking' => 'Garasi rumah',
            'documents.bpkb_name' => 'Sama dengan pemilik', 'documents.stnk_name' => 'Sama dengan pemilik', 'documents.id_card' => 'KTP asli dilihat, sesuai nama BPKB', 'documents.tax_paid' => 'Pajak tahun berjalan lunas',
            'documents.service_book' => 'Ada, servis berkala di bengkel umum', 'documents.policy' => 'Belum ada', 'documents.ownership_changes' => 'Tidak ada riwayat balik nama',
            'legal.original_bpkb' => ['yes', 'BPKB asli dilihat'], 'legal.original_stnk' => ['yes', 'STNK asli dilihat'], 'legal.names' => ['yes', 'Nama BPKB, STNK, dan KTP sama'],
            'legal.chassis' => ['yes', 'Digesek dan dicocokkan dengan BPKB'], 'legal.engine' => ['yes', 'Dicocokkan dengan BPKB'], 'legal.not_altered' => ['yes', 'Tidak ada bekas pahatan ulang'],
            'legal.no_dispute' => ['yes', 'Pengecekan kepolisian dan keterangan pemilik'], 'legal.not_pledged' => ['yes', 'BPKB di tangan pemilik'], 'legal.consent' => ['yes', 'Surat persetujuan pasangan'],
            'legal.third_party' => ['yes', 'Pemilik hadir'], 'legal.tax_kir' => ['yes', 'Pajak lunas, KIR tidak wajib'], 'legal.fiduciary' => ['yes', 'Dapat didaftarkan fidusia'],
            'legal.not_stolen' => ['yes', 'Nomor tidak terdaftar sebagai kendaraan hilang'], 'legal.no_hidden_financing' => ['yes', 'Tidak ada kontrak pembiayaan aktif menurut pemilik'],
            'history.owner_number' => 'Pemilik kedua', 'history.accident' => 'Tidak ada', 'history.flood' => 'Tidak ada', 'history.fire' => 'Tidak ada', 'history.overhaul' => 'Tidak ada',
            'history.modifications' => 'Tidak ada', 'history.heavy_use' => 'Tidak', 'history.idle' => 'Dipakai setiap hari', 'history.holder' => 'Pemilik',
            'diagnostics.cold_start' => 'Hidup normal', 'diagnostics.idle' => 'Stabil', 'diagnostics.gears' => 'Halus', 'diagnostics.braking' => 'Normal', 'diagnostics.steering' => 'Stabil',
            'diagnostics.noise' => 'Wajar untuk umur', 'diagnostics.warnings' => 'Tidak ada lampu peringatan', 'diagnostics.road_test' => 'Dilakukan 5 km, normal',
            'diagnostics.operating' => 'Layak operasi',
            'comparables.source.c1' => 'Iklan, Oktober 2026', 'comparables.source.c2' => 'Showroom setempat, Oktober 2026', 'comparables.source.c3' => 'Transaksi kenalan, September 2026',
            'comparables.document_status.c1' => 'Lengkap', 'comparables.document_status.c2' => 'Lengkap', 'comparables.document_status.c3' => 'Lengkap', 'comparables.location.c1' => 'Subang', 'comparables.location.c2' => 'Subang', 'comparables.location.c3' => 'Pamanukan',
            'repair.h_costs.basis' => 'Biaya tarik, simpan, dan lelang kecil', 'repair.h_age.basis' => 'Umur dan depresiasi tahunan', 'repair.h_market.basis' => 'Pasar kendaraan bekas di Subang aktif',
            'risks.legal' => ['low', 'Dokumen lengkap'], 'risks.numbers' => ['low', 'Nomor cocok'], 'risks.engine' => ['low', 'Hasil test fungsi baik'], 'risks.frame' => ['low', 'Tanpa riwayat tabrakan'],
            'risks.history' => ['low', 'Tanpa kecelakaan atau banjir'], 'risks.tax_kir' => ['low', 'Pajak lunas'], 'risks.volatility' => ['medium', 'Harga bekas turun tiap tahun'], 'risks.loss' => ['medium', 'Kendaraan dipakai sehari-hari'],
            'risks.insurance' => ['medium', 'Asuransi disyaratkan'],
            'binding.fiduciary' => 'Fidusia (FEO), didaftarkan online', 'binding.bpkb_kept' => 'BPKB disimpan Bank', 'binding.bankers_clause' => 'Ya', 'binding.no_transfer' => 'Dilarang dialihkan atau dimodifikasi tanpa izin Bank',
            'binding.reinspection' => 'Setiap tahun', 'binding.spare_key' => 'Satu kunci cadangan diserahkan',
            'independence.no_interest' => 'yes', 'independence.scope' => 'yes', 'independence.comparables_verified' => 'yes', 'independence.not_by_ceiling' => 'yes',
            'h:reappraisal_frequency' => 'Setiap tahun, atau setelah kecelakaan atau perubahan dokumen',
        ];
    }

    /**
     * A used car: accepted on conditions.
     *
     * @return array<string, string|array{0: string, 1: string}>
     */
    public static function car(): array
    {
        return [...self::base(),
            'h:debtor_name' => 'SANDI YUDA MAULANA', 'h:product_label' => 'KREDIT MODAL KERJA', 'h:credit_ceiling' => '50000000', 'h:owner_address' => 'JALAN KAPTEN HANAFIAH RAWABADAK RT/RW 100/28 KARANGANYAR SUBANG',
            'h:owner_relation' => 'Pihak ketiga, hubungan tercatat di berkas', 'h:effective_date' => '2026-10-07', 'h:report_date' => '2026-10-08',
            'vehicle.stnk_valid_until' => '2027-03-01', 'vehicle.odometer' => '168000', 'vehicle.accessories' => 'Karpet dan sarung jok',
            'documents.bpkb' => 'BPKB J-05868974, tanggal penerbitan sesuai surat', 'documents.stnk' => 'STNK berlaku sampai Maret 2027', 'documents.kir' => 'Tidak wajib (kendaraan pribadi)',
            'history.acquired' => '2018 dari pemilik pertama', 'history.price' => '78000000', 'history.source' => 'Pembelian dari perorangan', 'history.daily_use' => 'Kota dan luar kota, rata-rata 15.000 km per tahun',
            'history.service' => 'Servis tiap 10.000 km sesuai buku', 'history.workshop' => 'Bengkel umum di Subang',
            'condition.chassis_match' => ['good', 'Cocok dengan BPKB'], 'condition.engine_match' => ['good', 'Cocok dengan BPKB'], 'condition.frame' => 'good', 'condition.body' => ['fair', 'Baret halus di pintu kanan'],
            'condition.filler' => 'good', 'condition.glass_lights' => 'good', 'condition.tyres' => ['fair', 'Ban tinggal 50%, ganti dalam 6 bulan'], 'condition.brakes' => 'good', 'condition.suspension' => 'good',
            'condition.steering' => 'good', 'condition.electrics' => 'good', 'condition.panel' => 'good', 'condition.leaks' => 'good', 'condition.noise' => ['fair', 'Getaran kecil saat idle'], 'condition.cooling' => 'good',
            'condition.exhaust' => 'good', 'condition.cleanliness' => 'good', 'condition.toolkit' => 'good',
            'car.engine_transmission' => 'good', 'car.clutch' => 'good', 'car.air_conditioning' => ['fair', 'Kurang dingin, perlu servis'], 'car.interior' => 'good', 'car.power_windows' => 'good',
            'car.airbag' => 'good', 'car.abs' => 'good', 'car.doors' => 'good', 'car.underbody' => 'good', 'car.spare_wheel' => 'good',
            'comparables.model.c1' => 'MPV 1.4 manual 2012', 'comparables.model.c2' => 'MPV 1.4 manual 2013', 'comparables.model.c3' => 'MPV 1.4 manual 2012', 'comparables.year.c1' => '2012', 'comparables.year.c2' => '2013', 'comparables.year.c3' => '2012',
            'comparables.transmission.c1' => 'Manual 1.400 cc', 'comparables.transmission.c2' => 'Manual 1.400 cc', 'comparables.transmission.c3' => 'Manual 1.400 cc',
            'comparables.odometer.c1' => '150000', 'comparables.odometer.c2' => '120000', 'comparables.odometer.c3' => '170000', 'comparables.condition.c1' => 'Baik', 'comparables.condition.c2' => 'Baik', 'comparables.condition.c3' => 'Cukup',
            'comparables.price.c1' => '95000000', 'comparables.price.c2' => '100000000', 'comparables.price.c3' => '88000000',
            'adjustments.odometer.c1' => '-3', 'adjustments.condition.c1' => '-2', 'adjustments.time.c2' => '-2', 'adjustments.odometer.c2' => '-5', 'adjustments.condition.c3' => '2',
            'reconciliation.selected' => '90000000', 'repair.r1.finding' => 'Servis AC', 'repair.r1.cost' => '2500000', 'repair.r1.basis' => 'Taksiran bengkel AC', 'repair.r2.finding' => 'Ganti dua ban', 'repair.r2.cost' => '3500000', 'repair.r2.basis' => 'Harga ban di toko setempat',
            'repair.h_age.percent' => '10', 'repair.h_odometer.percent' => '3', 'repair.h_technical.percent' => '2', 'repair.h_market.percent' => '3', 'repair.h_costs.percent' => '2', 'ltv.execution_cost' => '5000000',
            'risks.age' => ['medium', 'Umur sekitar 14 tahun'], 'risks.use' => ['medium', 'Odometer tinggi'], 'risks.marketability' => ['low', 'Model laris dan suku cadang mudah'],
            'binding.guarantee_value' => 'Sebesar plafon', 'binding.notary' => 'Notaris rekanan Bank', 'binding.document_protection' => 'BPKB diblokir di sistem Bank', 'binding.insurance' => 'Comprehensive',
            'binding.extended_insurance' => 'Banjir dan huru-hara', 'binding.tracker' => 'Tidak disyaratkan',
            'conclusion.summary' => self::SAMPLE.'MPV bekas berusia sekitar 14 tahun, odometer 168.000 km, kondisi umum baik dengan AC dan ban yang perlu diperbaiki.',
            'conclusion.strengths' => 'Dokumen lengkap, nomor cocok, model laris.', 'conclusion.weaknesses' => 'Umur tua dan odometer tinggi; nilai turun setiap tahun.',
            'conclusion.assumptions' => 'Pembanding diverifikasi lewat telepon; biaya perbaikan taksiran bengkel.', 'conclusion.legal' => 'Cukup.', 'conclusion.physical' => 'Cukup dengan perbaikan AC dan ban.',
            'conclusion.mitigation' => 'Asuransi comprehensive, BPKB disimpan Bank, nilai dinilai ulang setiap tahun.',
            'h:decision' => 'conditional', 'h:main_conditions' => 'Fidusia terdaftar sebelum pencairan; asuransi comprehensive dengan banker\'s clause; AC dan ban diperbaiki dalam 3 bulan.', 'h:credit_basis_value' => '60000000', 'h:valid_until' => '2027-10-07',
        ];
    }

    /**
     * A recent motorcycle: accepted.
     *
     * @return array<string, string|array{0: string, 1: string}>
     */
    public static function motorcycle(): array
    {
        return [...self::base(),
            'h:debtor_name' => 'MELVIN ANGGARA RAMDHAN', 'h:product_label' => 'KREDIT PEGAWAI', 'h:credit_ceiling' => '8000000', 'h:owner_address' => 'DUSUN CIMANGGU RT 09 RW 03, PAMANUKAN HILIR, PAMANUKAN',
            'h:owner_relation' => 'Debitur sendiri', 'h:effective_date' => '2026-08-07', 'h:report_date' => '2026-08-07',
            'vehicle.stnk_valid_until' => '2027-05-01', 'vehicle.odometer' => '32000', 'vehicle.accessories' => 'Tidak ada',
            'documents.bpkb' => 'BPKB S-02006684, sesuai surat', 'documents.stnk' => 'STNK berlaku sampai Mei 2027', 'documents.kir' => 'Tidak wajib',
            'history.acquired' => '2021 baru dari dealer', 'history.price' => '19000000', 'history.source' => 'Dealer', 'history.daily_use' => 'Ke tempat kerja, sekitar 8.000 km per tahun',
            'history.service' => 'Servis di dealer resmi', 'history.workshop' => 'Dealer resmi Pamanukan', 'history.owner_number' => 'Pemilik pertama',
            'condition.chassis_match' => ['good', 'Cocok'], 'condition.engine_match' => ['good', 'Cocok'], 'condition.frame' => 'good', 'condition.body' => ['fair', 'Baret halus di bodi samping'],
            'condition.filler' => 'good', 'condition.glass_lights' => 'good', 'condition.tyres' => 'good', 'condition.brakes' => ['fair', 'Kampas belakang tipis'], 'condition.suspension' => 'good', 'condition.steering' => 'good',
            'condition.electrics' => 'good', 'condition.panel' => 'good', 'condition.leaks' => 'good', 'condition.noise' => 'good', 'condition.cooling' => 'good', 'condition.exhaust' => 'good',
            'condition.cleanliness' => 'good', 'condition.toolkit' => 'good',
            'motorcycle.engine_transmission' => 'good', 'motorcycle.chain' => 'good', 'motorcycle.fork' => 'good', 'motorcycle.swing_arm' => 'good', 'motorcycle.brakes' => ['fair', 'Kampas belakang tipis'],
            'motorcycle.speedometer' => 'good', 'motorcycle.tank_fairing' => 'good', 'motorcycle.seat' => 'good', 'motorcycle.stands' => 'good', 'motorcycle.lights' => 'good', 'motorcycle.ignition' => 'good',
            'comparables.model.c1' => 'Skuter matik 110 cc 2021', 'comparables.model.c2' => 'Skuter matik 110 cc 2021', 'comparables.model.c3' => 'Skuter matik 110 cc 2021', 'comparables.year.c1' => '2021', 'comparables.year.c2' => '2021', 'comparables.year.c3' => '2021',
            'comparables.transmission.c1' => 'Otomatis 110 cc', 'comparables.transmission.c2' => 'Otomatis 110 cc', 'comparables.transmission.c3' => 'Otomatis 110 cc',
            'comparables.odometer.c1' => '25000', 'comparables.odometer.c2' => '30000', 'comparables.odometer.c3' => '20000', 'comparables.condition.c1' => 'Baik', 'comparables.condition.c2' => 'Baik', 'comparables.condition.c3' => 'Baik',
            'comparables.price.c1' => '15500000', 'comparables.price.c2' => '14800000', 'comparables.price.c3' => '16000000',
            'adjustments.odometer.c1' => '-5', 'adjustments.condition.c1' => '-2', 'adjustments.time.c2' => '-1', 'adjustments.odometer.c3' => '-8', 'adjustments.documents.c3' => '-2',
            'reconciliation.selected' => '14400000', 'repair.r1.finding' => 'Ganti kampas rem belakang', 'repair.r1.cost' => '300000', 'repair.r1.basis' => 'Harga bengkel',
            'repair.h_age.percent' => '8', 'repair.h_odometer.percent' => '4', 'repair.h_technical.percent' => '2', 'repair.h_market.percent' => '3', 'repair.h_costs.percent' => '3', 'ltv.execution_cost' => '800000',
            'risks.age' => ['low', 'Umur 5 tahun'], 'risks.use' => ['low', 'Pemakaian wajar'], 'risks.marketability' => ['low', 'Skuter laris'],
            'binding.guarantee_value' => 'Sebesar plafon', 'binding.notary' => 'Notaris rekanan Bank', 'binding.document_protection' => 'BPKB disimpan Bank', 'binding.insurance' => 'TLO',
            'binding.extended_insurance' => 'Tidak disyaratkan', 'binding.tracker' => 'Tidak disyaratkan',
            'conclusion.summary' => self::SAMPLE.'Skuter matik berusia 5 tahun, odometer 32.000 km, terawat di dealer resmi.',
            'conclusion.strengths' => 'Dokumen lengkap, pemilik pertama, servis resmi.', 'conclusion.weaknesses' => 'Nilai kecil dan turun setiap tahun.',
            'conclusion.assumptions' => 'Pembanding diverifikasi lewat telepon.', 'conclusion.legal' => 'Cukup.', 'conclusion.physical' => 'Cukup.', 'conclusion.mitigation' => 'BPKB disimpan Bank, asuransi TLO.',
            'h:decision' => 'accepted', 'h:credit_basis_value' => '9000000', 'h:valid_until' => '2027-08-07',
        ];
    }
}
