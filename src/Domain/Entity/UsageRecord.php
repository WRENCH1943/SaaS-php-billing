<?php

declare(strict_types=1);

namespace Billing\Domain\Entity;

use Ramsey\Uuid\UuidInterface;

readonly class UsageRecord
{
    public function __construct(
        public UuidInterface $id,
        public UuidInterface $tenantId,
        public int $userCount,
        public \DateTimeImmutable $recordedAt
    ) {}
}