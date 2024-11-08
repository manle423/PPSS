<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class AdminCategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User | Authenticatable $user;
    protected User | Authenticatable $buyer;

    protected function setUp(): void
    {
        parent::setUp();
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
        $this->get(route('admin.category.list'));
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

    // Test list categories with search
    public function testListCategoriesWithSearch()
    {
        $category1 = Category::factory()->create(['name' => 'Test Category']);
        $category2 = Category::factory()->create(['name' => 'Another Category']);
        
        $response = $this->get(route('admin.category.list', ['search' => 'Test']));
        
        $response->assertStatus(200);
        $response->assertViewHas('categories');
        $response->assertSee('Test Category');
        $response->assertDontSee('Another Category');
    }

    // Test store category validation
    public function testStoreCategoryValidation()
    {
        $response = $this->post(route('admin.category.store'), [
            'name' => '', // Empty name
            'description' => 'Test description',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    // Test store duplicate category
    public function testStoreDuplicateCategory()
    {
        Category::factory()->create(['name' => 'Existing Category']);

        $response = $this->post(route('admin.category.store'), [
            'name' => 'Existing Category',
            'description' => 'Test description',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['name']);
    }

    // Test update category validation
    public function testUpdateCategoryValidation()
    {
        $category = Category::factory()->create();

        $response = $this->post(route('admin.category.update', $category->id), [
            'name' => '', // Empty name
            'description' => 'Updated description',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    // Test delete non-existent category
    public function testDeleteNonExistentCategory()
    {
        $response = $this->post(route('admin.category.delete', 999));

        $response->assertRedirect(route('admin.category.list'));
        $response->assertSessionHas('error', 'Category not found.');
    }

    // Test bulk action with no categories selected
    public function testBulkActionNoSelection()
    {
        $response = $this->post(route('admin.category.bulk-action'), [
            'action' => 'delete',
            'ids' => [],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'No categories selected.');
    }

    // Test bulk action with invalid action
    public function testBulkActionInvalidAction()
    {
        $categories = Category::factory()->count(2)->create();

        $response = $this->post(route('admin.category.bulk-action'), [
            'action' => 'invalid_action',
            'ids' => $categories->pluck('id')->toArray(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Invalid action.');
    }

    // Test import categories
    public function testImportCategories()
    {
        Storage::fake('local');
        
        $file = UploadedFile::fake()->create('categories.xlsx');

        $response = $this->post(route('admin.category.import'), [
            'file' => $file
        ]);

        $response->assertRedirect();
    }

    // Test import categories with invalid file
    public function testImportCategoriesInvalidFile()
    {
        Storage::fake('local');
        
        $file = UploadedFile::fake()->create('categories.txt');

        $response = $this->post(route('admin.category.import'), [
            'file' => $file
        ]);

        $response->assertSessionHasErrors(['file']);
    }

    // Test unauthorized access
    public function testUnauthorizedAccess()
    {
        $this->actingAs($this->buyer);

        $response = $this->get(route('admin.category.list'));
        $response->assertStatus(302);
    }

    // Test pagination
    public function testCategoryListPagination()
    {
        Category::factory()->count(15)->create();

        $response = $this->get(route('admin.category.list'));
        
        $response->assertStatus(200);
        $response->assertViewHas('categories');
        
        $categories = $response->original->getData()['categories'];
        $this->assertEquals(10, $categories->perPage());
        $this->assertEquals(2, $categories->lastPage());
    }
}