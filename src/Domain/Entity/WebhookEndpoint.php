<?php

declare(strict_types=1);

namespace Billing\Domain\Entity;

use Ramsey\Uuid\UuidInterface;

readonly class WebhookEndpoint
{
    public function __construct(
        public UuidInterface $id,
        public UuidInterface $tenantId,
        public string $url,
        public string $secret,
        public \DateTimeImmutable $createdAt
    ) {}
}