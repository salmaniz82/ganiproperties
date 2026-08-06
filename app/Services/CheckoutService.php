<?php
namespace App\Services;
use App\Models\{DeliveryZone, Order, Payment, Product, Shipment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class CheckoutService {
    public function __construct(private CartService $cart, private CashOnDeliveryProvider $payments, private InternalDeliveryProvider $delivery) {}
    public function place(Request $request, array $data): Order
    {
        return DB::transaction(function () use ($request, $data) {
            $items = $this->cart->items($request);
            if (!$items) throw ValidationException::withMessages(['cart'=>'Your cart is empty.']);
            $zone = DeliveryZone::where('id',$data['delivery_zone_id'])->where('is_active',true)->firstOrFail();
            foreach ($items as &$item) {
                $locked = Product::lockForUpdate()->findOrFail($item['product']->id);
                if ($locked->track_inventory && $locked->available_stock < $item['quantity']) {
                    throw ValidationException::withMessages(['cart'=>"{$locked->name} no longer has enough stock."]);
                }
                if ($locked->track_inventory) $locked->decrement('stock', $item['quantity']);
                $item['product'] = $locked;
                $item['unit_price'] = (int) $locked->price;
                $item['line_total'] = (int) $locked->price * $item['quantity'];
            }
            unset($item);
            $subtotal = collect($items)->sum('line_total');
            $deliveryFee = (int) $zone->fee;
            $discountTotal = 0;
            $total = $subtotal + $deliveryFee - $discountTotal;
            $order = Order::create([
                'number'=>'PP'.now()->format('ymd').strtoupper(substr(bin2hex(random_bytes(4)),0,6)),
                'user_id'=>$request->user()?->id,'delivery_zone_id'=>$zone->id,
                'customer_name'=>$data['customer_name'],'customer_email'=>$data['customer_email'] ?? null,'customer_phone'=>$data['customer_phone'],
                'shipping_address'=>['line1'=>$data['line1'],'line2'=>$data['line2'] ?? null,'city'=>$zone->city],
                'subtotal'=>$subtotal,'delivery_fee'=>$deliveryFee,'discount_total'=>$discountTotal,'total'=>$total,'payment_method'=>'cod',
                'delivery_date'=>$data['delivery_date'] ?? null,'customer_note'=>$data['customer_note'] ?? null,
            ]);
            foreach ($items as $item) $order->items()->create([
                'product_id'=>$item['product']->id,'name'=>$item['product']->name,'sku'=>$item['product']->sku,'image'=>$item['product']->image,
                'unit_price'=>$item['product']->price,'quantity'=>$item['quantity'],'total'=>$item['line_total'],'customizations'=>$item['customizations'],
            ]);
            Payment::create(['order_id'=>$order->id,'provider'=>'cod','amount'=>$order->total,...$this->payments->initialize($order)]);
            Shipment::create(['order_id'=>$order->id,...$this->delivery->createShipment($order)]);
            $request->session()->forget('cart');
            return $order->load('items','payment','shipment');
        });
    }
}
