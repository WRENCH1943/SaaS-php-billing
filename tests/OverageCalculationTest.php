<?php

declare(strict_types=1);

namespace Billing\Tests;

use Billing\Domain\Enum\Plan;
use Billing\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

class OverageCalculationTest extends TestCase
{
    public function testCalculateOverage(): void
    {
        $plan = Plan::STARTER; // max 5
        $userCount = 7;
        $expected = new Money(2 * 19900); // 2 extra * 199

        // Simulate
        $maxUsers = $plan->maxUsers();
        $extraUsers = max(0, $userCount - $maxUsers);
        $actual = new Money($extraUsers * 19900);

        $this->assertEquals($expected->amountInPaise, $actual->amountInPaise);
    }
}