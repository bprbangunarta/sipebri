<?php

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
use App\Models\Region;

/*
| Registry of the lookup tables managed under Referensi (Data Kantor, Data Resort, Data Kredit, Data Agunan, ...). One controller, one page
| and one permission pair (`<slug>.view` / `<slug>.manage`) serve every entry.
|
|  section – section shown under the page title ("Referensi"); group – heading inside that group.
|  usage   – relations that reference a record; deleting a record in use is refused.
|  fields  – columns, in table order. Keys: name, label, type (text|number|select|boolean), required,
|            unique (true, or a sibling column the value is unique within), uppercase, max, min, hint,
|            default, empty (label for null), hide_below (hide on small screens), options_from
|            ([model, value, label columns]), rules (extra validation rules).
|  actions – extra row actions: label, url (with {id}).
*/

$name = ['name' => 'name', 'label' => 'Nama', 'type' => 'text', 'required' => true, 'unique' => true, 'max' => 100];
$code = ['name' => 'code', 'label' => 'Kode', 'type' => 'text', 'required' => true, 'unique' => true, 'max' => 8, 'uppercase' => true];
$label = ['name' => 'name', 'label' => 'Nama', 'type' => 'text', 'required' => true, 'max' => 100, 'uppercase' => true];

return [
    'regions' => [
        'label' => 'Wilayah', 'section' => 'Referensi', 'group' => 'Lokasi', 'model' => Region::class, 'usage' => [],
        'fields' => [
            ['name' => 'code', 'label' => 'Kode', 'type' => 'text', 'required' => true, 'max' => 8, 'uppercase' => true, 'hint' => 'Kode kabupaten/kota (dati2) yang dikirim ke core banking.'],
            ['name' => 'regency', 'label' => 'Kabupaten/Kota', 'type' => 'text', 'required' => true, 'max' => 100, 'uppercase' => true],
            ['name' => 'district', 'label' => 'Kecamatan', 'type' => 'text', 'required' => true, 'max' => 100, 'uppercase' => true],
            ['name' => 'village', 'label' => 'Desa/Kelurahan', 'type' => 'text', 'required' => true, 'max' => 100, 'uppercase' => true],
            ['name' => 'postal_code', 'label' => 'Kode pos', 'type' => 'text', 'max' => 8, 'hide_below' => 'md'],
        ],
    ],

    'offices' => [
        'label' => 'Kantor', 'section' => 'Referensi', 'group' => 'Organisasi', 'model' => Office::class, 'usage' => ['loanApplications' => 'pengajuan kredit', 'users' => 'pengguna'],
        'fields' => [$code, ['name' => 'alias', 'label' => 'Alias', 'type' => 'text', 'required' => true, 'unique' => true, 'max' => 8, 'uppercase' => true], $label],
    ],
    'resorts' => ['label' => 'Resort', 'section' => 'Referensi', 'group' => 'Organisasi', 'model' => Institution::class, 'usage' => ['loanApplications' => 'pengajuan kredit'], 'fields' => [$code, $label]],
    'products' => [
        'label' => 'Produk', 'section' => 'Referensi', 'group' => 'Ketentuan kredit', 'model' => Product::class, 'usage' => ['loanApplications' => 'pengajuan kredit', 'committeePaths' => 'jalur komite'],
        'fields' => [
            $code,
            ['name' => 'alias', 'label' => 'Alias', 'type' => 'text', 'required' => true, 'unique' => true, 'max' => 12, 'uppercase' => true],
            $label,
            ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'boolean', 'default' => true, 'hint' => 'Produk nonaktif tidak bisa dipilih untuk pengajuan kredit baru.'],
        ],
        'actions' => [['label' => 'Atur parameter', 'url' => '/references/products/{id}/parameters']],
    ],
    'installments' => [
        'label' => 'Sistem Angsuran', 'section' => 'Referensi', 'group' => 'Ketentuan kredit', 'model' => Installment::class, 'usage' => ['loanApplications' => 'pengajuan kredit'],
        'fields' => [
            $code, $label,
            ['name' => 'period_months', 'label' => 'Periode (bulan)', 'type' => 'number', 'required' => true, 'min' => 0, 'max' => 120, 'default' => 1, 'hint' => 'Kelipatan tenor dalam bulan; 0 = tanpa angsuran.'],
        ],
    ],
    'interest-methods' => ['label' => 'Metode Bunga', 'section' => 'Referensi', 'group' => 'Ketentuan kredit', 'model' => Method::class, 'usage' => ['loanApplications' => 'pengajuan kredit'], 'fields' => [$code, $label]],
    'collateral-types' => ['label' => 'Jenis Agunan', 'section' => 'Referensi', 'group' => 'Agunan', 'model' => CollateralType::class, 'usage' => ['collaterals' => 'jaminan', 'ownershipStatuses' => 'klasifikasi agunan'], 'fields' => [$code, $label]],
    'collateral-bindings' => ['label' => 'Pengikatan Agunan', 'section' => 'Referensi', 'group' => 'Agunan', 'model' => BindingType::class, 'usage' => ['collaterals' => 'jaminan'], 'fields' => [$code, $label]],
    'collateral-conditions' => ['label' => 'Kondisi Agunan', 'section' => 'Referensi', 'group' => 'Agunan', 'model' => CollateralCondition::class, 'usage' => ['collaterals' => 'jaminan'], 'fields' => [$code, $label]],
    'collateral-valuations' => ['label' => 'Penilaian Agunan', 'section' => 'Referensi', 'group' => 'Agunan', 'model' => CollateralMethod::class, 'usage' => [], 'fields' => [$code, $label]],
    'collateral-classifications' => [
        'label' => 'Klasifikasi Agunan', 'section' => 'Referensi', 'group' => 'Agunan', 'model' => OwnershipStatus::class, 'usage' => [],
        'fields' => [
            ['name' => 'collateral_type_code', 'label' => 'Jenis agunan', 'type' => 'select', 'required' => true, 'options_from' => [CollateralType::class, 'code', ['code', 'name']]],
            ['name' => 'code', 'label' => 'Kode', 'type' => 'text', 'required' => true, 'unique' => 'collateral_type_code', 'max' => 8, 'uppercase' => true],
            $label,
        ],
    ],
];
