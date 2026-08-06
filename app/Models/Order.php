<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Order extends Model {
    protected $guarded = [];
    protected $casts = ['shipping_address'=>'array','delivery_date'=>'date'];
    public function items() { return $this->hasMany(OrderItem::class); }
    public function payment() { return $this->hasOne(Payment::class); }
    public function shipment() { return $this->hasOne(Shipment::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function zone() { return $this->belongsTo(DeliveryZone::class, 'delivery_zone_id'); }
}
