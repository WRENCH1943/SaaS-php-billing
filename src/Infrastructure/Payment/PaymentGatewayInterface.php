<?php

declare(strict_types=1);

namespace Billing\Infrastructure\Payment;

use Billing\Domain\ValueObject\Money;

interface PaymentGatewayInterface
{
    public function charge(string $customerId, Money $amount): array;
    public function refund(string $chargeId, Money $amount): array;
    public function createCustomer(array $data): string;
}