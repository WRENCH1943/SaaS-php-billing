<?php

declare(strict_types=1);

namespace Billing\Domain\Service;

use Billing\Domain\Entity\Invoice;
use Billing\Domain\Entity\InvoiceLine;
use Billing\Domain\Entity\Subscription;
use Billing\Domain\Entity\UsageRecord;
use Billing\Domain\Enum\InvoiceStatus;
use Billing\Domain\Enum\SubscriptionStatus;
use Billing\Domain\Repository\InvoiceRepository;
use Billing\Domain\Repository\SubscriptionRepository;
use Billing\Domain\Repository\UsageRecordRepository;
use Billing\Domain\ValueObject\Money;
use Ramsey\Uuid\Uuid;

class BillingCycleService
{
    public function __construct(
        private SubscriptionRepository $subscriptionRepo,
        private InvoiceRepository $invoiceRepo,
        private UsageRecordRepository $usageRepo,
        private ProrationService $prorationService
    ) {}

    public function renewSubscriptions(\DateTimeImmutable $now): void
    {
        $dueSubscriptions = $this->subscriptionRepo->findDueForRenewal($now);

        foreach ($dueSubscriptions as $subscription) {
            $this->renewSubscription($subscription, $now);
        }
    }

    private function renewSubscription(Subscription $subscription, \DateTimeImmutable $now): void
    {
        // Calculate overage
        $usage = $this->usageRepo->getLatestForTenant($subscription->tenantId);
        $overageAmount = $this->calculateOverage($subscription, $usage);

        // Create invoice
        $planAmount = new Money($subscription->plan->priceInPaise());
        $totalAmount = $planAmount->add($overageAmount);

        $invoice = new Invoice(
            Uuid::uuid4(),
            $subscription->id,
            $totalAmount,
            InvoiceStatus::OPEN,
            $now->modify('+30 days'),
            $now
        );

        $this->invoiceRepo->save($invoice);

        // Add line items
        $planLine = new InvoiceLine(
            Uuid::uuid4(),
            $invoice->id,
            "Plan: {$subscription->plan->value}",
            $planAmount
        );
        // Save line, but since readonly, perhaps repository for lines

        if ($overageAmount->amountInPaise > 0) {
            $overageLine = new InvoiceLine(
                Uuid::uuid4(),
                $invoice->id,
                'Overage charges',
                $overageAmount
            );
        }

        // Update subscription period
        $newSubscription = new Subscription(
            $subscription->id,
            $subscription->tenantId,
            $subscription->plan,
            $subscription->status,
            $subscription->currentPeriodEnd,
            $subscription->currentPeriodEnd->modify('+1 month'),
            null,
            $subscription->createdAt
        );

        $this->subscriptionRepo->save($newSubscription);
    }

    private function calculateOverage(Subscription $subscription, ?UsageRecord $usage): Money
    {
        if (!$usage || !$subscription->plan->maxUsers()) {
            return new Money(0);
        }

        $maxUsers = $subscription->plan->maxUsers();
        $extraUsers = max(0, $usage->userCount - $maxUsers);

        return new Money($extraUsers * 19900); // ₹199 per user
    }
}