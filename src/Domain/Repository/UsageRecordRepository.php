<?php

declare(strict_types=1);

namespace Billing\Domain\Repository;

use Billing\Domain\Entity\UsageRecord;
use Ramsey\Uuid\UuidInterface;

interface UsageRecordRepository
{
    public function save(UsageRecord $record): void;
    public function getLatestForTenant(UuidInterface $tenantId): ?UsageRecord;
    public function getForPeriod(UuidInterface $tenantId, \DateTimeImmutable $start, \DateTimeImmutable $end): array;
}