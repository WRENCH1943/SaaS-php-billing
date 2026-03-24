<?php

declare(strict_types=1);

namespace Billing\Tests;

use Billing\Domain\Enum\Plan;
use Billing\Domain\Service\ProrationService;
use Billing\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

class ProrationServiceTest extends TestCase
{
    private ProrationService $service;

    protected function setUp(): void
    {
        $this->service = new ProrationService();
    }

    public function testCalculateProratedAmount(): void
    {
        $oldPlan = Plan::STARTER;
        $newPlan = Plan::GROWTH;
        $changeDate = new \DateTimeImmutable('2023-01-15');
        $periodStart = new \DateTimeImmutable('2023-01-01');
        $periodEnd = new \DateTimeImmutable('2023-02-01');

        $result = $this->service->calculateProratedAmount($oldPlan, $newPlan, $changeDate, $periodStart, $periodEnd);

        // 17 days old plan, 14 days new plan
        $expected = 190223;

        $this->assertEquals($expected, $result->amountInPaise);
    }
}