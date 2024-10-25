<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class AdminCategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'role' => 'ADMIN',
        ]);
        $this->actingAs($this->user);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testListCategories()
    {
        Category::factory()->count(5)->create();

        $response = $this->get(route('admin.category.list'));

        $response->assertStatus(200);
        $response->assertViewHas('categories');
    }

    public function testCreateCategory()
    {
        $response = $this->get(route('admin.category.create'));

        $response->assertStatus(200);
    }

    public function testStoreCategory()
    {
        $response = $this->post(route('admin.category.store'), [
            'name' => 'New Category',
            'description' => 'A new category description',
        ]);

        $response->assertRedirect(route('admin.category.create'));
        $this->assertDatabaseHas('categories', ['name' => 'New Category']);
    }

    public function testEditCategory()
    {
        $category = Category::factory()->create();

        $response = $this->get(route('admin.category.edit', $category->id));

        $response->assertStatus(200);
        $response->assertViewHas('category', $category);
    }

    public function testUpdateCategory()
    {
        $category = Category::factory()->create();

        $response = $this->post(route('admin.category.update', $category->id), [
            'name' => 'Updated Category',
            'description' => 'Updated description',
        ]);

        $response->assertRedirect(route('admin.category.list'));
        $this->assertDatabaseHas('categories', ['name' => 'Updated Category']);
    }

    public function testDeleteCategory()
    {
        $category = Category::factory()->create();

        $response = $this->post(route('admin.category.delete', $category->id));

        $response->assertRedirect(route('admin.category.list'));

        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }

    public function testExportTemplate()
    {
        $response = $this->get(route('admin.category.export.template'));

        $response->assertStatus(200);
    }

    public function testBulkAction()
    {
        $categories = Category::factory()->count(3)->create();

        $response = $this->post(route('admin.category.bulk-action'), [
            'action' => 'delete',
            'ids' => $categories->pluck('id')->toArray(),
        ]);

        $response->assertRedirect(route('admin.category.list'));
        foreach ($categories as $category) {
            $this->assertSoftDeleted('categories', ['id' => $category->id]);
        }
    }
}
