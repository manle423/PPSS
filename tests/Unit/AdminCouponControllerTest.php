<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CouponsImport;
use App\Models\User;

class AdminCouponControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'role' => 'ADMIN',
        ]);
        $this->actingAs($this->user);
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
}
