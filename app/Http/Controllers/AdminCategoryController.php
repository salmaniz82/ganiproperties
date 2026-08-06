<?php
namespace App\Http\Controllers;

use App\Models\{AuditLog, Category, Media};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminCategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories', ['categories' => $this->flattenCategories(true)]);
    }

    public function create()
    {
        return view('admin.category-form', $this->formData());
    }

    public function store(Request $request)
    {
        $category = Category::create($this->validated($request));
        $this->audit($request, 'category.created', $category, null, $category->toArray());
        return redirect()->route('admin.categories')->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        return view('admin.category-form', $this->formData($category));
    }

    public function update(Request $request, Category $category)
    {
        $before = $category->toArray();
        $category->update($this->validated($request, $category));
        $this->audit($request, 'category.updated', $category, $before, $category->fresh()->toArray());
        return back()->with('success', 'Category updated.');
    }

    public function destroy(Request $request, Category $category)
    {
        $before = $category->toArray();
        DB::transaction(function () use ($category) {
            Category::where('parent_id', $category->id)->update(['parent_id' => $category->parent_id]);
            $category->delete();
        });
        $this->audit($request, 'category.deleted', $category, $before, null);
        return redirect()->route('admin.categories')->with('success', 'Category deleted. Its children were moved up one level.');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'max:120'],
            'slug' => ['required', 'max:120', Rule::unique('categories')->ignore($category)],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'max:500'],
            'image' => ['nullable', 'max:255'],
            'position' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        if ($category && ($data['parent_id'] ?? null) && in_array((int) $data['parent_id'], $this->descendantIds($category), true)) {
            throw ValidationException::withMessages(['parent_id' => 'A category cannot be moved below one of its descendants.']);
        }
        if ($category && (int) ($data['parent_id'] ?? 0) === $category->id) {
            throw ValidationException::withMessages(['parent_id' => 'A category cannot be its own parent.']);
        }
        return $data;
    }

    private function formData(?Category $category = null): array
    {
        $excluded = $category ? [$category->id, ...$this->descendantIds($category)] : [];
        return [
            'category' => $category,
            'parentOptions' => array_filter($this->flattenCategories(), fn ($item) => !in_array($item->id, $excluded, true)),
            'mediaItems' => Media::latest()->take(18)->get(),
        ];
    }

    private function descendantIds(Category $category): array
    {
        $grouped = Category::all()->groupBy('parent_id');
        $ids = [];
        $walk = function ($id) use (&$walk, &$ids, $grouped) {
            foreach ($grouped->get($id, collect()) as $child) {
                $ids[] = $child->id;
                $walk($child->id);
            }
        };
        $walk($category->id);
        return $ids;
    }

    private function flattenCategories(bool $withCount = false): array
    {
        $query = Category::orderBy('position')->orderBy('name');
        $categories = $withCount ? $query->withCount('products')->get() : $query->get();
        $grouped = $categories->groupBy('parent_id');
        $flat = [];
        $walk = function ($parentId, $depth) use (&$walk, &$flat, $grouped) {
            foreach ($grouped->get($parentId, collect()) as $category) {
                $category->depth = $depth;
                $flat[] = $category;
                $walk($category->id, $depth + 1);
            }
        };
        $walk(null, 0);
        return $flat;
    }

    private function audit(Request $request, string $action, Category $category, ?array $before, ?array $after): void
    {
        AuditLog::create(['user_id'=>$request->user()->id,'action'=>$action,'auditable_type'=>Category::class,'auditable_id'=>$category->id,'before'=>$before,'after'=>$after,'ip_address'=>$request->ip()]);
    }
}
