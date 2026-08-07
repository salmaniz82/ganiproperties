<?php
namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\PageCustomizerService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminPageController extends Controller
{
    public function index()
    {
        return view('admin.pages', ['pages' => Page::orderBy('position')->orderBy('title')->get()]);
    }

    public function create(PageCustomizerService $customizer)
    {
        return view('admin.page-form', ['page' => null, 'customizerTemplates' => $customizer->templateOptions()]);
    }

    public function store(Request $request)
    {
        $page = Page::create($this->validated($request));
        return redirect()->route('admin.pages.edit', $page)->with('success', 'Page created.');
    }

    public function edit(Page $page, PageCustomizerService $customizer)
    {
        return view('admin.page-form', ['page' => $page, 'customizerTemplates' => $customizer->templateOptions()]);
    }

    public function update(Request $request, Page $page)
    {
        $page->update($this->validated($request, $page));
        return back()->with('success', 'Page updated.');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('admin.pages')->with('success', 'Page deleted permanently.');
    }

    private function validated(Request $request, ?Page $page = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'max:180'],
            'slug' => ['required', 'max:180', Rule::unique('pages')->ignore($page)],
            'content' => ['nullable', 'string'],
            'customizer_template' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'max:180'],
            'meta_keywords' => ['nullable', 'max:1000'],
            'schema' => ['nullable', 'json'],
            'position' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['schema'] = filled($data['schema'] ?? null) ? json_decode($data['schema'], true) : null;
        $data['customizer_template'] = filled($data['customizer_template'] ?? null) ? $data['customizer_template'] : null;
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }
}
