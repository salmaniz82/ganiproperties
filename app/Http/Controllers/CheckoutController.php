<?php
namespace App\Http\Controllers;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
class CheckoutController extends Controller {
    public function store(Request $request, CheckoutService $checkout) {
        $data=$request->validate([
            'customer_name'=>'required|string|max:120','customer_email'=>'nullable|email|max:160','customer_phone'=>'required|string|max:30',
            'line1'=>'required|string|max:250','line2'=>'nullable|string|max:250','delivery_zone_id'=>'required|exists:delivery_zones,id',
            'delivery_date'=>'nullable|date|after_or_equal:today','customer_note'=>'nullable|string|max:1000',
        ]);
        $order=$checkout->place($request,$data);
        return redirect()->route('orders.success',$order)->with('success','Order placed successfully.');
    }
    public function success(\App\Models\Order $order) { return view('store.success',compact('order')); }
}
