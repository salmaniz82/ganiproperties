<?php
namespace App\Http\Controllers;

use App\Models\{AuditLog, Category, Media, Product, ProductVariant};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->q, fn ($query, $term) => $query->where(fn ($query) => $query->where('name', 'like', "%$term%")->orWhere('sku', 'like', "%$term%")))
            ->latest()->paginate(20)->withQueryString();
        Media::primeVariantCache($products->pluck('image'));

        return view('admin.products', compact('products'));
    }

    public function create()
    {
        return view('admin.product-form', $this->formData());
    }

    public function store(Request $request)
    {
        [$data, $variants] = $this->validated($request);
        $product = Product::create($data);
        $this->syncVariants($product, $variants);
        $this->audit($request, 'product.created', $product, null, $product->fresh('variants')->toArray());
        return redirect()->route('admin.products.edit', $product)->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $product->load('variants');
        return view('admin.product-form', $this->formData($product));
    }

    public function update(Request $request, Product $product)
    {
        [$data, $variants] = $this->validated($request, $product);
        $before = $product->load('variants')->toArray();
        $product->update($data);
        $this->syncVariants($product, $variants);
        $this->audit($request, 'product.updated', $product, $before, $product->fresh()->toArray());
        return back()->with('success', 'Product updated.');
    }

    public function destroy(Request $request, Product $product)
    {
        $before = $product->toArray();
        $product->delete();
        $this->audit($request, 'product.deleted', $product, $before, null);
        return redirect()->route('admin.products')->with('success', 'Product deleted.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $request->merge(['product_type' => $request->input('product_type', 'simple')]);
        $data = $request->validate([
            'name' => ['required', 'max:180'],
            'slug' => ['required', 'max:180', Rule::unique('products')->ignore($product)],
            'sku' => ['required', 'max:80', Rule::unique('products')->ignore($product)],
            'product_type' => ['required', Rule::in(['simple', 'variable'])],
            'category_id' => ['nullable', 'exists:categories,id'],
            'price' => ['required', 'integer', 'min:0'],
            'discount_price' => ['nullable', 'integer', 'min:0'],
            'compare_at_price' => ['nullable', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'string', 'max:255'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'meta_keywords' => ['nullable', 'string', 'max:1000'],
            'seo_schema' => ['nullable', 'json'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'track_inventory' => ['nullable', 'boolean'],
            'variant_same_pricing' => ['nullable', 'boolean'],
            'variant_options' => ['nullable', 'array'],
            'variant_options.*.name' => ['nullable', 'string', 'max:80'],
            'variant_options.*.values' => ['nullable', 'string', 'max:500'],
            'variants' => ['nullable', 'array'],
            'variants.*.name' => ['required_with:variants', 'string', 'max:120'],
            'variants.*.sku' => ['required_with:variants', 'string', 'max:100', 'distinct'],
            'variants.*.price' => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.discount_price' => ['nullable', 'integer', 'min:0'],
            'variants.*.stock' => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.options' => ['nullable', 'json'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['track_inventory'] = $request->boolean('track_inventory');
        $data['variant_same_pricing'] = $request->boolean('variant_same_pricing');
        $data['seo_schema'] = filled($data['seo_schema'] ?? null) ? json_decode($data['seo_schema'], true) : null;
        $data['gallery_images'] = array_values(array_filter($data['gallery_images'] ?? []));

        $variants = $data['product_type'] === 'variable' ? $this->validatedVariants($data['variants'] ?? [], $product) : [];
        $data['variant_options'] = $data['product_type'] === 'variable' ? $this->cleanVariantOptions($data['variant_options'] ?? []) : null;
        unset($data['variants']);

        return [$data, $variants];
    }

    private function formData(?Product $product = null): array
    {
        return [
            'product' => $product,
            'categories' => $this->flattenCategories(),
            'mediaItems' => Media::latest()->take(18)->get(),
        ];
    }

    private function cleanVariantOptions(array $options): array
    {
        return collect($options)
            ->map(fn ($option) => [
                'name' => trim($option['name'] ?? ''),
                'values' => collect(preg_split('/[,|]/', $option['values'] ?? ''))->map(fn ($value) => trim($value))->filter()->unique()->implode(', '),
            ])
            ->filter(fn ($option) => $option['name'] !== '' && $option['values'] !== '')
            ->values()->all();
    }

    private function validatedVariants(array $rows, ?Product $product): array
    {
        $variants = collect($rows)->map(function ($row) {
            return [
                'name' => trim($row['name']),
                'sku' => trim($row['sku']),
                'price' => (int) $row['price'],
                'discount_price' => filled($row['discount_price'] ?? null) ? (int) $row['discount_price'] : null,
                'stock' => (int) $row['stock'],
                'options' => filled($row['options'] ?? null) ? json_decode($row['options'], true) : [],
            ];
        })->values()->all();

        if ($variants === []) {
            throw ValidationException::withMessages(['variants' => 'Generate at least one variant for a variable product.']);
        }

        $skus = array_column($variants, 'sku');
        $conflict = ProductVariant::whereIn('sku', $skus)
            ->when($product, fn ($query) => $query->where('product_id', '!=', $product->id))
            ->exists();
        if ($conflict) {
            throw ValidationException::withMessages(['variants' => 'One or more variant SKUs are already used by another product.']);
        }

        return $variants;
    }

    private function syncVariants(Product $product, array $variants): void
    {
        $product->variants()->delete();
        foreach ($variants as $variant) {
            $product->variants()->create($variant);
        }
    }

    private function flattenCategories(): array
    {
        $grouped = Category::orderBy('position')->orderBy('name')->get()->groupBy('parent_id');
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

    private function audit(Request $request, string $action, Product $product, ?array $before, ?array $after): void
    {
        AuditLog::create(['user_id'=>$request->user()->id,'action'=>$action,'auditable_type'=>Product::class,'auditable_id'=>$product->id,'before'=>$before,'after'=>$after,'ip_address'=>$request->ip()]);
    }
}
