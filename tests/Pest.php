<?php

use App\Enums\LoanStatus;
use App\Enums\RoleName;
use App\Models\Collateral;
use App\Models\CommitteePath;
use App\Models\Installment;
use App\Models\LoanApplication;
use App\Models\Method;
use App\Models\Office;
use App\Models\Product;
use App\Models\ProductParameter;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/**
 * A user holding the Super Admin role (every permission).
 */
function superAdmin(): User
{
    test()->seed([PermissionSeeder::class, RoleSeeder::class]);

    return User::factory()->create()->assignRole(RoleName::SuperAdmin->value);
}

/**
 * A user whose role holds exactly the given permissions.
 *
 * @param  list<string>  $permissions
 */
function userWith(array $permissions, string $role = 'Tester'): User
{
    test()->seed(PermissionSeeder::class);

    return User::factory()->create()
        ->assignRole(Role::findOrCreate($role, 'web')->syncPermissions($permissions));
}

/**
 * Same shape as the real Codex /api/web-auth response (device data is ignored).
 *
 * @param  array<string, mixed>  $user
 */
function fakeCodex(array $user = [], int $status = 200): void
{
    // A fresh factory, otherwise the stubs of an earlier call inside the same test would win.
    Http::swap(new Factory);
    Http::fake(['codex.test/*' => Http::response([
        'success' => true,
        'data' => [
            'user' => [
                'id' => 30, 'name' => 'Test Person', 'username' => '900000030', 'email' => 'person@example.com',
                'birthday' => '1998-01-07', 'role' => 'Kepala Seksi Analis', 'office' => 'Pamanukan', 'alias' => 'TPE', 'mso_code' => null, 'collector_code' => null,
                'is_active' => true, ...$user,
            ],
            'office' => ['code' => '00', 'alias' => 'PMK', 'name' => 'Pamanukan', 'address' => 'Somewhere'],
            'device' => ['device_identifier' => 'X', 'fmc_token' => 'ignored'],
        ],
    ], $status)]);
}

/**
 * A Codex customer master that knows the given people (national ID => full name); every other ID is unregistered.
 *
 * @param  array<string, string>  $customers
 */
function fakeCustomers(array $customers = ['3201000000000001' => 'Siti Aminah']): void
{
    config(['services.codex.endpoint' => 'https://codex.test', 'services.codex.id' => 'id', 'services.codex.secret' => 'secret']);
    Http::swap(new Factory);
    Http::fake([
        'codex.test/oauth/token' => Http::response(['access_token' => 'tok', 'expires_in' => 3600]),
        'codex.test/api/customers/*' => function ($request) use ($customers) {
            $nik = basename($request->url());

            return isset($customers[$nik])
                ? Http::response(['data' => ['nomor_ktp' => $nik, 'nama_lengkap' => $customers[$nik], 'jenis_kelamin' => 'P', 'nomor_cif' => 'C'.substr($nik, -4)]])
                : Http::response([], 404);
        },
    ]);
}

/**
 * A product with limits, its committee path, an office and a section head.
 *
 * @return array<string, mixed>
 */
function loanSetup(bool $collateralRequired = false): array
{
    test()->seed(RoleSeeder::class);

    $method = Method::create(['code' => '10', 'name' => 'FLAT']);
    $other = Method::create(['code' => '20', 'name' => 'EFFECTIVE']);
    $installment = Installment::create(['code' => '3', 'name' => 'MONTHLY', 'period_months' => 1]);
    $product = Product::create(['code' => '01', 'alias' => 'KRU', 'name' => 'GENERAL', 'is_active' => true]);
    ProductParameter::create([
        'product_id' => $product->id, 'min_amount' => 1_000_000, 'max_amount' => 50_000_000, 'min_tenor' => 1, 'max_tenor' => 24,
        'allowed_method_ids' => [$method->id], 'allowed_installment_ids' => [$installment->id], 'collateral_required' => $collateralRequired,
    ]);

    return [
        'product' => $product, 'method' => $method, 'otherMethod' => $other, 'installment' => $installment,
        'path' => CommitteePath::create(['product_id' => $product->id, 'mechanism' => 'plafon']),
        'office' => Office::create(['code' => '00', 'alias' => 'PMK', 'name' => 'Pamanukan']),
        'supervisor' => User::factory()->create()->assignRole('Kepala Seksi Analis'),
    ];
}

/**
 * @param  array<string, mixed>  $s
 * @return array<string, mixed>
 */
function loanPayload(array $s, array $overrides = []): array
{
    return array_merge([
        'application_date' => now()->toDateString(), 'product_id' => $s['product']->id, 'committee_path_id' => $s['path']->id,
        'requested_amount' => 10_000_000, 'requested_tenor' => 12, 'method_id' => $s['method']->id, 'installment_id' => $s['installment']->id,
        'interest_rate' => 13, 'usage_type' => 'WORKING CAPITAL', 'office_id' => $s['office']->id, 'supervisor_id' => $s['supervisor']->id,
        'marketing' => 'adi',
    ], $overrides);
}

function loanOfficer(): User
{
    return userWith(['loan-applications.view', 'loan-applications.manage', 'collaterals.view', 'collaterals.manage'], 'AO Test');
}

/**
 * A scheduled file with its surveyor and one collateral.
 *
 * @return array{0: User, 1: LoanApplication, 2: Collateral}
 */
function surveyFile(): array
{
    test()->seed(RoleSeeder::class);
    $analyst = User::factory()->create()->assignRole('Staff Analis & Appraisal');
    $loan = LoanApplication::create([
        'application_code' => '00700001', 'application_date' => now(), 'status' => LoanStatus::Scheduling->value, 'nik' => '3201000000000001',
        'full_name' => 'Siti', 'surveyor_id' => $analyst->id, 'survey_date' => now()->toDateString(), 'created_by' => $analyst->id,
    ]);
    $collateral = Collateral::create(['collateral_type_code' => '05', 'owner_name' => 'ONIH', 'document_number' => 'SHM 1']);
    $loan->collaterals()->attach($collateral->id);

    return [$analyst, $loan, $collateral];
}

/** The named attributes of a model, in the order asked. */
function fieldsOf(Model $model, array $keys): array
{
    return collect($keys)->mapWithKeys(fn (string $key): array => [$key => $model->getAttribute($key)])->all();
}
