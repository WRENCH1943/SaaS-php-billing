<?php

declare(strict_types=1);

namespace Billing\Domain\Entity;

use Ramsey\Uuid\UuidInterface;

readonly class Tenant
{
    public function __construct(
        public UuidInterface $id,
        public string $name,
        public \DateTimeImmutable $createdAt
    ) {}
}