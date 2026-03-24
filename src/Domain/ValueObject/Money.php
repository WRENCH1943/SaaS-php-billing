<?php

declare(strict_types=1);

namespace Billing\Domain\ValueObject;

readonly class Money
{
    public function __construct(
        public int $amountInPaise
    ) {
        if ($amountInPaise < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
    }

    public function add(Money $other): self
    {
        return new self($this->amountInPaise + $other->amountInPaise);
    }

    public function multiply(float $factor): self
    {
        return new self((int) round($this->amountInPaise * $factor));
    }

    public function toFloat(): float
    {
        return $this->amountInPaise / 100.0;
    }
}