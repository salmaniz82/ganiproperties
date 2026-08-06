<?php
namespace App\Contracts;
use App\Models\Order;
interface PaymentProvider {
    public function initialize(Order $order): array;
    public function verify(string $reference, array $payload = []): bool;
    public function refund(Order $order, int $amount): array;
    public function handleWebhook(array $payload): void;
}
