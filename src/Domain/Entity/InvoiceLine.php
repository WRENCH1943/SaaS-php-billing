<?php

declare(strict_types=1);

namespace Billing\Domain\Entity;

use Billing\Domain\ValueObject\Money;
use Ramsey\Uuid\UuidInterface;

readonly class InvoiceLine
{
    public function __construct(
        public UuidInterface $id,
        public UuidInterface $invoiceId,
        public string $description,
        public Money $amount
    ) {}
}