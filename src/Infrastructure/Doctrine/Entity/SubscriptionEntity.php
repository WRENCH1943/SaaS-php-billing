<?php

declare(strict_types=1);

namespace Billing\Infrastructure\Doctrine\Entity;

use Billing\Domain\Enum\Plan;
use Billing\Domain\Enum\SubscriptionStatus;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'subscriptions')]
#[ORM\HasLifecycleCallbacks]
class SubscriptionEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $id;

    #[ORM\Column(type: 'uuid')]
    private string $tenantId;

    #[ORM\Column(type: 'string', enumType: Plan::class)]
    private Plan $plan;

    #[ORM\Column(type: 'string', enumType: SubscriptionStatus::class)]
    private SubscriptionStatus $status;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $currentPeriodStart;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $currentPeriodEnd;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $trialEnd;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        string $tenantId,
        Plan $plan,
        SubscriptionStatus $status,
        \DateTimeImmutable $currentPeriodStart,
        \DateTimeImmutable $currentPeriodEnd,
        ?\DateTimeImmutable $trialEnd
    ) {
        $this->id = Uuid::uuid4()->toString();
        $this->tenantId = $tenantId;
        $this->plan = $plan;
        $this->status = $status;
        $this->currentPeriodStart = $currentPeriodStart;
        $this->currentPeriodEnd = $currentPeriodEnd;
        $this->trialEnd = $trialEnd;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTenantId(): string
    {
        return $this->tenantId;
    }

    public function getPlan(): Plan
    {
        return $this->plan;
    }

    public function getStatus(): SubscriptionStatus
    {
        return $this->status;
    }

    public function getCurrentPeriodStart(): \DateTimeImmutable
    {
        return $this->currentPeriodStart;
    }

    public function getCurrentPeriodEnd(): \DateTimeImmutable
    {
        return $this->currentPeriodEnd;
    }

    public function getTrialEnd(): ?\DateTimeImmutable
    {
        return $this->trialEnd;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setStatus(SubscriptionStatus $status): void
    {
        $this->status = $status;
    }

    public function setCurrentPeriodStart(\DateTimeImmutable $start): void
    {
        $this->currentPeriodStart = $start;
    }

    public function setCurrentPeriodEnd(\DateTimeImmutable $end): void
    {
        $this->currentPeriodEnd = $end;
    }
}