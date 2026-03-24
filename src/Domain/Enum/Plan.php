<?php

declare(strict_types=1);

namespace Billing\Domain\Enum;

enum Plan: string
{
    case STARTER = 'starter';
    case GROWTH = 'growth';
    case ENTERPRISE = 'enterprise';

    public function priceInPaise(): int
    {
        return match ($this) {
            self::STARTER => 99900, // ₹999
            self::GROWTH => 299900, // ₹2,999
            self::ENTERPRISE => 0, // Custom
        };
    }

    public function maxUsers(): ?int
    {
        return match ($this) {
            self::STARTER => 5,
            self::GROWTH => 25,
            self::ENTERPRISE => null, // Unlimited
        };
    }
}