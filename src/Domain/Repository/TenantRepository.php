<?php

declare(strict_types=1);

namespace Billing\Domain\Repository;

use Billing\Domain\Entity\Tenant;
use Ramsey\Uuid\UuidInterface;

interface TenantRepository
{
    public function find(UuidInterface $id): ?Tenant;
    public function save(Tenant $tenant): void;
}