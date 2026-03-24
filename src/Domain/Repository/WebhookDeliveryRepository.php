<?php

declare(strict_types=1);

namespace Billing\Domain\Repository;

use Billing\Domain\Entity\WebhookDelivery;
use Ramsey\Uuid\UuidInterface;

interface WebhookDeliveryRepository
{
    public function save(WebhookDelivery $delivery): void;
    public function findPending(): array;
}