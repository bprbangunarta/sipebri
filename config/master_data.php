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
| Registry of the lookup tables managed under Data Master / Credit Setup. One controller, one page
| and one permission pair (`<slug>.view` / `<slug>.manage`) serve every entry.
|
|  section – sidebar group ("Data Master" or "Credit Setup"); group – heading inside that group.
|  usage   – relations that reference a record; deleting a record in use is refused.
|  fields  – columns, in table order. Keys: name, label, type (text|number|select|boolean), required,
|            unique (true, or a sibling column the value is unique within), uppercase, max, min, hint,
|            default, empty (label for null), hide_below (hide on small screens), options_from
|            ([model, value, label columns]), rules (extra validation rules).
|  actions – extra row actions: label, url (with {id}).
*/

$name = ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true, 'unique' => true, 'max' => 100];
$code = ['name' => 'code', 'label' => 'Code', 'type' => 'text', 'required' => true, 'unique' => true, 'max' => 8, 'uppercase' => true];
$label = ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true, 'max' => 100, 'uppercase' => true];

return [
    'regions' => [
        'label' => 'Region', 'section' => 'Data Master', 'group' => 'Location', 'model' => Region::class, 'usage' => [],
        'fields' => [
            ['name' => 'code', 'label' => 'Code', 'type' => 'text', 'required' => true, 'max' => 8, 'uppercase' => true, 'hint' => 'Regency code (dati2) sent to core banking.'],
            ['name' => 'regency', 'label' => 'Regency', 'type' => 'text', 'required' => true, 'max' => 100, 'uppercase' => true],
            ['name' => 'district', 'label' => 'District', 'type' => 'text', 'required' => true, 'max' => 100, 'uppercase' => true],
            ['name' => 'village', 'label' => 'Village', 'type' => 'text', 'required' => true, 'max' => 100, 'uppercase' => true],
            ['name' => 'postal_code', 'label' => 'Postal code', 'type' => 'text', 'max' => 8, 'hide_below' => 'md'],
        ],
    ],

    'offices' => [
        'label' => 'Office', 'section' => 'Credit Setup', 'group' => 'Organization', 'model' => Office::class, 'usage' => ['loanApplications' => 'loan application', 'users' => 'user'],
        'fields' => [$code, ['name' => 'alias', 'label' => 'Alias', 'type' => 'text', 'required' => true, 'unique' => true, 'max' => 8, 'uppercase' => true], $label],
    ],
    'institutions' => ['label' => 'Institution', 'section' => 'Credit Setup', 'group' => 'Organization', 'model' => Institution::class, 'usage' => ['loanApplications' => 'loan application'], 'fields' => [$code, $label]],
    'products' => [
        'label' => 'Product', 'section' => 'Credit Setup', 'group' => 'Loan terms', 'model' => Product::class, 'usage' => ['loanApplications' => 'loan application', 'committeePaths' => 'committee path'],
        'fields' => [
            $code,
            ['name' => 'alias', 'label' => 'Alias', 'type' => 'text', 'required' => true, 'unique' => true, 'max' => 12, 'uppercase' => true],
            $label,
            ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean', 'default' => true, 'hint' => 'Inactive products cannot be chosen for new loan applications.'],
        ],
        'actions' => [['label' => 'Set parameters', 'url' => '/master-data/products/{id}/parameters']],
    ],
    'installments' => [
        'label' => 'Installment System', 'section' => 'Credit Setup', 'group' => 'Loan terms', 'model' => Installment::class, 'usage' => ['loanApplications' => 'loan application'],
        'fields' => [
            $code, $label,
            ['name' => 'period_months', 'label' => 'Period (months)', 'type' => 'number', 'required' => true, 'min' => 0, 'max' => 120, 'default' => 1, 'hint' => 'Tenor multiple in months; 0 = no installments.'],
        ],
    ],
    'methods' => ['label' => 'Interest Method', 'section' => 'Credit Setup', 'group' => 'Loan terms', 'model' => Method::class, 'usage' => ['loanApplications' => 'loan application'], 'fields' => [$code, $label]],
    'collateral-types' => ['label' => 'Collateral Type', 'section' => 'Credit Setup', 'group' => 'Collateral', 'model' => CollateralType::class, 'usage' => ['collaterals' => 'collateral', 'ownershipStatuses' => 'ownership status'], 'fields' => [$code, $label]],
    'collateral-bindings' => ['label' => 'Binding Type', 'section' => 'Credit Setup', 'group' => 'Collateral', 'model' => BindingType::class, 'usage' => ['collaterals' => 'collateral'], 'fields' => [$code, $label]],
    'collateral-conditions' => ['label' => 'Collateral Condition', 'section' => 'Credit Setup', 'group' => 'Collateral', 'model' => CollateralCondition::class, 'usage' => ['collaterals' => 'collateral'], 'fields' => [$code, $label]],
    'collateral-methods' => ['label' => 'Valuation Method', 'section' => 'Credit Setup', 'group' => 'Collateral', 'model' => CollateralMethod::class, 'usage' => [], 'fields' => [$code, $label]],
    'ownership-statuses' => [
        'label' => 'Ownership Status', 'section' => 'Credit Setup', 'group' => 'Collateral', 'model' => OwnershipStatus::class, 'usage' => [],
        'fields' => [
            ['name' => 'collateral_type_code', 'label' => 'Collateral type', 'type' => 'select', 'required' => true, 'options_from' => [CollateralType::class, 'code', ['code', 'name']]],
            ['name' => 'code', 'label' => 'Code', 'type' => 'text', 'required' => true, 'unique' => 'collateral_type_code', 'max' => 8, 'uppercase' => true],
            $label,
        ],
    ],
];
