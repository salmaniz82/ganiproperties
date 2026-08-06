<?php
namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $allowedStaticPages = ['services', 'events', 'faq', 'privacy-policy', 'terms', 'refund-policy', 'return-policy'];
        abort_unless($page->customizer_template || in_array($page->slug, $allowedStaticPages, true), 404);

        if ($page->customizer_template) {
            return response()
                ->view('store.customizer-page', compact('page'))
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        }

        return view('store.page', compact('page'));
    }
}
