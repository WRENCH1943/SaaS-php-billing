<?php

declare(strict_types=1);

namespace Billing\Domain\Repository;

use Billing\Domain\Entity\WebhookEndpoint;
use Ramsey\Uuid\UuidInterface;

interface WebhookEndpointRepository
{
    public function findByTenant(UuidInterface $tenantId): array;
    public function save(WebhookEndpoint $endpoint): void;
}