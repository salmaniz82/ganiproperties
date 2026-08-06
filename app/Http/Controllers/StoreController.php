<?php
namespace App\Http\Controllers;
use App\Models\{Category, DeliveryZone, Media, Order, Product};
use App\Services\CartService;
use Illuminate\Http\Request;
class StoreController extends Controller {
    public function home() {
        $categories = Category::where('is_active', true)->orderBy('position')->get();
        $products = Product::with('category')->where('is_active', true)->where('is_featured', true)->get();
        $this->primeProductMedia($products);

        return view('store.home', [
            'categories' => $categories,
            'categoryMap' => $categories->keyBy('slug'),
            'featured' => $products->take(6),
            'birthdayProducts' => $products->where('category.slug', 'birthday-decorations')->take(5),
            'flowerProducts' => $products->where('category.slug', 'flowers')->take(3),
            'chocolateProducts' => $products->where('category.slug', 'chocolate-bouquets')->take(3),
            'cakeProducts' => $products->where('category.slug', 'cakes')->take(3),
        ]);
    }
    public function catalog(Request $request, ?Category $category = null, int $page = 1) {
        $queryPage = (int) $request->query('page');
        if ($queryPage > 0) {
            return redirect()->to($this->catalogUrl($category, $queryPage, $request->except('page')));
        }

        if ($page < 2 && $request->route('page')) {
            return redirect()->to($this->catalogUrl($category, 1, $request->except('page')));
        }

        $products = Product::with('category')->where('is_active',true)
            ->when($category, fn($q)=>$q->where('category_id',$category->id))
            ->when($request->q, fn($q,$term)=>$q->where(fn($q)=>$q->where('name','like',"%$term%")->orWhere('description','like',"%$term%")->orWhere('sku','like',"%$term%")))
            ->latest()->paginate(12, ['*'], 'page', max(1, $page))->withQueryString();
        $this->primeProductMedia($products);
        return view('store.catalog', compact('products','category'));
    }
    private function catalogUrl(?Category $category, int $page = 1, array $query = []): string {
        $url = $category
            ? ($page > 1 ? route('category.page', [$category, $page]) : route('category.show', $category))
            : ($page > 1 ? route('shop.page', $page) : route('shop'));

        return $query ? $url.'?'.http_build_query($query) : $url;
    }
    public function product(Product $product) { abort_unless($product->is_active,404); $this->primeProductMedia([$product]); return view('store.product', compact('product')); }
    public function cart(Request $request, CartService $cart) { return view('store.cart', $cart->summary($request)); }
    public function addCart(Request $request, Product $product, CartService $cart) {
        $data=$request->validate(['quantity'=>'required|integer|min:1|max:99','custom_text'=>'nullable|string|max:250','custom_option'=>'nullable|string|max:100','custom_image'=>'nullable|image|max:4096']);
        $custom=array_filter(['text'=>$data['custom_text']??null,'option'=>$data['custom_option']??null]);
        if ($request->hasFile('custom_image')) $custom['image']=$request->file('custom_image')->store('customizations','local');
        $cart->add($request,$product,$data['quantity'],$custom);
        if ($request->expectsJson()) return response()->json(['message'=>'Added to cart.','cart'=>$cart->payload($request)]);
        return redirect()->route('cart')->with('success','Added to cart.');
    }
    public function updateCart(Request $request, string $key, CartService $cart) { $data=$request->validate(['quantity'=>'required|integer|min:0|max:99']); $cart->update($request,$key,$data['quantity']); if($request->expectsJson()) return response()->json(['message'=>'Cart updated.','cart'=>$cart->payload($request)]); return back()->with('success','Cart updated.'); }
    public function removeCart(Request $request, string $key, CartService $cart) { $cart->remove($request,$key); if($request->expectsJson()) return response()->json(['message'=>'Item removed.','cart'=>$cart->payload($request)]); return back()->with('success','Item removed.'); }
    public function clearCart(Request $request, CartService $cart) { $cart->clear($request); return back()->with('success','Cart cleared.'); }
    public function checkout(Request $request, CartService $cart) {
        $zones=DeliveryZone::where('is_active',true)->orderBy('name')->get();
        $selectedZone=$zones->firstWhere('id',(int)$request->old('delivery_zone_id')) ?? $zones->first();
        return view('store.checkout', [...$cart->summary($request,$selectedZone),'zones'=>$zones,'selectedZone'=>$selectedZone]);
    }
    public function track(Request $request) {
        $order=null;
        if ($request->filled('number')) $order=Order::with('items','shipment')->where('number',$request->number)->where(fn($q)=>$q->where('customer_phone',$request->identity)->orWhere('customer_email',$request->identity))->first();
        return view('store.track',compact('order'));
    }
    private function primeProductMedia(iterable $products): void {
        $items = $products instanceof \Illuminate\Pagination\AbstractPaginator ? $products->items() : $products;
        $paths = collect($items)->flatMap(fn (Product $product) => [$product->image, ...($product->gallery_images ?? [])]);
        Media::primeVariantCache($paths);
    }
}
