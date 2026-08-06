<?php
namespace Tests\Feature;
use App\Models\{Category,DeliveryZone,Page,Product,User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class CommerceTest extends TestCase {
    use RefreshDatabase;
    private function product(): Product { $id=uniqid(); $category=Category::create(['name'=>'Balloons','slug'=>"balloons-$id"]); return Product::create(['category_id'=>$category->id,'name'=>'Balloon Kit','slug'=>"balloon-kit-$id",'sku'=>"BAL-$id",'price'=>2500,'stock'=>2,'is_active'=>true]); }
    public function test_catalog_and_product_are_visible(): void { $product=$this->product(); $this->get('/shop')->assertOk()->assertSee('Balloon Kit'); $this->get('/products/'.$product->slug)->assertOk()->assertSee('PKR 2,500'); }
    public function test_catalog_and_category_use_pretty_pagination_urls(): void
    {
        $category = Category::create(['name'=>'Birthday Decorations','slug'=>'birthday-decorations']);
        foreach (range(1, 13) as $index) {
            Product::create([
                'category_id'=>$category->id,
                'name'=>"Birthday Kit {$index}",
                'slug'=>"birthday-kit-{$index}",
                'sku'=>"BDAY-{$index}",
                'price'=>1500,
                'stock'=>5,
                'is_active'=>true,
            ]);
        }

        $this->get('/shop')
            ->assertOk()
            ->assertSee('/shop/page/2', false)
            ->assertDontSee('?page=2', false);

        $this->get('/shop/page/2')->assertOk();
        $this->get('/shop?page=2')->assertRedirect('/shop/page/2');

        $this->get('/product-category/birthday-decorations')
            ->assertOk()
            ->assertSee('/product-category/birthday-decorations/page/2', false)
            ->assertDontSee('?page=2', false);

        $this->get('/product-category/birthday-decorations/page/2')->assertOk();
        $this->get('/product-category/birthday-decorations?page=2')->assertRedirect('/product-category/birthday-decorations/page/2');
    }
    public function test_guest_can_add_to_cart_and_checkout_with_cod(): void {
        $product=$this->product(); $zone=DeliveryZone::create(['name'=>'Lahore','city'=>'Lahore','fee'=>250,'is_active'=>true]);
        $this->post("/cart/{$product->id}",['quantity'=>1])->assertRedirect('/cart');
        $this->post('/checkout',['customer_name'=>'Guest','customer_phone'=>'03001234567','customer_email'=>'guest@example.com','line1'=>'Street 1','delivery_zone_id'=>$zone->id])->assertRedirect();
        $this->assertDatabaseHas('orders',['customer_phone'=>'03001234567','total'=>2750,'payment_method'=>'cod']);
        $this->assertDatabaseHas('products',['id'=>$product->id,'stock'=>1]);
    }
    public function test_cart_supports_create_read_update_delete_and_clear_with_true_totals(): void
    {
        $first = $this->product();
        $second = Product::create(['category_id'=>$first->category_id,'name'=>'Gift Box','slug'=>'gift-box-'.uniqid(),'sku'=>'GIFT-'.uniqid(),'price'=>1250,'stock'=>10,'is_active'=>true]);
        $firstKey = $first->id.'-'.hash('sha256', json_encode([]));
        $secondKey = $second->id.'-'.hash('sha256', json_encode([]));

        $this->post("/cart/{$first->id}", ['quantity'=>1])->assertRedirect('/cart');
        $this->post("/cart/{$second->id}", ['quantity'=>2])->assertRedirect('/cart');
        $this->get('/cart')->assertOk()->assertSee('PKR 5,000')->assertSee('3 item(s)');

        $this->put("/cart/{$secondKey}", ['quantity'=>3])->assertRedirect();
        $this->get('/cart')->assertSee('PKR 6,250')->assertSee('4 item(s)');

        $this->delete("/cart/{$firstKey}")->assertRedirect();
        $this->get('/cart')->assertDontSee('Balloon Kit')->assertSee('PKR 3,750');

        $this->delete('/cart')->assertRedirect();
        $this->get('/cart')->assertSee('Your cart is empty');
    }
    public function test_cart_rejects_quantities_above_available_stock(): void
    {
        $product = $this->product();
        $this->from('/products/'.$product->slug)->post("/cart/{$product->id}", ['quantity'=>3])
            ->assertRedirect('/products/'.$product->slug)
            ->assertSessionHasErrors('quantity');
    }
    public function test_customized_products_create_distinct_cart_lines(): void
    {
        $product = $this->product();
        $this->post("/cart/{$product->id}", ['quantity'=>1,'custom_text'=>'Happy Birthday']);
        $this->post("/cart/{$product->id}", ['quantity'=>1,'custom_text'=>'Congratulations']);

        $this->get('/cart')->assertSee('2 item(s) across 2 product line(s)')->assertSee('Happy Birthday')->assertSee('Congratulations');
    }
    public function test_checkout_uses_authoritative_product_price_and_delivery_fee(): void
    {
        $product = $this->product();
        $zone = DeliveryZone::create(['name'=>'Lahore','city'=>'Lahore','fee'=>375,'is_active'=>true]);
        $this->post("/cart/{$product->id}", ['quantity'=>2]);
        $product->update(['price'=>2750]);

        $this->post('/checkout',['customer_name'=>'Guest','customer_phone'=>'03001234567','line1'=>'Street 1','delivery_zone_id'=>$zone->id])->assertRedirect();
        $this->assertDatabaseHas('orders',['subtotal'=>5500,'delivery_fee'=>375,'total'=>5875]);
        $this->assertDatabaseHas('order_items',['unit_price'=>2750,'quantity'=>2,'total'=>5500]);
    }
    public function test_listing_add_to_cart_returns_ajax_drawer_payload(): void
    {
        $product = $this->product();

        $this->postJson("/cart/{$product->id}", ['quantity'=>1])
            ->assertOk()
            ->assertJsonPath('message', 'Added to cart.')
            ->assertJsonPath('cart.item_count', 1)
            ->assertJsonPath('cart.line_count', 1)
            ->assertJsonPath('cart.subtotal', 2500)
            ->assertJsonPath('cart.subtotal_formatted', 'PKR 2,500')
            ->assertJsonPath('cart.items.0.name', 'Balloon Kit')
            ->assertJsonPath('cart.items.0.quantity', 1)
            ->assertJsonPath('cart.items.0.line_total', 2500);
    }
    public function test_quick_cart_ajax_can_update_and_remove_items(): void
    {
        $product = $this->product();
        $key = $product->id.'-'.hash('sha256', json_encode([]));
        $this->postJson("/cart/{$product->id}", ['quantity'=>1]);

        $this->putJson("/cart/{$key}", ['quantity'=>2])
            ->assertOk()
            ->assertJsonPath('cart.item_count', 2)
            ->assertJsonPath('cart.subtotal', 5000)
            ->assertJsonPath('cart.items.0.quantity', 2);

        $this->deleteJson("/cart/{$key}")
            ->assertOk()
            ->assertJsonPath('cart.item_count', 0)
            ->assertJsonPath('cart.subtotal', 0)
            ->assertJsonCount(0, 'cart.items');
    }
    public function test_store_layout_contains_global_quick_cart_and_ajax_hook(): void
    {
        $this->product();

        $this->get('/shop')
            ->assertOk()
            ->assertSee('id="quickCart"', false)
            ->assertSee('js-ajax-add-cart', false)
            ->assertSee('js/store-cart.js', false);
    }
    public function test_store_pagination_icons_are_constrained(): void
    {
        $css = file_get_contents(public_path('css/store.css'));
        $this->assertStringContainsString('nav[role=navigation] svg', $css);
        $this->assertStringContainsString('width:16px!important', $css);
    }
    public function test_store_layout_preserves_patterned_footer(): void
    {
        foreach (['about'=>'About Us','faq'=>'FAQ','terms'=>'Terms','refund-policy'=>'Refund Policy','return-policy'=>'Return Policy','privacy-policy'=>'Privacy Policy'] as $slug => $title) {
            Page::updateOrCreate(['slug'=>$slug], ['title'=>$title,'content'=>$title,'is_active'=>true]);
        }

        $this->get('/shop')
            ->assertOk()
            ->assertSee('class="site-footer"', false)
            ->assertSee('class="footer-clouds"', false)
            ->assertSee('footer-logo-transparent.png', false)
            ->assertSee('footer-mascot-transparent.png', false)
            ->assertSee('footer-payment-logos.png', false)
            ->assertSee('/services', false)
            ->assertSee('/events', false)
            ->assertSee('/privacy-policy', false)
            ->assertSee('/faq', false)
            ->assertSee('/terms', false)
            ->assertSeeText('Subscribe Us');
    }
    public function test_non_admin_cannot_access_admin(): void { $user=User::factory()->create(['is_admin'=>false]); $this->actingAs($user)->get('/dashboard')->assertForbidden(); }
    public function test_admin_can_access_admin(): void { $admin=User::factory()->create(['is_admin'=>true]); $this->actingAs($admin)->get('/dashboard')->assertOk(); }
    public function test_admin_pages_listing_has_preview_action(): void
    {
        $admin = User::factory()->create(['is_admin'=>true]);
        Page::updateOrCreate(['slug'=>'preview-test'], ['title'=>'Preview Test','content'=>'Preview content','is_active'=>true]);

        $this->actingAs($admin)
            ->get('/dashboard/pages')
            ->assertOk()
            ->assertSee('preview-action', false)
            ->assertSee('/preview-test', false);
    }
    public function test_login_and_register_pages_are_separate(): void
    {
        $this->get('/login')->assertOk()->assertSee('Create account')->assertDontSee('Confirm password');
        $this->get('/register')->assertOk()->assertSee('Already have an account?')->assertSee('Confirm password');
    }
    public function test_registered_users_are_customers_and_admins_redirect_to_dashboard(): void
    {
        $this->post('/register', ['name'=>'Customer','email'=>'customer-new@example.com','phone'=>'03000000000','password'=>'password','password_confirmation'=>'password'])
            ->assertRedirect('/account');
        $this->assertDatabaseHas('users', ['email'=>'customer-new@example.com','is_admin'=>false]);

        auth()->logout();
        $admin = User::factory()->create(['is_admin'=>true,'password'=>'password']);
        $this->post('/login', ['email'=>$admin->email,'password'=>'password'])->assertRedirect('/dashboard');
    }
    public function test_landing_page_preserves_hero_and_category_mosaic(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('landing-hero__panel', false)
            ->assertSee('landing-category-grid', false)
            ->assertSee('category-birthday', false)
            ->assertSee('category-flowers', false)
            ->assertSeeTextInOrder(['Make Every', 'Celebration', 'Memorable', 'Shop By', 'Category']);

        $css = file_get_contents(public_path('css/store.css'));
        $this->assertStringContainsString("url('/images/hero-visual.jpg')", $css);
        $this->assertStringNotContainsString("url('/images/hero.jpg')", $css);
    }
    public function test_public_page_renders_metadata_and_content(): void
    {
        Page::updateOrCreate(['slug'=>'faq'], ['title'=>'FAQ','content'=>'Dummy FAQ content.','meta_title'=>'FAQ | Party Poppers','meta_keywords'=>'faq, party poppers','schema'=>['@context'=>'https://schema.org','@type'=>'FAQPage'],'is_active'=>true]);

        $this->get('/faq')
            ->assertOk()
            ->assertSee('FAQ | Party Poppers')
            ->assertSee('name="keywords"', false)
            ->assertSee('application/ld+json', false)
            ->assertSeeText('Dummy FAQ content.');
    }

    public function test_customizer_save_updates_rendered_page(): void
    {
        $admin = User::factory()->create(['is_admin'=>true]);
        $page = Page::updateOrCreate(['slug'=>'services'], [
            'title'=>'Services',
            'content'=>'Customizer services page.',
            'customizer_template'=>'page-customizer/templates/services.json',
            'is_active'=>true,
        ]);
        $template = app(\App\Services\PageCustomizerService::class)->readTemplate($page->customizer_template);
        $original = $template['sections']['hero']['data']['title'];
        $template['sections']['hero']['data']['title'] = 'Saved Through Dashboard Test';

        try {
            $this->actingAs($admin)
                ->postJson(route('admin.pages.customizer.save', $page), $template)
                ->assertOk()
                ->assertJsonPath('ok', true)
                ->assertJsonPath('template_path', $page->customizer_template);

            $response = $this->get('/services')->assertOk()->assertSeeText('Saved Through Dashboard Test');
            $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        } finally {
            $template['sections']['hero']['data']['title'] = $original;
            app(\App\Services\PageCustomizerService::class)->writeTemplate($page->customizer_template, $template);
        }
    }

}
