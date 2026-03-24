<?php

declare(strict_types=1);

namespace Billing\Domain\Entity;

use Billing\Domain\Enum\WebhookEvent;
use Ramsey\Uuid\UuidInterface;

readonly class WebhookDelivery
{
    public function __construct(
        public UuidInterface $id,
        public UuidInterface $endpointId,
        public WebhookEvent $event,
        public string $payload,
        public string $status, // 'pending', 'delivered', 'failed'
        public int $attempts,
        public ?\DateTimeImmutable $deliveredAt
    ) {}
}