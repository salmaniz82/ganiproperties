<?php
namespace Tests\Feature;

use App\Models\{Category, Media, Page, Product, ProductVariant, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCatalogManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    public function test_admin_can_manage_nested_categories(): void
    {
        $admin = $this->admin();
        $parent = Category::create(['name' => 'Events', 'slug' => 'events']);

        $this->actingAs($admin)->post('/dashboard/categories', [
            'name' => 'Birthdays', 'slug' => 'birthdays', 'parent_id' => $parent->id, 'position' => 1, 'is_active' => 1,
        ])->assertRedirect('/dashboard/categories');

        $child = Category::where('slug', 'birthdays')->firstOrFail();
        $this->actingAs($admin)->get('/dashboard/categories')->assertOk()->assertSee('Level 2');
        $this->actingAs($admin)->put("/dashboard/categories/{$child->id}", [
            'name' => 'Birthday Parties', 'slug' => 'birthday-parties', 'parent_id' => $parent->id, 'position' => 2, 'is_active' => 1,
        ])->assertRedirect();
        $this->assertDatabaseHas('categories', ['id' => $child->id, 'name' => 'Birthday Parties', 'parent_id' => $parent->id]);
    }

    public function test_product_creation_and_editing_use_dedicated_routes(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->get('/dashboard/products')->assertOk()->assertSee('/dashboard/products/create', false)->assertDontSee('Add product');
        $this->actingAs($admin)->get('/dashboard/products/create')->assertOk()->assertSee('Create product');

        $this->actingAs($admin)->post('/dashboard/products', [
            'name' => 'Confetti Set', 'slug' => 'confetti-set', 'sku' => 'CONF-1', 'price' => 1200, 'stock' => 10, 'is_active' => 1, 'track_inventory' => 1,
            'meta_description' => 'A colorful confetti set.', 'meta_keywords' => 'confetti, parties', 'seo_schema' => '{"@context":"https://schema.org","@type":"Product"}',
            'image' => 'storage/media/2026/06/hero.jpg', 'gallery_images' => ['storage/media/2026/06/one.jpg', 'storage/media/2026/06/two.jpg'],
        ])->assertRedirect();
        $product = Product::where('sku', 'CONF-1')->firstOrFail();
        $this->assertSame(['storage/media/2026/06/one.jpg', 'storage/media/2026/06/two.jpg'], $product->gallery_images);
        $this->actingAs($admin)->get("/dashboard/products/{$product->id}/edit")->assertOk()->assertSee('Confetti Set')->assertSee('Gallery images');
        $this->get("/products/{$product->slug}")->assertOk()->assertSee('name="description"', false)->assertSee('application/ld+json', false);
    }

    public function test_admin_can_upload_and_update_media(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->post('/dashboard/media', [
            'files' => [UploadedFile::fake()->image('balloons.jpg')],
        ])->assertRedirect();

        $media = Media::where('filename', 'balloons.jpg')->latest('id')->firstOrFail();
        Storage::disk('public')->assertExists($media->path);
        $this->assertNotNull($media->webp_path);
        $this->assertNotNull($media->thumbnail_path);
        Storage::disk('public')->assertExists($media->webp_path);
        Storage::disk('public')->assertExists($media->thumbnail_path);
        $this->assertSame('storage/'.$media->thumbnail_path, Media::variantAssetPath($media->asset_path, 'thumbnail'));
        $this->actingAs($admin)->put("/dashboard/media/{$media->id}", ['title' => 'Blue balloons', 'alt_text' => 'Blue balloons'])->assertRedirect();
        $this->assertDatabaseHas('media', ['id' => $media->id, 'title' => 'Blue balloons']);
    }

    public function test_admin_can_disable_webp_while_still_generating_a_thumbnail(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post('/dashboard/media', [
            'files' => [UploadedFile::fake()->image('original.jpg', 900, 600)],
            'optimize_webp' => 0,
        ])->assertRedirect();

        $media = Media::where('filename', 'original.jpg')->latest('id')->firstOrFail();
        $this->assertNull($media->webp_path);
        $this->assertStringEndsWith('-thumb.jpg', $media->thumbnail_path);
        Storage::disk('public')->assertExists($media->path);
        Storage::disk('public')->assertExists($media->thumbnail_path);
    }

    public function test_admin_can_create_variable_product_with_generated_variants(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/dashboard/products', [
            'product_type' => 'variable',
            'name' => 'Balloon Bundle',
            'slug' => 'balloon-bundle',
            'sku' => 'BUNDLE',
            'price' => 1500,
            'discount_price' => 1300,
            'stock' => 0,
            'track_inventory' => 1,
            'variant_same_pricing' => 1,
            'is_active' => 1,
            'variant_options' => [
                ['name' => 'Color', 'values' => 'Red, Blue'],
                ['name' => 'Size', 'values' => 'Small, Large'],
            ],
            'variants' => [
                ['name' => 'Red / Small', 'sku' => 'BUNDLE-RED-SMALL', 'price' => 1500, 'discount_price' => 1300, 'stock' => 5, 'options' => '{"Color":"Red","Size":"Small"}'],
                ['name' => 'Blue / Large', 'sku' => 'BUNDLE-BLUE-LARGE', 'price' => 1500, 'discount_price' => 1300, 'stock' => 3, 'options' => '{"Color":"Blue","Size":"Large"}'],
            ],
        ])->assertRedirect();

        $product = Product::where('sku', 'BUNDLE')->firstOrFail();
        $this->assertSame('variable', $product->product_type);
        $this->assertTrue($product->variant_same_pricing);
        $this->assertCount(2, $product->variant_options);
        $this->assertDatabaseHas('product_variants', ['product_id' => $product->id, 'sku' => 'BUNDLE-RED-SMALL', 'stock' => 5, 'discount_price' => 1300]);
        $this->assertEquals(['Color' => 'Red', 'Size' => 'Small'], ProductVariant::where('sku', 'BUNDLE-RED-SMALL')->firstOrFail()->options);
    }

    public function test_variant_attribute_values_are_normalized_from_comma_lists(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/dashboard/products', [
            'product_type' => 'variable',
            'name' => 'Party Hat',
            'slug' => 'party-hat',
            'sku' => 'HAT',
            'price' => 500,
            'stock' => 0,
            'is_active' => 1,
            'variant_options' => [
                ['name' => 'Color', 'values' => 'Red, Green, Blue'],
                ['name' => 'Size', 'values' => 'Small, Medium, Large'],
            ],
            'variants' => [
                ['name' => 'Red / Small', 'sku' => 'HAT-RED-SMALL', 'price' => 500, 'stock' => 1, 'options' => '{"Color":"Red","Size":"Small"}'],
            ],
        ])->assertRedirect();

        $product = Product::where('sku', 'HAT')->firstOrFail();
        $this->assertSame('Red, Green, Blue', $product->variant_options[0]['values']);
        $this->assertSame('Small, Medium, Large', $product->variant_options[1]['values']);
    }

    public function test_admin_script_is_valid_javascript(): void
    {
        $script = public_path('js/admin.js');
        $result = null;
        $output = [];
        exec('node --check '.escapeshellarg($script).' 2>&1', $output, $result);

        $this->assertSame(0, $result, implode("\n", $output));
    }

    public function test_admin_can_manage_pages(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get('/dashboard/pages')->assertOk()->assertSee('Pages');
        $this->actingAs($admin)->post('/dashboard/pages', [
            'title'=>'About Us',
            'slug'=>'about',
            'content'=>'Dummy about content.',
            'meta_title'=>'About | Party Poppers',
            'meta_keywords'=>'about, party poppers',
            'schema'=>'{"@context":"https://schema.org","@type":"AboutPage"}',
            'position'=>1,
            'is_active'=>1,
        ])->assertRedirect();

        $page = Page::where('slug','about')->firstOrFail();
        $this->assertSame('AboutPage', $page->schema['@type']);
        $this->actingAs($admin)->get("/dashboard/pages/{$page->id}/edit")->assertOk()->assertSee('Dummy about content.');
    }
}
