<?php

declare(strict_types=1);

namespace Billing\Domain\Entity;

use Billing\Domain\Enum\Plan;
use Billing\Domain\Enum\SubscriptionStatus;
use Ramsey\Uuid\UuidInterface;

readonly class Subscription
{
    public function __construct(
        public UuidInterface $id,
        public UuidInterface $tenantId,
        public Plan $plan,
        public SubscriptionStatus $status,
        public \DateTimeImmutable $currentPeriodStart,
        public \DateTimeImmutable $currentPeriodEnd,
        public ?\DateTimeImmutable $trialEnd,
        public \DateTimeImmutable $createdAt
    ) {}
}