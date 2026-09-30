<?php

namespace Database\Seeders;

use App\Models\BindingType;
use App\Models\CollateralCondition;
use App\Models\CollateralMethod;
use App\Models\CollateralType;
use App\Models\Installment;
use App\Models\Institution;
use App\Models\Method;
use App\Models\Office;
use App\Models\OwnershipStatus;
use App\Models\Product;
use App\Models\ProductParameter;
use App\Models\Setting;
use App\Support\LendingLimit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Credit reference data, mirroring the core banking system. Idempotent: safe to re-run.
 */
class CreditReferenceSeeder extends Seeder
{
    /** [code, alias, name] */
    private const OFFICES = [
        ['00', 'PMK', 'Pamanukan'], ['01', 'CGK', 'Jalancagak'], ['02', 'SBG', 'Subang'], ['03', 'SKM', 'Sukamandi'],
        ['04', 'PGD', 'Pagaden'], ['05', 'KJT', 'Kalijati'], ['06', 'PSK', 'Pusakajaya'],
    ];

    /** [code, alias, name, active] */
    private const PRODUCTS = [
        ['01', 'KRU', 'KREDIT UMUM', true], ['02', 'KUP', 'KREDIT PEGAWAI', true], ['03', 'KRM', 'KREDIT MOTOR', false],
        ['04', 'PRK', 'KREDIT REKENING KORAN', false], ['05', 'KTO', 'KREDIT TAKE OVER', true], ['06', 'KBT', 'KREDIT BUDIDAYA PERTANIAN', true],
        ['07', 'KPS', 'KREDIT PEGAWAI SWASTA', true], ['08', 'KKO', 'KREDIT KENDARAAN OPERASIONAL', true], ['09', 'KIH', 'KREDIT IBADAH HAJI', true],
        ['10', 'KPJ', 'KREDIT PEGAWAI SWASTA NON MOU', true], ['11', 'KRS', 'KREDIT RESEPSI', true], ['12', 'KPN', 'KREDIT PEGAWAI NEGERI', false],
        ['13', 'KIU', 'KREDIT IBADAH UMROH', true], ['14', 'KTA', 'KREDIT TANPA AGUNAN', true], ['15', 'KPMI', 'KREDIT PEKERJA MIGRAN INDONESIA', true],
        ['16', 'KPP', 'KREDIT PENSIUN PN (CHANNELING)', true], ['17', 'KRISPI', 'KREDIT PASAR MINGGUAN', true],
    ];

    /** [code, name, tenor multiple in months] */
    private const INSTALLMENTS = [
        ['1', 'HARIAN', 1], ['2', 'MINGGUAN', 1], ['3', 'BULANAN', 1], ['4', 'TRIWULANAN', 3],
        ['5', 'SEMESTERAN', 6], ['6', 'TAHUNAN', 12], ['7', 'MUSIMAN', 6], ['8', 'NON ANGSURAN', 0],
    ];

    private const METHODS = [
        ['10', 'FLATE'], ['14', 'FLATE GP DISTRIBUSI'], ['16', 'FLATE MUSIMAN'], ['20', 'EFEKTIF HARIAN (RC)'],
        ['21', 'EFEKTIF HARIAN (NON ANGSUR)'], ['22', 'EFEKTIF BULANAN'], ['24', 'EFEKTIF NON ANGSUR'],
        ['30', 'ANUITAS'], ['40', 'KONVERSI'], ['41', 'KONVERSI CARI BUNGA'],
    ];

    private const COLLATERAL_TYPES = [
        ['01', 'SBI/SPN/ON/OR'], ['02', 'TABUNGAN / DEPOSITO'], ['03', 'LOGAM MULIA'], ['04', 'PERHIASAN EMAS'],
        ['05', 'TANAH/BNGN-SERTIFIKAT DGN HT'], ['06', 'TANAH/BNGN-SERTIFIKAT NON HT'], ['07', 'TANAH/BNGN-SURAT ADAT + SPPT'],
        ['08', 'TEMPAT USAHA'], ['09', 'RESI GUDANG'], ['10', 'KENDARAAN / KAPAL - FIDUCIA'], ['11', 'KENDARAAN / KAPAL - NOTARIEL'],
        ['12', 'KENDARAAN / KAPAL - LAINNYA'], ['13', 'BAGIAN DANA YG DIJAMIN BUMN/BUMD'], ['14', 'LAINNYA : SK/IJAZAH'],
        ['15', 'LAINNYA : SAHAM'], ['16', 'LAINNYA : REKSADANA'], ['17', 'LAINNYA : PERSEDIAAN'], ['18', 'LAINNYA : LAIN-LAIN'],
        ['99', 'TANPA AGUNAN'],
    ];

    private const BINDING_TYPES = [
        ['01', 'APHT : HAK TANGGUNGAN'], ['02', 'GADAI'], ['03', 'FEO : FIDUCIARE EIGENDOM OVERDRACHT'],
        ['04', 'SKMHT : SURAT KUASA MEMBEBANKAN HAK TANGGUNGAN'], ['05', 'CESSIE'], ['06', 'BELUM DIIKAT'], ['99', 'LAINNYA'],
    ];

    private const CONDITIONS = [
        ['1', 'TELAH DIGUNAKAN SEBAGAI FASUM/FASOS'], ['2', 'DALAM SENGKETA'], ['3', 'DISITA NEGARA'], ['4', 'TDK DIKETAHUI KEBERADAANNYA'],
        ['5', 'TDK MEMILIKI NILAI EKONOMIS LAGI'], ['6', 'TDK DAPAT DIEKSEKUSI'], ['9', 'TIDAK ADA MASALAH'],
    ];

    private const COLLATERAL_METHODS = [['0', 'DEFAULT'], ['1', 'NETTO'], ['2', 'GROSS'], ['3', 'FULL']];

    private const INSTITUTIONS = [
        ['001', 'KLINIK HAPPY HEALTY'], ['002', 'PT.KWANGLIMYHI'], ['003', 'PT.KWANGLIMYHI'], ['004', 'PT.KWANGLIMYHI'],
        ['005', 'PT.KWANGLIMYHI'], ['006', 'PT.TAEKWANG'], ['007', 'PT.SHEBAINDAH'], ['008', 'PT.PANPACIFICNESIA'],
        ['009', 'PT.C-SITETEXPIA'], ['10', 'PT.ABB'],
    ];

    /** Ownership evidence per collateral type (only type 05 is confirmed). */
    private const OWNERSHIP = [
        '05' => ['PEKARANGAN', 'SAWAH', 'KEBUN/HUTAN', 'RUMAHTINGGAL', 'RUMAHSUSUN', 'RUKO/RUKAN', 'HOTEL', 'GUDANG', 'GEDUNG', 'BANGUNAN', 'LAINNYA'],
    ];

    /**
     * Product parameters, mirroring the staging database of the previous SIPEBRI as dumped on 2026-10-01 (the company's
     * configured values): RC threshold is not set there (null), and KPN carries 17.04 % interest.
     * They are only created when missing, so values a Super Admin changes later are never reset by a re-seed or a deploy.
     * [product, min amount, max amount, min tenor, max tenor, interest %, provision %, admin %, RC % (null = not set), interest method codes, installment codes, collateral required]
     */
    private const PARAMETERS = [
        ['01', 1000000, 999999999999, 1, 300, 3.5, 0, 0, null, ['10'], ['3'], true],
        ['02', 1000000, 999999999999, 1, 60, 13, 0, 0, null, ['10'], ['3'], true],
        ['03', 1000000, 999999999999, 1, 36, 15, 0, 0, null, ['10'], ['3'], true],
        ['04', 1000000, 999999999999, 1, 36, 14, 0, 0, null, ['20'], ['8'], true],
        ['05', 10000000, 999999999999, 6, 60, 13, 0, 0, null, ['10'], ['3'], true],
        ['06', 1000000, 150000000, 6, 120, 10, 0, 0, null, ['22'], ['7'], true],
        ['07', 1000000, 100000000, 1, 36, 32, 0, 0, null, ['30'], ['3'], true],
        ['08', 1000000, 21000000, 6, 60, 13, 0, 0, null, ['10'], ['3'], true],
        ['09', 10000000, 100000000, 12, 60, 12, 0, 0, null, ['10'], ['8'], true],
        ['10', 1000000, 100000000, 1, 36, 25, 0, 0, null, ['10'], ['3'], true],
        ['11', 10000000, 100000000, 1, 3, 18, 0, 0, null, ['10'], ['3'], true],
        ['12', 1000000, 250000000, 1, 60, 17.04, 0, 0, null, ['10'], ['3'], true],
        ['13', 10000000, 100000000, 12, 36, 12, 0, 0, null, ['10'], ['8'], true],
        ['14', 2000000, 10000000, 3, 10, 32, 0, 0, null, ['10'], ['3'], false],
        ['15', 1000000, 100000000, 6, 18, 24, 0, 0, null, ['10'], ['3'], true],
        ['16', 2000000, 250000000, 6, 120, 14, 0, 0, null, ['10'], ['3'], true],
        ['17', 1000000, 3000000, 6, 12, 9, 0, 0, null, ['10'], ['2'], false],
    ];

    public function run(): void
    {
        foreach (self::OFFICES as [$code, $alias, $name]) {
            Office::updateOrCreate(['code' => $code], ['alias' => $alias, 'name' => $name]);
        }

        foreach (self::INSTITUTIONS as [$code, $name]) {
            Institution::updateOrCreate(['code' => $code], ['name' => $name]);
        }

        foreach (self::PRODUCTS as [$code, $alias, $name, $active]) {
            Product::updateOrCreate(['code' => $code], ['alias' => $alias, 'name' => $name, 'is_active' => $active]);
        }

        foreach (self::INSTALLMENTS as [$code, $name, $period]) {
            Installment::updateOrCreate(['code' => $code], ['name' => $name, 'period_months' => $period]);
        }

        foreach ([Method::class => self::METHODS, CollateralType::class => self::COLLATERAL_TYPES, BindingType::class => self::BINDING_TYPES,
            CollateralCondition::class => self::CONDITIONS, CollateralMethod::class => self::COLLATERAL_METHODS] as $model => $rows) {
            foreach ($rows as [$code, $name]) {
                $model::updateOrCreate(['code' => $code], ['name' => $name]);
            }
        }

        foreach (self::OWNERSHIP as $type => $names) {
            foreach ($names as $i => $name) {
                OwnershipStatus::updateOrCreate(['collateral_type_code' => $type, 'code' => str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)], ['name' => $name]);
            }
        }

        // Only created when missing: a value a Super Admin changed later is never reset by a re-seed or deploy.
        Setting::firstOrCreate(['key' => LendingLimit::KEY], ['value' => (string) LendingLimit::DEFAULT_BMPK]);

        $this->parameters();
        $this->regions();
    }

    private function parameters(): void
    {
        $products = Product::pluck('id', 'code');
        $methods = Method::pluck('id', 'code');
        $installments = Installment::pluck('id', 'code');

        foreach (self::PARAMETERS as [$product, $minAmount, $maxAmount, $minTenor, $maxTenor, $interest, $provision, $admin, $rc, $methodCodes, $installmentCodes, $collateral]) {
            if (! $products->has($product)) {
                continue;
            }

            ProductParameter::firstOrCreate(['product_id' => $products[$product]], [
                'min_amount' => $minAmount, 'max_amount' => $maxAmount, 'min_tenor' => $minTenor, 'max_tenor' => $maxTenor,
                'interest_rate' => $interest, 'provision_rate' => $provision, 'admin_rate' => $admin, 'rc_threshold' => $rc,
                'allowed_method_ids' => array_map(fn (string $c) => $methods[$c], $methodCodes),
                'allowed_installment_ids' => array_map(fn (string $c) => $installments[$c], $installmentCodes),
                'default_method_id' => $methods[$methodCodes[0]],
                'default_installment_id' => $installments[$installmentCodes[0]],
                'collateral_required' => $collateral,
            ]);
        }
    }

    /** ~82k villages from database/data/regions.csv.gz, inserted once. */
    private function regions(): void
    {
        if (DB::table('regions')->exists()) {
            return;
        }

        $handle = gzopen(database_path('data/regions.csv.gz'), 'rb') ?: throw new \RuntimeException('Cannot read database/data/regions.csv.gz');
        gzgets($handle);
        $now = now();
        $buffer = [];

        while (($line = gzgets($handle)) !== false) {
            $row = str_getcsv(trim($line));

            if (count($row) < 4 || $row[0] === '') {
                continue;
            }

            $buffer[] = ['code' => $row[0], 'regency' => $row[1], 'district' => $row[2], 'village' => $row[3], 'postal_code' => $row[4] ?? null, 'created_at' => $now, 'updated_at' => $now];

            if (count($buffer) === 1000) {
                DB::table('regions')->insert($buffer);
                $buffer = [];
            }
        }

        if ($buffer !== []) {
            DB::table('regions')->insert($buffer);
        }

        gzclose($handle);
    }
}
