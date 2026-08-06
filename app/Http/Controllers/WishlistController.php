<?php
namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
class WishlistController extends Controller {
    public function toggle(Request $request, Product $product) { $request->user()->wishlistProducts()->toggle($product); return back()->with('success','Wishlist updated.'); }
}
