<?php
namespace App\Contracts;
use App\Models\Order;
interface DeliveryProvider {
    public function createShipment(Order $order): array;
    public function track(string $trackingNumber): array;
    public function cancel(string $trackingNumber): bool;
}
