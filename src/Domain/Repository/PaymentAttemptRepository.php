<?php

declare(strict_types=1);

namespace Billing\Domain\Repository;

use Billing\Domain\Entity\PaymentAttempt;
use Ramsey\Uuid\UuidInterface;

interface PaymentAttemptRepository
{
    public function save(PaymentAttempt $attempt): void;
    public function findByInvoice(UuidInterface $invoiceId): array;
}