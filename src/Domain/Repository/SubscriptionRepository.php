<?php

declare(strict_types=1);

namespace Billing\Domain\Repository;

use Billing\Domain\Entity\Subscription;
use Ramsey\Uuid\UuidInterface;

interface SubscriptionRepository
{
    public function find(UuidInterface $id): ?Subscription;
    public function findByTenant(UuidInterface $tenantId): array;
    public function save(Subscription $subscription): void;
    public function findDueForRenewal(\DateTimeImmutable $now): array;
}