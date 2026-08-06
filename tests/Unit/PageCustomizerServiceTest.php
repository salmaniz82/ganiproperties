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
        $this->assertSame('Services page', $customizer->readTemplate('page-customizer/templates/services.json')['name']);
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
}
