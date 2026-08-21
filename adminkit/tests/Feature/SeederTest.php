<?php

namespace Tests\Feature;

use App\Models\BindingType;
use App\Models\CollateralCondition;
use App\Models\CollateralMethod;
use App\Models\CollateralType;
use App\Models\Installment;
use App\Models\Institution;
use App\Models\Method;
use App\Models\Office;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/** Data awal (seeder) harus utuh dan aman dijalankan berulang kali. */
class SeederTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_jumlah_data_referensi_sesuai_cetakan(): void
    {
        $this->assertSame(22, User::count());
        $this->assertSame(45, Role::count());
        $this->assertSame(7, Office::count());
        $this->assertSame(10, Institution::count());
        $this->assertSame(17, Product::count());
        $this->assertSame(8, Installment::count());
        $this->assertSame(10, Method::count());
        $this->assertSame(19, CollateralType::count());
        $this->assertSame(7, BindingType::count());
        $this->assertSame(7, CollateralCondition::count());
        $this->assertSame(4, CollateralMethod::count());
        $this->assertSame(82449, DB::table('regions')->count());
        $this->assertSame(11, DB::table('ownership_statuses')->count());
        $this->assertSame(0, DB::table('collateral_simulations')->count());
        $this->assertSame(0, DB::table('loan_applications')->count());
        $this->assertSame(29, DB::table('menus')->count());
        $this->assertSame(
            DB::table('products')->count(),
            DB::table('product_parameters')->count(),
            'setiap produk wajib punya parameter',
        );
        $this->assertNotSame(0, Permission::count());
    }

    public function test_setiap_pengguna_punya_tepat_satu_peranan(): void
    {
        $this->assertSame(User::count(), DB::table('model_has_roles')->count());
        $this->assertTrue(
            User::all()->every(fn (User $u) => $u->roles->count() === 1 && $u->role === $u->roles->first()->name),
        );
    }

    public function test_kata_sandi_bawaan_pegawai_dan_super_admin(): void
    {
        $admin = User::where('username', 'superadmin')->firstOrFail();
        $this->assertTrue(Hash::check('SA@4dm1n', $admin->password));

        $staff = User::where('username', '!=', 'superadmin')->get();
        $this->assertCount(21, $staff);
        $this->assertTrue($staff->every(fn (User $u) => Hash::check('password', $u->password)));
    }

    public function test_kantor_pengguna_mengacu_data_kantor(): void
    {
        $names = Office::pluck('name');

        // Hanya akun pemilik sistem yang berkantor di luar referensi (Kantor Pusat).
        $this->assertSame(
            ['superadmin'],
            User::whereNotIn('office', $names)->pluck('username')->all(),
        );
    }

    public function test_seeding_ulang_tidak_menggandakan_atau_mengubah_kata_sandi(): void
    {
        $staff = User::where('username', '350010923')->firstOrFail();
        $staff->forceFill(['password' => Hash::make('KataSandiBaru123')])->save();

        Artisan::call('db:seed', ['--force' => true]);

        $this->assertSame(22, User::count());
        $this->assertSame(45, Role::count());
        $this->assertSame(7, Office::count());
        $this->assertSame(17, Product::count());
        $this->assertTrue(Hash::check('KataSandiBaru123', $staff->fresh()->password));
    }

    public function test_akun_terarsip_dipulihkan_oleh_seeder(): void
    {
        $staff = User::where('username', '350010923')->firstOrFail();
        $staff->delete();
        $this->assertTrue($staff->fresh()->trashed());

        Artisan::call('db:seed', ['--class' => 'Database\Seeders\UserSeeder', '--force' => true]);

        $this->assertFalse(User::withTrashed()->find($staff->id)->trashed());
    }

    public function test_setiap_menu_bawaan_mengarah_ke_rute_yang_hidup(): void
    {
        $admin = User::where('username', 'superadmin')->firstOrFail();
        $menus = DB::table('menus')->whereNotNull('href')->get();

        $this->assertSame(26, $menus->count());

        foreach ($menus as $menu) {
            $this->actingAs($admin)->get($menu->href)
                ->assertOk("Menu {$menu->label} ({$menu->href}) tidak dapat dibuka.");

            if ($menu->permission) {
                $this->assertTrue(
                    Permission::where('name', $menu->permission)->exists(),
                    "Izin {$menu->permission} untuk menu {$menu->label} belum terdaftar.",
                );
            }
        }
    }

    public function test_login_memakai_kata_sandi_bawaan(): void
    {
        $staff = User::where('username', '350010923')->firstOrFail();

        $this->post('/login', ['credential' => $staff->username, 'password' => 'password'])
            ->assertRedirect();

        $this->assertAuthenticatedAs($staff);
    }
}
