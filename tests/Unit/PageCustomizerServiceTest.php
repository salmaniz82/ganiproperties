<?php

namespace Tests\Unit;

use App\Services\PageCustomizerService;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PageCustomizerServiceTest extends TestCase
{
    public function test_bundled_templates_are_available_without_storage_files(): void
    {
        Storage::fake('local');

        $customizer = app(PageCustomizerService::class);

        $this->assertArrayHasKey('page-customizer/templates/services.json', $customizer->templateOptions());
        $this->assertArrayHasKey('page-customizer/templates/events.json', $customizer->templateOptions());
        $this->assertArrayHasKey('page-customizer/templates/landlords.json', $customizer->templateOptions());
        $this->assertSame('Services page', $customizer->readTemplate('page-customizer/templates/services.json')['name']);
        $landlords = $customizer->readTemplate('page-customizer/templates/landlords.json');
        $this->assertSame('banner', $landlords['sections']['hero']['type']);
        $this->assertSame('image-content', $landlords['sections']['intro']['type']);
        $this->assertSame('card-grid', $landlords['sections']['services']['type']);
        $this->assertSame([
            '/images/icons/key.svg',
            '/images/icons/chat.svg',
            '/images/icons/shield.svg',
            '/images/icons/pin.svg',
            '/images/icons/check.svg',
            '/images/icons/heart.svg',
        ], array_column($landlords['sections']['services']['data']['cards'], 'image'));
        foreach ($landlords['sections']['services']['data']['cards'] as $card) {
            $this->assertFileExists(public_path(ltrim($card['image'], '/')));
        }
        $this->assertSame('feature-panel', $landlords['sections']['management']['type']);
        $this->assertSame('steps', $landlords['sections']['process']['type']);
        $this->assertSame('cta-banner', $landlords['sections']['cta']['type']);
        $this->assertSame(['hero', 'services', 'steps', 'faq', 'request'], array_values(array_map(fn ($section) => $section['type'], $customizer->readTemplate('page-customizer/templates/services.json')['sections'])));
        $this->assertSame(['hero', 'image-carousel', 'services', 'testimonials', 'request'], array_values(array_map(fn ($section) => $section['type'], $customizer->readTemplate('page-customizer/templates/events.json')['sections'])));
    }

    public function test_saved_template_overrides_bundled_template_in_writable_storage(): void
    {
        Storage::fake('local');

        $customizer = app(PageCustomizerService::class);
        $path = 'page-customizer/templates/services.json';
        $template = $customizer->readTemplate($path);
        $template['title'] = 'Saved custom title';

        $customizer->writeTemplate($path, $template);

        Storage::disk('local')->assertExists($path);
        $this->assertSame('Saved custom title', $customizer->readTemplate($path)['title']);
        $this->assertNotSame(
            'Saved custom title',
            json_decode(file_get_contents(resource_path($path)), true)['title'],
        );
    }

    public function test_render_marks_repeated_component_instances_with_independent_preview_targets(): void
    {
        Storage::fake('local');

        $customizer = app(PageCustomizerService::class);
        $path = 'page-customizer/templates/repeated-sections.json';
        $customizer->writeTemplate($path, [
            'name' => 'Repeated sections',
            'title' => 'Repeated sections',
            'template' => 'page',
            'order' => ['primary_cards', 'secondary_cards'],
            'sections' => [
                'primary_cards' => [
                    'type' => 'card-grid',
                    'disabled' => false,
                    'data' => ['title' => 'Primary cards', 'cards' => []],
                ],
                'secondary_cards' => [
                    'type' => 'card-grid',
                    'disabled' => false,
                    'data' => ['title' => 'Secondary cards', 'cards' => []],
                ],
            ],
        ]);

        $html = $customizer->render($path);

        $this->assertStringContainsString('data-customizer-section-id="primary_cards"', $html);
        $this->assertStringContainsString('data-customizer-section-id="secondary_cards"', $html);
        $this->assertSame(2, substr_count($html, 'class="card-grid-section"'));
    }
}
