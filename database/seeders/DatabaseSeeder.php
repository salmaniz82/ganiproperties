<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Property;
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
        User::updateOrCreate(['email'=>'app@ganiproperties.co.uk'],['name'=>'Gani Property Admin','phone'=>'02086737778','password'=>'password','is_admin'=>true]);

        foreach (collect(config('gani_properties', []))->whereIn('intent', ['rent', 'commercial']) as $row) {
            Property::updateOrCreate(['slug' => $row['slug']], [
                'title' => $row['title'],
                'reference' => $row['reference'],
                'intent' => $row['intent'],
                'listing_type' => $row['intent'] === 'rent' ? 'rent' : (str_contains(strtolower($row['status']), 'sale') ? 'sale' : 'rent'),
                'is_commercial' => $row['intent'] === 'commercial',
                'status' => $row['status'],
                'type' => $row['type'],
                'area' => $row['area'],
                'postcode' => $row['postcode'],
                'price' => $row['price'],
                'rent_period' => $row['rent_period'] ?? null,
                'bedrooms' => $row['bedrooms'],
                'bathrooms' => $row['bathrooms'],
                'receptions' => $row['receptions'],
                'tenure' => $row['tenure'] ?? null,
                'council_tax' => $row['council_tax'] ?? null,
                'epc' => $row['epc'] ?? null,
                'floor_area' => $row['floor_area'] ?? null,
                'featured_image' => ltrim($row['image'], '/'),
                'gallery_images' => [],
                'summary' => $row['summary'],
                'description' => $row['description'] ?? [],
                'features' => $row['features'] ?? [],
                'is_published' => true,
                'published_at' => now(),
            ]);
        }

        $pages = [
            ['FAQ','faq','Find quick answers about lettings, viewings, property management, guaranteed rent, tenant enquiries and landlord support.',1],
            ['Terms & Conditions','terms',implode("\n\n", [
                'These draft terms are provided as a working reference for Gani Property Services and should be reviewed by the client before publication on the live site.',
                'Website information is provided for general guidance only. Property particulars, prices, rents, availability, measurements, floor areas, photographs and descriptions are supplied in good faith and should be checked by applicants, buyers, tenants and landlords before making decisions.',
                'Submitting an enquiry, valuation request, viewing request or landlord callback request does not create a binding contract with Gani Property Services. A formal agreement, offer acceptance, tenancy agreement, management agreement or sales memorandum will be required before any instruction or transaction proceeds.',
                'Applicants may be asked to provide identification, referencing information, proof of funds, employment details, right to rent evidence and other compliance information where required. Offers and tenancies remain subject to contract, satisfactory checks and landlord or seller approval.',
                'Landlord services, including lettings, property management and guaranteed rent arrangements, are subject to written terms agreed directly with the client. Fees, service levels, notice periods and responsibilities should be confirmed in the signed agency agreement.',
                'Nothing on this website should be treated as legal, mortgage, tax, planning or investment advice. Clients should take independent professional advice where appropriate.',
            ]),2],
            ['Refund Policy','refund-policy',implode("\n\n", [
                'This draft refund policy is intended as a practical reference for any future paid services, holding payments, administration fees or online payments connected with Gani Property Services. It should be checked against the final fee structure and current legal requirements before launch.',
                'Where a payment is taken for a clearly defined service, the refund position should be explained before payment is made. Refunds may depend on the type of payment, the stage reached, the work already completed and any signed agreement between the parties.',
                'For tenant or applicant payments, any holding deposit, reservation payment or agreed fee should be handled in line with applicable housing legislation and the written terms supplied at the time of payment.',
                'For landlord services, refunds or cancellations should follow the signed agency, lettings, management or guaranteed rent agreement. Where marketing, compliance checks, referencing, photography or administrative work has already started, reasonable costs may be retained if the agreement allows.',
                'If a payment is made in error, duplicated or processed incorrectly, the client should contact Gani Property Services promptly with the payment date, amount, payer name and reason for the request so the matter can be reviewed.',
                'Approved refunds should normally be returned to the original payment method. Processing times may depend on the bank, card provider or payment platform used.',
            ]),3],
            ['Return Policy','return-policy',implode("\n\n", [
                'Gani Property Services is a property agency and does not generally sell physical goods through this website. This draft return policy is included as a reference in case the live site later offers paid documents, downloadable guides, printed materials or other resources.',
                'For digital resources, returns may not apply once access has been provided or a download has started, unless the item is faulty, duplicated or not as described.',
                'For printed documents or physical materials, the client should confirm whether returns are accepted, the return window, the required condition of the item and who is responsible for return postage.',
                'Any property-related documents, reports, valuation notes, tenancy paperwork or management documents prepared specifically for a client may be treated as bespoke service materials and may not be returnable once work has begun.',
                'This page should be finalised before any ecommerce, paid resource library or document-ordering feature is activated on the live site.',
            ]),4],
            ['Privacy Policy','privacy-policy',implode("\n\n", [
                'This draft privacy policy explains the types of personal information Gani Property Services may collect through the website and during property enquiries. It should be reviewed and completed with the client privacy lead, registered company details and any appointed data protection contact before publication.',
                'Gani Property Services may collect names, email addresses, phone numbers, property addresses, search requirements, valuation details, viewing preferences, landlord service enquiries and messages submitted through website forms, phone calls, email or in-person conversations.',
                'Information may be used to respond to enquiries, arrange valuations and viewings, match applicants with suitable properties, manage landlord instructions, support tenancy progression, meet legal and regulatory duties, improve service quality and send relevant property updates where consent or another lawful basis applies.',
                'For lettings and sales progression, additional checks may be required, including identity verification, right to rent checks, referencing, affordability information, proof of funds and contact details for professional advisers. These details should only be requested when relevant to the service being provided.',
                'Personal information may be shared with landlords, sellers, tenants, buyers, referencing providers, contractors, conveyancers, inventory clerks, deposit protection providers, payment processors, software providers and regulators where necessary for the property service or where legally required.',
                'The website may use analytics, cookies, embedded maps, form tools or advertising pixels to understand enquiries and improve the user experience. The final live site should list the actual tools in use and link to a cookie notice where required.',
                'People can contact Gani Property Services to ask about their personal information, request corrections, unsubscribe from marketing or raise a privacy concern. Final contact details and retention periods should be confirmed before this policy is published.',
            ]),5],
        ];
        foreach ($pages as [$title,$slug,$content,$position]) {
            Page::updateOrCreate(['slug'=>$slug],[
                'title'=>$title,
                'content'=>$content,
                'meta_title'=>$title.' | Gani Property Services',
                'meta_keywords'=>strtolower(str_replace(['&',' '], [',','-'], $title)).', gani property services, balham estate agents',
                'schema'=>['@context'=>'https://schema.org','@type'=>'WebPage','name'=>$title],
                'position'=>$position,
                'is_active'=>true,
            ]);
        }

        Page::whereIn('slug', ['about-us', 'about-static'])->delete();

        foreach ([
            ['Services','services',"Gani Property Services pages are managed through the JSON page customizer.\n\nDefault content fallback: use this page to describe lettings, guaranteed rent, property management, landlord advice and tenant support.\n\nThis text remains useful for search, exports and fallback rendering even when the visual customizer template is active.",'page-customizer/templates/services.json',6],
            ['Events','events',"Gani Property Services event and update content is managed through the JSON page customizer.\n\nDefault content fallback: describe landlord open days, valuation campaigns, rental market updates and local property events.\n\nThis text can be edited from the dashboard while the visible page body is controlled by flexible customizer sections.",'page-customizer/templates/events.json',7],
            ['Landlords','landlords',"Property management and lettings support for landlords in Balham and South London.",'page-customizer/templates/landlords.json',8],
            ['About Us','about',"Independent property advice, sales, lettings and management services from Gani Property Services in Balham.",'page-customizer/templates/about.json',9],
        ] as [$title,$slug,$content,$template,$position]) {
            Page::updateOrCreate(['slug'=>$slug],[
                'title'=>$title,
                'content'=>$content,
                'customizer_template'=>$template,
                'meta_title'=>match ($slug) {
                    'landlords' => 'Landlord & Property Management Services | Gani Property Services',
                    'about' => 'About Us | Gani Property Services',
                    default => $title.' | Gani Property Services',
                },
                'meta_keywords'=>match ($slug) {
                    'landlords' => 'landlord services, property management, lettings management, Balham estate agents',
                    'about' => 'about Gani Property Services, independent estate agents, Balham estate agents, South London property experts',
                    default => strtolower($title).', gani property services, customizer page',
                },
                'schema'=>['@context'=>'https://schema.org','@type'=>$slug === 'landlords' ? 'Service' : ($slug === 'about' ? 'AboutPage' : 'WebPage'),'name'=>$slug === 'landlords' ? 'Landlord and property management services' : $title],
                'position'=>$position,
                'is_active'=>true,
            ]);
        }
    }
}
