<?php

declare(strict_types=1);

namespace Billing\Infrastructure\Payment;

use Billing\Domain\ValueObject\Money;

class MockGateway implements PaymentGatewayInterface
{
    public function charge(string $customerId, Money $amount): array
    {
        // Simulate success/failure
        $success = rand(0, 10) > 1; // 90% success

        return [
            'success' => $success,
            'charge_id' => $success ? 'ch_' . uniqid() : null,
            'error' => $success ? null : 'Card declined',
        ];
    }

    public function refund(string $chargeId, Money $amount): array
    {
        return [
            'success' => true,
            'refund_id' => 'ref_' . uniqid(),
        ];
    }

    public function createCustomer(array $data): string
    {
        return 'cus_' . uniqid();
    }
}