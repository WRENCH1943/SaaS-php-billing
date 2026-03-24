<?php

declare(strict_types=1);

namespace Billing\Domain\Entity;

use Ramsey\Uuid\UuidInterface;

readonly class PaymentAttempt
{
    public function __construct(
        public UuidInterface $id,
        public UuidInterface $invoiceId,
        public string $gatewayResponse,
        public string $status, // 'success', 'failed'
        public \DateTimeImmutable $attemptedAt
    ) {}
}