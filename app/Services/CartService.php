<?php

namespace App\Services;

use App\Models\DeliveryZone;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function items(Request $request): array
    {
        $cart = $request->session()->get('cart', []);
        $items = [];
        $changed = false;

        foreach ($cart as $key => $row) {
            $product = Product::find($row['product_id'] ?? null);

            if (! $product || ! $product->is_active) {
                unset($cart[$key]);
                $changed = true;
                continue;
            }

            $quantity = max(1, (int) ($row['quantity'] ?? 1));
            $unitPrice = (int) $product->price;

            $items[] = [
                'key' => $key,
                'product' => $product,
                'quantity' => $quantity,
                'customizations' => $row['customizations'] ?? [],
                'unit_price' => $unitPrice,
                'line_total' => $unitPrice * $quantity,
            ];
        }

        if ($changed) {
            $request->session()->put('cart', $cart);
        }

        return $items;
    }

    public function summary(Request $request, ?DeliveryZone $zone = null): array
    {
        $items = $this->items($request);
        $subtotal = array_sum(array_column($items, 'line_total'));
        $deliveryFee = $zone?->is_active ? (int) $zone->fee : 0;

        return [
            'items' => $items,
            'line_count' => count($items),
            'item_count' => array_sum(array_column($items, 'quantity')),
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'discount_total' => 0,
            'total' => $subtotal + $deliveryFee,
        ];
    }

    public function payload(Request $request): array
    {
        $summary = $this->summary($request);

        return [
            'item_count' => $summary['item_count'],
            'line_count' => $summary['line_count'],
            'subtotal' => $summary['subtotal'],
            'subtotal_formatted' => $this->formatPrice($summary['subtotal']),
            'cart_url' => route('cart'),
            'checkout_url' => route('checkout'),
            'items' => array_map(fn (array $item) => [
                'key' => $item['key'],
                'name' => $item['product']->name,
                'image' => asset($item['product']->image ?: 'images/prod-arch.jpg'),
                'url' => route('products.show', $item['product']),
                'quantity' => $item['quantity'],
                'available_stock' => $item['product']->track_inventory ? $item['product']->available_stock : 99,
                'unit_price' => $item['unit_price'],
                'unit_price_formatted' => $this->formatPrice($item['unit_price']),
                'line_total' => $item['line_total'],
                'line_total_formatted' => $this->formatPrice($item['line_total']),
                'update_url' => route('cart.update', $item['key']),
                'remove_url' => route('cart.remove', $item['key']),
            ], $summary['items']),
        ];
    }

    public function add(Request $request, Product $product, int $quantity, array $customizations = []): string
    {
        $this->ensurePurchasable($product);
        $cart = $request->session()->get('cart', []);
        $key = $this->lineKey($product, $customizations);
        $newQuantity = (int) ($cart[$key]['quantity'] ?? 0) + $quantity;
        $this->ensureQuantityAvailable($product, $newQuantity);

        $cart[$key] = [
            'product_id' => $product->id,
            'quantity' => $newQuantity,
            'customizations' => $customizations,
        ];
        $request->session()->put('cart', $cart);

        return $key;
    }

    public function update(Request $request, string $key, int $quantity): void
    {
        $cart = $request->session()->get('cart', []);

        if (! isset($cart[$key])) {
            throw ValidationException::withMessages(['cart' => 'That cart item no longer exists.']);
        }

        if ($quantity === 0) {
            $this->remove($request, $key);
            return;
        }

        $product = Product::find($cart[$key]['product_id']);
        if (! $product) {
            $this->remove($request, $key);
            throw ValidationException::withMessages(['cart' => 'That product is no longer available.']);
        }

        $this->ensurePurchasable($product);
        $this->ensureQuantityAvailable($product, $quantity);
        $cart[$key]['quantity'] = $quantity;
        $request->session()->put('cart', $cart);
    }

    public function remove(Request $request, string $key): void
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$key]);
        $request->session()->put('cart', $cart);
    }

    public function clear(Request $request): void
    {
        $request->session()->forget('cart');
    }

    private function lineKey(Product $product, array $customizations): string
    {
        ksort($customizations);

        return $product->id.'-'.hash('sha256', json_encode($customizations));
    }

    private function ensurePurchasable(Product $product): void
    {
        if (! $product->is_active) {
            throw ValidationException::withMessages(['cart' => 'This product is no longer available.']);
        }
    }

    private function ensureQuantityAvailable(Product $product, int $quantity): void
    {
        if ($product->track_inventory && $quantity > $product->available_stock) {
            throw ValidationException::withMessages([
                'quantity' => "Only {$product->available_stock} unit(s) of {$product->name} are available.",
            ]);
        }
    }

    private function formatPrice(int $amount): string
    {
        return 'PKR '.number_format($amount);
    }
}
