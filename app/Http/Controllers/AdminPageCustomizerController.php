<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\PageCustomizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AdminPageCustomizerController extends Controller
{
    public function edit(Page $page, PageCustomizerService $customizer)
    {
        abort_unless($page->customizer_template, 404);

        return view('admin.page-customizer', compact('page'));
    }

    public function schema(PageCustomizerService $customizer)
    {
        return response()->json($customizer->sectionSchemas())->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function template(Page $page, PageCustomizerService $customizer)
    {
        abort_unless($page->customizer_template, 404);

        return response()->json($customizer->readTemplate($page->customizer_template))->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function save(Request $request, Page $page, PageCustomizerService $customizer)
    {
        abort_unless($page->customizer_template, 404);
        $payload = $this->validatedTemplate($request);

        $customizer->writeTemplate($page->customizer_template, $payload);

        return response()->json([
            'ok' => true,
            'message' => 'Saved',
            'template_path' => $page->customizer_template,
            'saved_at' => now()->toIso8601String(),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function preview(Page $page)
    {
        abort_unless($page->customizer_template, 404);

        return response()
            ->view('store.customizer-page', compact('page'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    private function validatedTemplate(Request $request): array
    {
        return $request->validate([
            'name' => ['nullable', 'string', 'max:180'],
            'title' => ['nullable', 'string', 'max:180'],
            'template' => ['nullable', 'string', 'max:80'],
            'order' => ['required', 'array'],
            'sections' => ['required', 'array'],
        ]);
    }

    public function upload(Request $request, Page $page)
    {
        abort_unless($page->customizer_template, 404);
        $data = $request->validate(['image' => ['required', 'image', 'max:4096']]);

        $directory = public_path('customizer/uploads');
        File::ensureDirectoryExists($directory);

        $file = $data['image'];
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'image';
        $filename = $name.'-'.Str::random(8).'.'.$file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return response()->json(['ok' => true, 'path' => asset('customizer/uploads/'.$filename)]);
    }
}
