<?php
namespace App\Http\Controllers;
use App\Models\{AuditLog, DeliveryZone, Order, Product, Promotion, User};
use Illuminate\Http\Request;
class AdminController extends Controller {
    public function dashboard() { return view('admin.dashboard',['orders'=>Order::latest()->take(8)->get(),'orderCount'=>Order::count(),'revenue'=>Order::whereNot('status','cancelled')->sum('total'),'productCount'=>Product::count(),'customerCount'=>User::where('is_admin',false)->count()]); }
    public function orders() { return view('admin.orders',['orders'=>Order::with('items')->latest()->paginate(25)]); }
    public function updateOrder(Request $request, Order $order) {
        $data=$request->validate(['status'=>'required|in:pending,confirmed,processing,completed,cancelled','payment_status'=>'required|in:pending,paid,failed,refunded','fulfillment_status'=>'required|in:unfulfilled,preparing,shipped,delivered,cancelled','internal_note'=>'nullable|string|max:2000']);
        $before=$order->toArray(); $order->update($data); $this->audit($request,'order.updated',$order,$before,$order->fresh()->toArray()); return back()->with('success','Order updated.');
    }
    public function zones() { return view('admin.zones',['zones'=>DeliveryZone::latest()->get()]); }
    public function saveZone(Request $request) { $data=$request->validate(['name'=>'required|max:120','city'=>'required|max:120','fee'=>'required|integer|min:0','minimum_days'=>'required|integer|min:0','same_day_cutoff'=>'nullable','same_day_enabled'=>'nullable|boolean','is_active'=>'nullable|boolean']); $data['same_day_enabled']=$request->boolean('same_day_enabled');$data['is_active']=$request->boolean('is_active');$zone=DeliveryZone::create($data);$this->audit($request,'delivery_zone.created',$zone,null,$zone->toArray());return back()->with('success','Delivery zone added.'); }
    public function customers() { return view('admin.customers',['customers'=>User::where('is_admin',false)->withCount('orders')->latest()->paginate(25)]); }
    public function settings() { return view('admin.settings',['promotions'=>Promotion::latest()->get(),'logs'=>AuditLog::latest()->take(25)->get()]); }
    public function savePromotion(Request $request) { $data=$request->validate(['name'=>'required|max:120','code'=>'nullable|max:50|unique:promotions','type'=>'required|in:fixed,percentage','value'=>'required|integer|min:1','starts_at'=>'nullable|date','ends_at'=>'nullable|date|after:starts_at']); $promotion=Promotion::create($data+['is_active'=>true]); $this->audit($request,'promotion.created',$promotion,null,$promotion->toArray()); return back()->with('success','Promotion added.'); }
    private function audit(Request $request,string $action,$model,?array $before,?array $after): void { AuditLog::create(['user_id'=>$request->user()->id,'action'=>$action,'auditable_type'=>get_class($model),'auditable_id'=>$model->id,'before'=>$before,'after'=>$after,'ip_address'=>$request->ip()]); }
}
