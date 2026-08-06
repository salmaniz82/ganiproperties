<?php
namespace App\Services;
use App\Contracts\PaymentProvider;
use App\Models\Order;
class CashOnDeliveryProvider implements PaymentProvider {
    public function initialize(Order $order): array { return ['status'=>'pending','reference'=>'COD-'.$order->number]; }
    public function verify(string $reference, array $payload = []): bool { return false; }
    public function refund(Order $order, int $amount): array { return ['status'=>'not_applicable']; }
    public function handleWebhook(array $payload): void {}
}
