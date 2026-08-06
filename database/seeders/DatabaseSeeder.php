<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\DeliveryZone;
use App\Models\Page;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(['email'=>'admin@partypoppers.pk'],['name'=>'Party Poppers Admin','phone'=>'03001234567','password'=>'password','is_admin'=>true]);
        User::updateOrCreate(['email'=>'customer@example.com'],['name'=>'Demo Customer','phone'=>'03009876543','password'=>'password','is_admin'=>false]);

        $categories = [
            ['name'=>'Birthday Decorations','slug'=>'birthday-decorations','description'=>'Make your party extra special','image'=>'images/img-birthday-decor.jpg','position'=>1],
            ['name'=>'Balloons','slug'=>'balloons','description'=>'Brighten every moment','image'=>'images/img-balloons.jpg','position'=>2],
            ['name'=>'Gift Baskets','slug'=>'gift-baskets','description'=>'Happiness in a basket','image'=>'images/img-gift-basket.jpg','position'=>3],
            ['name'=>'Flowers','slug'=>'flowers','description'=>'Fresh flowers, lasting smiles','image'=>'images/img-flower-bouquet.jpg','position'=>4],
            ['name'=>'Chocolate Bouquets','slug'=>'chocolate-bouquets','description'=>'Sweetness wrapped with love','image'=>'images/img-chocolate-bouquet.jpg','position'=>5],
            ['name'=>'Cakes & Treats','slug'=>'cakes','description'=>'Delicious creations','image'=>'images/img-cake-blue.jpg','position'=>6],
            ['name'=>'Customized Items','slug'=>'customized-items','description'=>'Make it personal','image'=>'images/img-custom-mug.jpg','position'=>7],
            ['name'=>'Party Supplies','slug'=>'party-supplies','description'=>'Everything you need to party','image'=>'images/img-party-supplies.jpg','position'=>8],
        ];
        foreach ($categories as $row) Category::updateOrCreate(['slug'=>$row['slug']],$row);
        $catalog = [
            ['Birthday Decorations','Balloon Arch Kit','balloon-arch-kit','PP-ARCH-01',2500,'images/prod-arch.jpg'],
            ['Birthday Decorations','Happy Birthday Backdrop','birthday-backdrop','PP-BACK-01',3200,'images/prod-backdrop.jpg'],
            ['Birthday Decorations','Neon Light Sign','neon-light-sign','PP-NEON-01',1800,'images/prod-neon.jpg'],
            ['Birthday Decorations','Table Decoration Set','table-decoration-set','PP-TABLE-01',2000,'images/prod-table.jpg'],
            ['Balloons','Foil Balloon Set','foil-balloon-set','PP-BAL-01',1200,'images/prod-balloon-set.jpg'],
            ['Flowers','Rose Bouquet','rose-bouquet','PP-FLOW-01',2800,'images/prod-rose.jpg'],
            ['Flowers','Sunflower Bouquet','sunflower-bouquet','PP-FLOW-02',2500,'images/prod-sunflower.jpg'],
            ['Flowers','Lily Bouquet','lily-bouquet','PP-FLOW-03',3000,'images/prod-lily.jpg'],
            ['Chocolate Bouquets','Ferrero Rocher Bouquet','ferrero-bouquet','PP-CHOC-01',3000,'images/prod-ferrero.jpg'],
            ['Chocolate Bouquets','KitKat Bouquet','kitkat-bouquet','PP-CHOC-02',2200,'images/prod-kitkat.jpg'],
            ['Chocolate Bouquets','Dairy Milk Bouquet','dairy-milk-bouquet','PP-CHOC-03',2500,'images/prod-dairy.jpg'],
            ['Cakes & Treats','Birthday Cake','birthday-cake','PP-CAKE-01',2800,'images/prod-birthday-cake.jpg'],
            ['Cakes & Treats','Chocolate Cake','chocolate-cake','PP-CAKE-02',2500,'images/prod-chocolate-cake.jpg'],
            ['Cakes & Treats','Cupcakes Set','cupcakes-set','PP-CAKE-03',1500,'images/prod-cupcakes.jpg'],
            ['Customized Items','Personalized Celebration Mug','personalized-mug','PP-CUSTOM-01',1600,'images/img-custom-mug.jpg'],
        ];
        foreach ($catalog as [$cat,$name,$slug,$sku,$price,$image]) Product::updateOrCreate(['sku'=>$sku],[
            'category_id'=>Category::where('name',$cat)->value('id'),'name'=>$name,'slug'=>$slug,'description'=>"Premium $name prepared with care for your celebration.",
            'price'=>$price,'stock'=>25,'image'=>$image,'is_active'=>true,'is_featured'=>true,
            'customization_schema'=>$cat==='Customized Items'?['text'=>true,'option'=>true,'image'=>true]:null,
        ]);
        foreach ([['Lahore Central','Lahore',250,true,'15:00',0],['Karachi Central','Karachi',350,true,'14:00',0],['Islamabad & Rawalpindi','Islamabad',300,true,'15:00',0],['Nationwide Delivery','Other',500,false,null,3]] as [$name,$city,$fee,$same,$cutoff,$days]) {
            DeliveryZone::updateOrCreate(['name'=>$name],['city'=>$city,'fee'=>$fee,'same_day_enabled'=>$same,'same_day_cutoff'=>$cutoff,'minimum_days'=>$days,'is_active'=>true]);
        }

        $pages = [
            ['About Us','about','Learn more about Party Poppers, our celebration services, and how we help families plan memorable birthdays, gifts, and events.',1],
            ['FAQ','faq','Find quick answers about delivery timing, customization, same-day orders, payment methods, and how to track your order.',2],
            ['Terms & Conditions','terms','These sample terms explain how orders, payments, fulfillment, cancellations, and website use work for Party Poppers customers.',3],
            ['Refund Policy','refund-policy','This sample refund policy explains eligibility, damaged item reporting, refund review timelines, and replacement options.',4],
            ['Return Policy','return-policy','This sample return policy explains which celebration products may be returned and which customized or perishable items are final sale.',5],
            ['Privacy Policy','privacy-policy','This sample privacy policy explains how Party Poppers collects customer details, delivery information, order notes, uploaded customization files, and communication preferences.',6],
        ];
        foreach ($pages as [$title,$slug,$content,$position]) {
            Page::updateOrCreate(['slug'=>$slug],[
                'title'=>$title,
                'content'=>$content."\n\nThis is placeholder content. Replace it from the dashboard with your final brand copy.",
                'meta_title'=>$title.' | Party Poppers',
                'meta_keywords'=>strtolower(str_replace(['&',' '], [',','-'], $title)).', party poppers, celebration service',
                'schema'=>['@context'=>'https://schema.org','@type'=>'WebPage','name'=>$title],
                'position'=>$position,
                'is_active'=>true,
            ]);
        }

        foreach ([
            ['Services','services',"Party Poppers celebration services are managed through the JSON page customizer.\n\nDefault content fallback: use this page to describe birthday decorations, gift baskets, flower bouquets, cakes, personalized items, and same-day delivery support.\n\nThis text remains useful for search, exports, and fallback rendering even when the visual customizer template is active.",'page-customizer/templates/services.json',7],
            ['Events','events',"Party Poppers event styling content is managed through the JSON page customizer.\n\nDefault content fallback: describe birthday setups, anniversary surprises, corporate gifting, home celebrations, and coordinated decoration packages.\n\nThis text can be edited from the dashboard while the visible page body is controlled by flexible customizer sections.",'page-customizer/templates/events.json',8],
        ] as [$title,$slug,$content,$template,$position]) {
            Page::updateOrCreate(['slug'=>$slug],[
                'title'=>$title,
                'content'=>$content,
                'customizer_template'=>$template,
                'meta_title'=>$title.' | Party Poppers',
                'meta_keywords'=>strtolower($title).', party poppers, customizer page',
                'schema'=>['@context'=>'https://schema.org','@type'=>'WebPage','name'=>$title],
                'position'=>$position,
                'is_active'=>true,
            ]);
        }
    }
}
