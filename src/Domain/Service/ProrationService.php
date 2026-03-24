<?php

declare(strict_types=1);

namespace Billing\Domain\Service;

use Billing\Domain\Enum\Plan;
use Billing\Domain\ValueObject\Money;

class ProrationService
{
    public function calculateProratedAmount(
        Plan $oldPlan,
        Plan $newPlan,
        \DateTimeImmutable $changeDate,
        \DateTimeImmutable $periodStart,
        \DateTimeImmutable $periodEnd
    ): Money {
        $oldPrice = new Money($oldPlan->priceInPaise());
        $newPrice = new Money($newPlan->priceInPaise());

        $totalDays = $periodStart->diff($periodEnd)->days;
        $remainingDays = $changeDate->diff($periodEnd)->days;

        $oldProrated = $oldPrice->multiply($remainingDays / $totalDays);
        $newProrated = $newPrice->multiply(($totalDays - $remainingDays) / $totalDays);

        return $newProrated->add($oldProrated);
    }
}