<?php

namespace Tests\Feature;

use Tests\TestCase;

class SearchEngineBlockingTest extends TestCase
{
    public function test_html_pages_block_search_engine_indexing(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertHeader(
                'X-Robots-Tag',
                'noindex, nofollow, noarchive, nosnippet, noimageindex',
            )
            ->assertSee(
                '<meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noimageindex">',
                false,
            );
    }

    public function test_robots_file_disallows_all_crawlers(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('User-agent: *', $robots);
        $this->assertStringContainsString('Disallow: /', $robots);
    }
}
