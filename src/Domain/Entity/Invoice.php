<?php

declare(strict_types=1);

namespace Billing\Domain\Entity;

use Billing\Domain\Enum\InvoiceStatus;
use Billing\Domain\ValueObject\Money;
use Ramsey\Uuid\UuidInterface;

readonly class Invoice
{
    public function __construct(
        public UuidInterface $id,
        public UuidInterface $subscriptionId,
        public Money $amount,
        public InvoiceStatus $status,
        public \DateTimeImmutable $dueDate,
        public \DateTimeImmutable $createdAt
    ) {}
}