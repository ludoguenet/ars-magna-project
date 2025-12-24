<?php

namespace AppModules\Shared\src\ValueObjects;

final readonly class Money
{
    public function __construct(
        private float $amount
    ) {}

    public static function fromDecimal(float $amount): self
    {
        return new self($amount);
    }

    public function toDecimal(): float
    {
        return $this->amount;
    }

    public function multiply(float $multiplier): self
    {
        return new self($this->amount * $multiplier);
    }

    public function add(self $other): self
    {
        return new self($this->amount + $other->amount);
    }

    public function subtract(self $other): self
    {
        return new self($this->amount - $other->amount);
    }
}
