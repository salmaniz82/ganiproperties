<?php
namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    public function landlords()
    {
        return $this->show('landlords');
    }

    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();

        if ($page->customizer_template) {
            return response()
                ->view('store.customizer-page', compact('page'))
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        }

        return view('store.page', compact('page'));
    }
}
