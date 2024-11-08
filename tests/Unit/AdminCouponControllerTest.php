<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CouponsImport;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Models\StoreInfo;

class AdminCouponControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User | Authenticatable $user;
    protected User | Authenticatable $buyer;
    protected function setUp(): void
    {
        parent::setUp();
        StoreInfo::factory()->create();
        $this->user = User::factory()->create([
            'role' => 'ADMIN',
        ]);
        $this->buyer = User::factory()->create([
            'role' => 'BUYER',
        ]);
        $this->actingAs($this->user);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testListCoupons()
    {
        Coupon::factory()->count(5)->create();

        $response = $this->get(route('admin.coupon.list'));

        $response->assertStatus(200);
        $response->assertViewHas('coupons');
    }

    public function testCreateCoupon()
    {
        $response = $this->post(route('admin.coupon.store'), [
            'code' => 'TESTCODE',
            'discount_value' => 0.1,
            'min_order_value' => 500000,
            'max_discount' => 50000,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(10)->toDateString(),
            'status' => 1,
        ]);

        $response->assertRedirect(route('admin.coupon.create'));
        $this->assertDatabaseHas('coupons', ['code' => 'TESTCODE']);
    }

    public function testUpdateCoupon()
    {
        $coupon = Coupon::factory()->create();

        $response = $this->post(route('admin.coupon.update', $coupon->id), [
            'code' => 'UPDATEDCODE',
            'discount_value' => 0.2,
            'min_order_value' => 600000,
            'max_discount' => 60000,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(15)->toDateString(),
            'status' => 1,
        ]);

        $response->assertRedirect(route('admin.coupon.list'));
        $this->assertDatabaseHas('coupons', ['code' => 'UPDATEDCODE']);
    }

    public function testDeleteCoupon()
    {
        $coupon = Coupon::factory()->create();

        $response = $this->post(route('admin.coupon.delete', $coupon->id));

        $response->assertRedirect(route('admin.coupon.list'));
        $this->assertSoftDeleted('coupons', ['id' => $coupon->id]);
    }

    public function testExportTemplate()
    {
        $response = $this->get(route('admin.coupon.export.template'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=coupons_template.xlsx');
    }

    public function testBulkActivateCoupons()
    {
        $coupons = Coupon::factory()->count(3)->create(['status' => 0]);

        $response = $this->post(route('admin.coupon.bulk-action'), [
            'action' => 'activate',
            'ids' => $coupons->pluck('id')->toArray(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Selected coupons activated successfully.');

        foreach ($coupons as $coupon) {
            $this->assertDatabaseHas('coupons', ['id' => $coupon->id, 'status' => 1]);
        }
    }

    public function testBulkDeactivateCoupons()
    {
        $coupons = Coupon::factory()->count(3)->create(['status' => 1]);

        $response = $this->post(route('admin.coupon.bulk-action'), [
            'action' => 'deactivate',
            'ids' => $coupons->pluck('id')->toArray(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Selected coupons deactivated successfully.');

        foreach ($coupons as $coupon) {
            $this->assertDatabaseHas('coupons', ['id' => $coupon->id, 'status' => 0]);
        }
    }

    public function testBulkDeleteCoupons()
    {
        $coupons = Coupon::factory()->count(3)->create();

        $response = $this->post(route('admin.coupon.bulk-action'), [
            'action' => 'delete',
            'ids' => $coupons->pluck('id')->toArray(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Selected coupons deleted successfully.');

        foreach ($coupons as $coupon) {
            $this->assertSoftDeleted('coupons', ['id' => $coupon->id]);
        }
    }

    public function testBulkActionWithNoIds()
    {
        $response = $this->post(route('admin.coupon.bulk-action'), [
            'action' => 'activate',
            'ids' => [],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'No coupons selected.');
    }

    public function testBulkActionWithInvalidAction()
    {
        $coupons = Coupon::factory()->count(3)->create();

        $response = $this->post(route('admin.coupon.bulk-action'), [
            'action' => 'invalid_action',
            'ids' => $coupons->pluck('id')->toArray(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Invalid action.');
    }

    public function testListCouponsWithSearch()
    {
        $coupon1 = Coupon::factory()->create(['code' => 'TEST123']);
        $coupon2 = Coupon::factory()->create(['code' => 'ANOTHER']);

        $response = $this->get(route('admin.coupon.list', ['search' => 'TEST']));

        $response->assertStatus(200);
        $response->assertSee('TEST123');
        $response->assertDontSee('ANOTHER');
    }

    public function testListCouponsWithStatusFilter()
    {
        $activeCoupon = Coupon::factory()->create(['status' => 1]);
        $inactiveCoupon = Coupon::factory()->create(['status' => 0]);

        $response = $this->get(route('admin.coupon.list', ['status' => 1]));

        $response->assertStatus(200);
        $response->assertViewHas('coupons', function ($coupons) use ($activeCoupon) {
            return $coupons->contains($activeCoupon)
                && $coupons->where('status', 0)->isEmpty();
        });
    }

    public function testAutoDeactivateExpiredCoupons()
    {
        $expiredCoupon = Coupon::factory()->create([
            'end_date' => now()->subDay(),
            'status' => 1
        ]);

        $this->get(route('admin.coupon.list'));

        $this->assertDatabaseHas('coupons', [
            'id' => $expiredCoupon->id,
            'status' => 0
        ]);
    }

    public function testCreateCouponValidation()
    {
        $response = $this->post(route('admin.coupon.store'), [
            'code' => '',
            'discount_value' => -1,
            'min_order_value' => -1,
            'max_discount' => -1,
            'start_date' => now()->toDateString(),
            'end_date' => now()->subDay()->toDateString(), // Invalid end date
            'status' => 'invalid'
        ]);

        $response->assertSessionHasErrors([
            'code',
            'discount_value',
            'min_order_value',
            'max_discount',
            'end_date',
            'status'
        ]);
    }

    public function testCreateDuplicateCoupon()
    {
        Coupon::factory()->create(['code' => 'DUPLICATE']);

        $response = $this->post(route('admin.coupon.store'), [
            'code' => 'DUPLICATE',
            'discount_value' => 0.1,
            'min_order_value' => 500000,
            'max_discount' => 50000,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(10)->toDateString(),
            'status' => 1,
        ]);

        $response->assertSessionHasErrors(['error' => 'This coupon already exists.']);
    }

    public function testUpdateCouponValidation()
    {
        $coupon = Coupon::factory()->create();

        $response = $this->post(route('admin.coupon.update', $coupon->id), [
            'code' => '',
            'discount_value' => -1,
            'min_order_value' => -1,
            'max_discount' => -1,
            'start_date' => now()->toDateString(),
            'end_date' => now()->subDay()->toDateString(),
            'status' => 'invalid'
        ]);

        $response->assertSessionHasErrors([
            'code',
            'discount_value',
            'min_order_value',
            'max_discount',
            'end_date',
            'status'
        ]);
    }

    public function testImportCouponsWithValidFile()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('coupons.xlsx');
        Excel::shouldReceive('import')
            ->once()
            ->andReturn(new CouponsImport());

        $response = $this->post(route('admin.coupon.import'), [
            'file' => $file
        ]);

        $response->assertRedirect(route('admin.coupon.list'));
        $response->assertSessionHas('success');
    }

    public function testImportCouponsWithInvalidFileType()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('coupons.txt');

        $response = $this->post(route('admin.coupon.import'), [
            'file' => $file
        ]);

        $response->assertSessionHasErrors(['file']);
    }

    public function testImportCouponsWithValidationErrors()
    {
        Storage::fake('local');
        
        $file = UploadedFile::fake()->create('coupons.xlsx');
        
        $validator = \Validator::make([], ['field' => 'required']);
        $validator->fails();
        $validationException = new \Illuminate\Validation\ValidationException($validator);
        
        Excel::shouldReceive('import')
            ->once()
            ->andThrow(new \Maatwebsite\Excel\Validators\ValidationException(
                $validationException, 
                []
            ));

        $response = $this->post(route('admin.coupon.import'), [
            'file' => $file
        ]);

        $response->assertSessionHas('error');
    }

    public function testDetailCoupon()
    {
        $coupon = Coupon::factory()->create();

        $response = $this->get(route('admin.coupon.detail', $coupon->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.coupons.show');
        $response->assertViewHas('coupon', $coupon);
    }

    public function testDetailNonExistentCoupon()
    {
        StoreInfo::factory()->create();
        
        $response = $this->get(route('admin.coupon.detail', 999));
        $response->assertStatus(404);
    }

    public function testUnauthorizedAccess()
    {
        StoreInfo::factory()->create();
        
        $this->actingAs($this->buyer);

        $response = $this->get(route('admin.coupon.list'));
        $response->assertRedirect(route('login'));
    }
}
