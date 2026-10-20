<?php

namespace Database\Seeders\Examples;

/**
 * Answers of the two sample land and building appraisals, by item key. A value is the answer, or [answer, note] for an item that has a
 * note. Every observation is invented to show how the form is filled; none describes a real object.
 */
final class LandExamples
{
    private const SAMPLE = '[CONTOH PENGISIAN] Penilaian ini diisi oleh seeder untuk menunjukkan cara memakai form; bukan penilaian atas objek nyata. ';

    /**
     * The answers that both samples share: the form of a modest house on a village road.
     *
     * @return array<string, string|array{0: string, 1: string}>
     */
    private static function base(): array
    {
        return [
            'h:purpose' => 'new', 'h:inspection' => 'full', 'h:owner_contact' => '0812-0000-0000', 'h:appraiser_name' => 'PENILAI CONTOH', 'h:reviewer_name' => 'REVIEWER CONTOH',
            'object.best_use' => 'Rumah tinggal', 'object.occupant' => 'Pemilik dan keluarga', 'object.distance' => 'Sekitar 25 km dari kantor Bank',
            'land_documents.name_match' => 'Nama pada sertipikat sama dengan identitas pemilik', 'building_documents.insurance' => 'Belum diasuransikan',
            'building_documents.violations' => 'Tidak ada teguran', 'building_documents.taxes' => 'PBB terakhir lunas',
            'legal.original_documents' => ['yes', 'Dokumen asli dilihat saat inspeksi'], 'legal.identity' => ['yes', 'Cocok dengan KTP pemilik'], 'legal.boundaries' => ['yes', 'Batas sesuai surat ukur'],
            'legal.no_dispute' => ['yes', 'Keterangan ketua RT dan tetangga batas'], 'legal.no_blockade' => ['yes', 'Pengecekan di kantor pertanahan bersih'], 'legal.not_pledged' => ['yes', 'Pengecekan sertipikat'],
            'legal.third_party' => ['yes', 'Pemilik hadir dan menyetujui'], 'legal.access_basis' => ['yes', 'Menghadap jalan lingkungan'], 'legal.not_public_land' => ['yes', 'Bukan fasum/fasos'],
            'legal.no_acquisition_plan' => ['yes', 'Tidak diketahui ada rencana'], 'legal.no_obstructing_tenant' => ['yes', 'Dihuni pemilik'],
            'land_physical.shape' => 'regular', 'land_physical.position' => 'middle', 'land_physical.road_width' => '4 m', 'land_physical.road_surface' => 'Cor beton', 'land_physical.elevation' => 'Setara jalan',
            'land_physical.topography' => 'Datar', 'land_physical.drainage' => 'Baik, saluran terbuka di depan', 'land_physical.fence' => 'Pagar tembok sebagian', 'land_physical.utilities' => 'Listrik, air sumur/PDAM',
            'land_physical.surroundings' => 'Permukiman padat', 'land_physical.hazards' => 'Tidak ada genangan yang diketahui', 'land_physical.contamination' => 'Tidak ada', 'land_physical.vehicle_access' => 'Roda 4 bisa masuk',
            'land_physical.marketability' => 'Cukup: permukiman yang hidup',
            'building_physical.function.main' => 'Rumah tinggal', 'building_physical.floors.main' => '1', 'building_physical.structure.main' => 'Bata, fondasi batu kali', 'building_physical.walls.main' => 'Bata plester cat',
            'building_physical.floor.main' => 'Keramik', 'building_physical.roof.main' => 'Genteng, rangka kayu', 'building_physical.ceiling.main' => 'Triplek', 'building_physical.doors.main' => 'Kayu',
            'building_physical.electricity.main' => 'Terpasang, baik', 'building_physical.water.main' => 'Sumur bor', 'building_physical.upkeep.main' => 'Cukup terawat', 'building_physical.materials.main' => 'Standar',
            'building_physical.life.main' => '15 tahun', 'building_physical.damage.main' => 'Cat mengelupas, tanpa kerusakan struktur', 'building_physical.fit.main' => 'Sesuai fungsi',
            'zoning.plan' => 'Permukiman perdesaan', 'zoning.zoning_match' => 'Sesuai', 'zoning.setbacks' => 'Di luar sempadan', 'zoning.protected_area' => 'Bukan kawasan lindung', 'zoning.projects' => 'Tidak ada yang diketahui',
            'zoning.density' => 'Sedang, tumbuh pelan', 'zoning.facilities' => 'Sekolah dan pasar dalam 2 km', 'zoning.security' => 'Aman', 'zoning.demand' => 'Cukup untuk rumah tipe sejenis',
            'zoning.normal_marketing' => '6 sampai 9 bulan', 'zoning.forced_marketing' => '12 bulan atau lebih', 'zoning.buyers' => 'Keluarga muda dan pedagang setempat', 'zoning.limits' => 'Pasar perdesaan yang kecil',
            'comparables.source.c1' => 'Broker setempat, September 2026', 'comparables.source.c2' => 'Transaksi tetangga, Agustus 2026', 'comparables.source.c3' => 'Iklan properti, Oktober 2026',
            'comparables.status.c1' => 'Penawaran', 'comparables.status.c2' => 'Transaksi', 'comparables.status.c3' => 'Penawaran', 'comparables.right.c1' => 'SHM', 'comparables.right.c2' => 'SHM', 'comparables.right.c3' => 'SHM',
            'comparables.distance.c1' => '1,5 km', 'comparables.distance.c2' => '0,5 km', 'comparables.distance.c3' => '3 km',
            'risks.legal' => ['low', 'Dokumen lengkap'], 'risks.right_term' => ['low', 'Hak milik tanpa batas waktu'], 'risks.access' => ['low', 'Jalan lingkungan 4 m'], 'risks.shape' => ['low', 'Bentuk beraturan'],
            'risks.disaster' => ['low', 'Tidak ada riwayat banjir'], 'risks.permit_zoning' => ['medium', 'Izin bangunan belum diurus'], 'risks.building' => ['medium', 'Bangunan sederhana'], 'risks.occupancy' => ['low', 'Dihuni pemilik'],
            'risks.marketability' => ['medium', 'Pasar perdesaan'], 'risks.volatility' => ['low', 'Harga tanah stabil'], 'risks.insurance' => ['medium', 'Belum diasuransikan'], 'risks.third_party' => ['low', 'Pemilik hadir'],
            'binding.notary' => 'Notaris/PPAT rekanan Bank', 'binding.bankers_clause' => 'Ya, bila diasuransikan', 'binding.monitoring' => 'Kunjungan ulang setiap tahun',
            'independence.no_interest' => 'yes', 'independence.no_benefit' => 'yes', 'independence.scope' => 'yes', 'independence.comparables_verified' => 'yes', 'independence.assumptions' => 'yes', 'independence.not_by_ceiling' => 'yes',
            'h:reappraisal_frequency' => 'Setiap tahun atau saat ada perubahan material', 'h:valid_until' => '2027-10-07',
        ];
    }

    /**
     * Land with a mortgage right on paper, whose papers are customary ones: the assessment ends in a refusal.
     *
     * @return array<string, string|array{0: string, 1: string}>
     */
    public static function customaryPapers(): array
    {
        return [...self::base(),
            'h:debtor_name' => 'SANDI YUDA MAULANA', 'h:product_label' => 'KREDIT MODAL KERJA', 'h:credit_ceiling' => '250000000', 'h:owner_address' => 'JALAN KAPTEN HANAFIAH RAWABADAK RT/RW 101/28 KARANGANYAR SUBANG',
            'h:owner_relation' => 'Pihak ketiga, hubungan tercatat di berkas', 'h:effective_date' => '2026-10-07', 'h:report_date' => '2026-10-08',
            'object.current_use' => 'Rumah tinggal dan warung kecil', 'land_documents.measured_area' => '197', 'land_documents.certificate_check' => 'Tidak ada sertipikat: alas hak Letter C/girik',
            'land_documents.encumbrance' => 'Belum ada catatan; belum terdaftar di kantor pertanahan', 'land_documents.tax' => 'SPPT atas nama pemilik, lunas',
            'building_documents.actual_area' => '60', 'building_documents.permit_match' => 'Belum ada PBG', 'building_documents.plans' => 'Tidak ada',
            'legal.can_be_encumbered' => ['no', 'Letter C/girik belum bisa dibebani Hak Tanggungan sebelum disertipikatkan'], 'legal.spouse_consent' => ['yes', 'Surat persetujuan pasangan'], 'legal.owners_consent' => ['yes', 'Pemilik tunggal'],
            'land_physical.area' => '197', 'land_physical.dimensions' => '10 x 19,7 m', 'land_physical.border_north' => 'Rumah warga', 'land_physical.border_east' => 'Jalan lingkungan', 'land_physical.border_south' => 'Kebun', 'land_physical.border_west' => 'Rumah warga',
            'building_physical.area.main' => '60', 'building_physical.built.main' => '2008', 'building_physical.function.other' => 'Warung dan teras', 'building_physical.area.other' => '12',
            'comparables.address.c1' => 'Desa tetangga, jalan 4 m', 'comparables.address.c2' => 'Gang sebelah', 'comparables.address.c3' => 'Jalan desa, 3 km',
            'comparables.land_area.c1' => '180', 'comparables.land_area.c2' => '210', 'comparables.land_area.c3' => '200', 'comparables.land_per_m2.c1' => '2100000', 'comparables.land_per_m2.c2' => '2200000', 'comparables.land_per_m2.c3' => '2000000',
            'comparables.building_condition.c1' => 'Rumah sederhana', 'comparables.building_condition.c2' => 'Rumah sederhana', 'comparables.building_condition.c3' => 'Tanah kosong', 'comparables.road.c1' => '4 m', 'comparables.road.c2' => '3 m', 'comparables.road.c3' => '4 m',
            'adjustments.time.c1' => '-3', 'adjustments.location.c1' => '-2', 'adjustments.right.c1' => '-10', 'adjustments.time.c2' => '-2', 'adjustments.shape.c2' => '-3', 'adjustments.right.c2' => '-10',
            'adjustments.right.c3' => '-10', 'adjustments.offer.c3' => '-5', 'adjustments.selected' => '1800000',
            'cost.main.area' => '60', 'cost.main.unit_cost' => '3000000', 'cost.fence.area' => '30', 'cost.fence.unit_cost' => '400000', 'cost.fence.depreciation' => '50',
            'cost.physical' => '20', 'cost.functional' => '10', 'cost.external' => '5',
            'reconciliation.land.selected' => '354600000', 'reconciliation.building.selected' => '117000000', 'reconciliation.facilities.selected' => '6000000', 'reconciliation.deductions.selected' => '30000000',
            'h:haircut_percent' => '25', 'reconciliation.haircut_basis' => 'Alas hak belum sertipikat dan pasar perdesaan', 'ltv.execution_cost' => '20000000',
            'risks.legal' => ['high', 'Alas hak girik: belum bisa diikat Hak Tanggungan'], 'risks.right_term' => ['medium', 'Belum ada hak yang terdaftar'],
            'binding.kind' => 'Belum bisa APHT; SKMHT pun tidak dapat dibuat atas Letter C', 'binding.pre_disbursement' => 'Sertipikatkan dulu menjadi SHM atas nama pemilik',
            'conclusion.summary' => self::SAMPLE.'Rumah tinggal dan warung 60 m² di atas tanah 197 m² di permukiman perdesaan, beralas hak Letter C/girik.',
            'conclusion.strengths' => 'Lokasi di permukiman yang hidup, bentuk tanah beraturan, dihuni pemilik.', 'conclusion.weaknesses' => 'Tercatat sebagai agunan bersertipikat dengan Hak Tanggungan, padahal alas haknya girik.',
            'conclusion.assumptions' => 'Nilai pasar mengandaikan alas hak sudah menjadi SHM; biaya sertipikasi dikurangkan sebagai pengurang.', 'conclusion.legal' => 'Tidak cukup: tidak dapat diikat Hak Tanggungan.',
            'conclusion.physical' => 'Cukup.', 'conclusion.mitigation' => 'Sertipikatkan lebih dulu di Kantor Pertanahan, lalu nilai ulang untuk diikat APHT.',
            'h:decision' => 'rejected', 'h:main_conditions' => 'Agunan baru dapat dinilai ulang setelah terbit SHM atas nama pemilik.',
        ];
    }

    /**
     * Land with a certificate and no binding yet, held by two owners: accepted on conditions.
     *
     * @return array<string, string|array{0: string, 1: string}>
     */
    public static function certificateWithoutBinding(): array
    {
        return [...self::base(),
            'h:debtor_name' => 'SAEHPUDIN', 'h:product_label' => 'KREDIT PERDAGANGAN', 'h:credit_ceiling' => '40000000', 'h:owner_address' => 'DUSUN LENGKONG BARAT RT/RW 11/04 LENGKONGJAYA PAMANUKAN SUBANG',
            'h:owner_relation' => 'Orang tua debitur', 'h:effective_date' => '2026-10-15', 'h:report_date' => '2026-10-16',
            'object.current_use' => 'Rumah tinggal', 'land_documents.measured_area' => '67', 'land_documents.certificate_check' => 'Dicek di kantor pertanahan: SHM atas nama dua pemilik',
            'land_documents.encumbrance' => 'Belum ada Hak Tanggungan, blokir, atau sita', 'land_documents.tax' => 'SPPT lunas',
            'building_documents.actual_area' => '36', 'building_documents.permit_match' => 'Belum ada PBG', 'building_documents.plans' => 'Tidak ada',
            'legal.can_be_encumbered' => ['yes', 'SHM, belum dibebani'], 'legal.spouse_consent' => ['yes', 'Surat persetujuan pasangan kedua pemilik'], 'legal.owners_consent' => ['yes', 'Kedua pemilik menandatangani; bukti diminta saat akad'],
            'land_physical.area' => '67', 'land_physical.dimensions' => '6 x 11,2 m', 'land_physical.border_north' => 'Rumah warga', 'land_physical.border_east' => 'Jalan dusun', 'land_physical.border_south' => 'Rumah warga', 'land_physical.border_west' => 'Kebun',
            'building_physical.area.main' => '36', 'building_physical.built.main' => '2010',
            'comparables.address.c1' => 'Dusun sebelah', 'comparables.address.c2' => 'Lengkongjaya', 'comparables.address.c3' => 'Dusun tetangga, jalan lebih lebar',
            'comparables.land_area.c1' => '70', 'comparables.land_area.c2' => '80', 'comparables.land_area.c3' => '60', 'comparables.land_per_m2.c1' => '750000', 'comparables.land_per_m2.c2' => '800000', 'comparables.land_per_m2.c3' => '650000',
            'comparables.building_condition.c1' => 'Sederhana', 'comparables.building_condition.c2' => 'Sederhana', 'comparables.building_condition.c3' => 'Tanah kosong', 'comparables.road.c1' => '3 m', 'comparables.road.c2' => '3 m', 'comparables.road.c3' => '5 m',
            'adjustments.location.c1' => '-5', 'adjustments.time.c2' => '-2', 'adjustments.shape.c2' => '-3', 'adjustments.location.c3' => '8', 'adjustments.selected' => '700000',
            'cost.main.area' => '36', 'cost.main.unit_cost' => '2000000', 'cost.physical' => '25', 'cost.functional' => '10', 'cost.external' => '10',
            'reconciliation.land.selected' => '46900000', 'reconciliation.building.selected' => '39600000', 'h:haircut_percent' => '25', 'reconciliation.haircut_basis' => 'Pasar perdesaan dan bangunan sederhana',
            'ltv.execution_cost' => '5000000',
            'binding.kind' => 'APHT (nilai plafon) atau SKMHT bila Bank mensyaratkan', 'binding.consent' => 'Kedua pemilik hadir dan menandatangani', 'binding.document_fixes' => 'Urus PBG bila diminta pemutus',
            'binding.pre_disbursement' => 'Pengikatan terdaftar sebelum pencairan', 'binding.fire_insurance' => 'Disarankan, belum ada',
            'conclusion.summary' => self::SAMPLE.'Rumah sederhana 36 m² di atas tanah 67 m², SHM atas nama dua pemilik, belum dibebani.',
            'conclusion.strengths' => 'SHM bersih, bentuk beraturan, dihuni pemilik.', 'conclusion.weaknesses' => 'Dua pemilik: setiap pengikatan butuh tanda tangan keduanya; nilai kecil dan pasar perdesaan.',
            'conclusion.assumptions' => 'Harga pembanding diverifikasi lewat telepon; bangunan dinilai dengan pendekatan biaya.', 'conclusion.legal' => 'Cukup, dengan syarat pengikatan.',
            'conclusion.physical' => 'Cukup.', 'conclusion.mitigation' => 'Ikat APHT sebelum pencairan dan minta persetujuan tertulis kedua pemilik.',
            'h:decision' => 'conditional', 'h:main_conditions' => 'Pengikatan APHT terdaftar sebelum pencairan; kedua pemilik menandatangani; asuransi kebakaran dianjurkan.', 'h:credit_basis_value' => '48000000',
        ];
    }
}
