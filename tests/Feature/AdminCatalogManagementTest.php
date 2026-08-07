<?php
namespace Tests\Feature;

use App\Models\{Media, Page, Property, User};
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

    public function test_dashboard_is_property_only_and_public_link_opens_new_tab(): void
    {
        $response = $this->actingAs($this->admin())->get('/dashboard');

        $response->assertOk()
            ->assertSee('View public')
            ->assertSee('target="_blank"', false)
            ->assertDontSee('>Products<', false)
            ->assertDontSee('>Categories<', false)
            ->assertDontSee('>Orders<', false)
            ->assertDontSee('>Customers<', false)
            ->assertDontSee('>Delivery zones<', false);
        $this->actingAs($this->admin())->get('/dashboard/products')->assertNotFound();
        $this->actingAs($this->admin())->get('/dashboard/categories')->assertNotFound();
        $this->actingAs($this->admin())->get('/dashboard/orders')->assertNotFound();
        $this->actingAs($this->admin())->get('/dashboard/customers')->assertNotFound();
        $this->actingAs($this->admin())->get('/dashboard/delivery-zones')->assertNotFound();
        $this->get('/register')->assertNotFound();
    }

    public function test_admin_can_manage_property_listings_and_media(): void
    {
        $admin = $this->admin();
        $featured = Media::create(['user_id' => $admin->id, 'disk' => 'public', 'path' => 'media/home.jpg', 'filename' => 'home.jpg', 'mime_type' => 'image/jpeg', 'size' => 1200]);
        $gallery = Media::create(['user_id' => $admin->id, 'disk' => 'public', 'path' => 'media/kitchen.jpg', 'filename' => 'kitchen.jpg', 'mime_type' => 'image/jpeg', 'size' => 1200]);

        $this->actingAs($admin)->get('/dashboard/properties/create')->assertOk()
            ->assertSee('Create property')->assertSee('This is a commercial property')
            ->assertSee('featured_image_upload', false)->assertSee('gallery_image_uploads[]', false)
            ->assertDontSee('Media library');
        $this->actingAs($admin)->post('/dashboard/properties', [
            'title' => 'Test Mews', 'slug' => 'test-mews', 'reference' => 'GPS-T100', 'listing_type' => 'rent',
            'status' => 'To let', 'type' => 'Mews house', 'area' => 'Balham', 'postcode' => 'SW12',
            'price' => 2750, 'bedrooms' => 2, 'bathrooms' => 1, 'receptions' => 1,
            'tenure' => 'Long let', 'council_tax' => 'Band D', 'epc' => 'C', 'floor_area' => '800 sq ft',
            'featured_image' => $featured->asset_path, 'gallery_images' => [$gallery->asset_path],
            'summary' => 'A bright test property.', 'description_text' => "First paragraph.\nSecond paragraph.",
            'features_text' => "Private garden\nNear station", 'is_published' => 1,
            'meta_title' => 'Test Mews to rent', 'meta_description' => 'SEO description for Test Mews.',
            'meta_keywords' => 'test mews, Balham', 'seo_schema' => '{"@context":"https://schema.org","@type":"RealEstateListing"}',
        ])->assertRedirect();

        $property = Property::where('slug', 'test-mews')->firstOrFail();
        $this->assertSame(['First paragraph.', 'Second paragraph.'], $property->description);
        $this->assertSame([$gallery->asset_path], $property->gallery_images);
        $this->get('/rent')->assertOk()->assertSee('Test Mews')->assertSee('&pound;2,750 pcm', false);
        $this->get('/property/test-mews')->assertOk()->assertSee('Private garden')->assertSee('/'.$gallery->asset_path, false)
            ->assertSee('<title>Test Mews to rent</title>', false)
            ->assertSee('name="description" content="SEO description for Test Mews."', false)
            ->assertSee('application/ld+json', false);
        $this->actingAs($admin)->delete("/dashboard/media/{$featured->id}")->assertStatus(422);

        $this->actingAs($admin)->put("/dashboard/properties/{$property->id}", [
            'title' => 'Updated Mews', 'slug' => 'test-mews', 'reference' => 'GPS-T100', 'listing_type' => 'rent',
            'status' => 'Let agreed', 'type' => 'Mews house', 'area' => 'Balham', 'postcode' => 'SW12',
            'price' => 2800, 'bedrooms' => 2, 'bathrooms' => 1, 'receptions' => 1,
            'summary' => 'Updated summary.', 'description_text' => 'Updated description.', 'features_text' => 'Near station',
            'is_published' => 0,
        ])->assertRedirect();
        $this->assertDatabaseHas('properties', ['id' => $property->id, 'title' => 'Updated Mews', 'is_published' => false]);
        $this->get('/property/test-mews')->assertNotFound();

        $this->actingAs($admin)->delete("/dashboard/properties/{$property->id}")->assertRedirect('/dashboard/properties');
        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
    }

    public function test_property_form_uploads_featured_and_multiple_gallery_images_directly(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $initialMediaCount = Media::count();

        $this->actingAs($admin)->post('/dashboard/properties', [
            'title' => 'Uploaded Home', 'slug' => 'uploaded-home', 'reference' => 'GPS-UP1', 'listing_type' => 'rent',
            'status' => 'To let', 'type' => 'Apartment', 'area' => 'Balham', 'postcode' => 'SW12',
            'price' => 2100, 'bedrooms' => 1, 'bathrooms' => 1, 'receptions' => 1,
            'summary' => 'A directly uploaded property.', 'description_text' => 'Property description.',
            'features_text' => 'One feature', 'is_published' => 1,
            'featured_image_upload' => UploadedFile::fake()->image('featured.jpg', 1200, 800),
            'gallery_image_uploads' => [
                UploadedFile::fake()->image('living-room.jpg', 1000, 700),
                UploadedFile::fake()->image('kitchen.jpg', 1000, 700),
            ],
        ])->assertRedirect();

        $property = Property::where('slug', 'uploaded-home')->firstOrFail();
        $this->assertStringStartsWith('storage/media/', $property->featured_image);
        $this->assertCount(2, $property->gallery_images);
        $this->assertSame($initialMediaCount + 3, Media::count());
        foreach ([$property->featured_image, ...$property->gallery_images] as $assetPath) {
            Storage::disk('public')->assertExists(substr($assetPath, 8));
        }
    }

    public function test_commercial_page_uses_published_database_properties_and_filters(): void
    {
        Property::create([
            'title' => 'High Street Shop', 'slug' => 'high-street-shop', 'reference' => 'GPS-C900',
            'intent' => 'commercial', 'listing_type' => 'rent', 'is_commercial' => true, 'status' => 'To let', 'type' => 'Retail premises', 'area' => 'Streatham',
            'postcode' => 'SW16', 'price' => 45000, 'rent_period' => 'Yearly', 'bathrooms' => 1,
            'summary' => 'A visible retail unit.', 'is_published' => true, 'published_at' => now(),
        ]);
        Property::create([
            'title' => 'Hidden Shop', 'slug' => 'hidden-shop', 'reference' => 'GPS-C901',
            'intent' => 'commercial', 'listing_type' => 'rent', 'is_commercial' => true, 'status' => 'To let', 'type' => 'Retail premises', 'area' => 'Balham',
            'postcode' => 'SW12', 'price' => 25000, 'rent_period' => 'Yearly', 'bathrooms' => 1,
            'summary' => 'A draft unit.', 'is_published' => false,
        ]);

        $this->get('/commercial?location=Streatham')->assertOk()->assertSee('High Street Shop')->assertDontSee('Hidden Shop');
        $this->get('/commercial?max_price=30000')->assertOk()->assertDontSee('High Street Shop');
    }

    public function test_sale_and_commercial_are_independent_and_published_is_immediate(): void
    {
        $sale = Property::create([
            'title' => 'Residential Sale', 'slug' => 'residential-sale', 'reference' => 'GPS-S100',
            'intent' => 'buy', 'listing_type' => 'sale', 'is_commercial' => false, 'status' => 'For sale',
            'type' => 'Apartment', 'area' => 'Balham', 'postcode' => 'SW12', 'price' => 500000,
            'bedrooms' => 2, 'bathrooms' => 1, 'receptions' => 1, 'summary' => 'A residential sale.',
            'is_published' => true, 'published_at' => now()->addHours(5),
        ]);
        Property::create([
            'title' => 'Commercial Sale', 'slug' => 'commercial-sale', 'reference' => 'GPS-CS100',
            'intent' => 'commercial', 'listing_type' => 'sale', 'is_commercial' => true, 'status' => 'For sale',
            'type' => 'Office', 'area' => 'Tooting', 'postcode' => 'SW17', 'price' => 750000,
            'bathrooms' => 1, 'summary' => 'A commercial sale.', 'is_published' => true,
        ]);

        $this->get('/property/'.$sale->slug)->assertOk()->assertSee('Residential Sale');
        $this->get('/buy')->assertOk()->assertSee('Residential Sale')->assertSee('Commercial Sale');
        $this->get('/commercial?listing_type=sale')->assertOk()->assertSee('Commercial Sale')->assertDontSee('Residential Sale');
        $this->get('/rent')->assertOk()->assertDontSee('Residential Sale');
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
        $this->get('/about')->assertOk()->assertSee('Dummy about content.');
    }

    public function test_unpublished_dashboard_page_still_returns_not_found(): void
    {
        Page::create([
            'title' => 'Unpublished Page',
            'slug' => 'unpublished-page',
            'content' => 'This content should not be public.',
            'position' => 20,
            'is_active' => false,
        ]);

        $this->get('/unpublished-page')->assertNotFound();
    }

    public function test_admin_can_permanently_delete_pages_without_deleting_customizer_files(): void
    {
        Storage::fake('local');
        $admin = $this->admin();
        $page = Page::create([
            'title' => 'Frequently Asked Questions',
            'slug' => 'faq',
            'content' => 'Temporary FAQ content.',
            'position' => 10,
            'is_active' => true,
        ]);
        $customizerPage = Page::create([
            'title' => 'Temporary Customizer Page',
            'slug' => 'temporary-customizer-page',
            'customizer_template' => 'page-customizer/templates/temporary.json',
            'position' => 11,
            'is_active' => true,
        ]);
        Storage::disk('local')->put('page-customizer/templates/temporary.json', '{"name":"Saved customizer override"}');

        $this->delete("/dashboard/pages/{$page->id}")->assertRedirect('/login');
        $this->assertDatabaseHas('pages', ['id' => $page->id]);

        $this->actingAs($admin)->get('/dashboard/pages')
            ->assertOk()
            ->assertSee('Delete')
            ->assertSee('public URL will return 404');

        $this->get('/faq')->assertOk();
        $this->actingAs($admin)->delete("/dashboard/pages/{$page->id}")
            ->assertRedirect('/dashboard/pages')
            ->assertSessionHas('success', 'Page deleted permanently.');
        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
        $this->get('/faq')->assertNotFound();

        $this->actingAs($admin)->delete("/dashboard/pages/{$customizerPage->id}")
            ->assertRedirect('/dashboard/pages');
        $this->assertDatabaseMissing('pages', ['id' => $customizerPage->id]);
        Storage::disk('local')->assertExists('page-customizer/templates/temporary.json');
    }

    public function test_admin_can_delete_a_seeded_page_without_deleting_its_bundled_template(): void
    {
        $this->seed();
        $page = Page::where('slug', 'landlords')->firstOrFail();
        $bundledTemplate = resource_path('page-customizer/templates/landlords.json');

        $this->assertFileExists($bundledTemplate);
        $this->actingAs($this->admin())->delete("/dashboard/pages/{$page->id}")
            ->assertRedirect('/dashboard/pages');

        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
        $this->get('/landlords')->assertNotFound();
        $this->assertFileExists($bundledTemplate);
    }

    public function test_landlords_page_is_rendered_and_editable_through_customizer(): void
    {
        Storage::fake('local');
        $this->seed();
        $page = Page::where('slug', 'landlords')->firstOrFail();

        $this->get('/landlords')->assertOk()
            ->assertSee('Property management, handled.')
            ->assertSee('Management from move-in to renewal')
            ->assertSee('data-customizer-section-id="hero"', false)
            ->assertSee('data-customizer-section-id="services"', false)
            ->assertSee('Book a landlord consultation');

        $this->actingAs($this->admin())
            ->get("/dashboard/pages/{$page->id}/customizer")
            ->assertOk()
            ->assertSee('page-customizer/templates/landlords.json');

        $this->actingAs($this->admin())
            ->get("/dashboard/pages/{$page->id}/customizer/schema")
            ->assertOk()
            ->assertJsonFragment(['id' => 'banner'])
            ->assertJsonFragment(['id' => 'card-grid'])
            ->assertJsonFragment(['type' => 'image', 'label' => 'Icon image'])
            ->assertJsonMissing(['id' => 'landlord-services']);

        $template = app(\App\Services\PageCustomizerService::class)->readTemplate($page->customizer_template);
        $template['order'] = ['hero', 'services', 'intro', 'management', 'process', 'cta'];
        $template['sections']['management']['disabled'] = true;
        $template['sections']['services']['data']['cards'][0]['image'] = '/customizer/uploads/valuation-icon.png';

        $this->actingAs($this->admin())
            ->postJson("/dashboard/pages/{$page->id}/customizer/template", $template)
            ->assertOk()
            ->assertJson(['ok' => true]);

        Storage::disk('local')->assertExists('page-customizer/templates/landlords.json');
        $saved = app(\App\Services\PageCustomizerService::class)->readTemplate($page->customizer_template);
        $this->assertSame(['hero', 'services', 'intro', 'management', 'process', 'cta'], $saved['order']);
        $this->assertTrue($saved['sections']['management']['disabled']);
        $this->assertSame('/customizer/uploads/valuation-icon.png', $saved['sections']['services']['data']['cards'][0]['image']);
        $this->get('/landlords')->assertOk()->assertSee('/customizer/uploads/valuation-icon.png');

        unset($saved['sections']['intro']);
        $saved['order'] = array_values(array_filter($saved['order'], fn (string $id) => $id !== 'intro'));
        $this->actingAs($this->admin())
            ->postJson("/dashboard/pages/{$page->id}/customizer/template", $saved)
            ->assertOk()
            ->assertJson(['ok' => true]);

        $savedWithoutIntro = app(\App\Services\PageCustomizerService::class)->readTemplate($page->customizer_template);
        $this->assertArrayNotHasKey('intro', $savedWithoutIntro['sections']);
        $this->assertNotContains('intro', $savedWithoutIntro['order']);
    }
}
