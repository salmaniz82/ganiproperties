<?php
namespace App\Services;
use App\Contracts\DeliveryProvider;
use App\Models\Order;
class InternalDeliveryProvider implements DeliveryProvider {
    public function createShipment(Order $order): array { return ['provider'=>'internal','tracking_number'=>'PP-'.$order->number,'status'=>'pending']; }
    public function track(string $trackingNumber): array { return ['tracking_number'=>$trackingNumber,'status'=>'managed_internally']; }
    public function cancel(string $trackingNumber): bool { return true; }
}
