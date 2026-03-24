<?php

declare(strict_types=1);

namespace Billing\Domain\Repository;

use Billing\Domain\Entity\Invoice;
use Ramsey\Uuid\UuidInterface;

interface InvoiceRepository
{
    public function find(UuidInterface $id): ?Invoice;
    public function findBySubscription(UuidInterface $subscriptionId): array;
    public function save(Invoice $invoice): void;
}