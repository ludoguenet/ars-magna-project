<?php

namespace AppModules\Shared\src\ValueObjects;

use InvalidArgumentException;

final readonly class Money
{
    public function __construct(
        public int $amount, // Amount in cents
        public string $currency = 'EUR'
    ) {
        if ($amount < 0) {
            throw new InvalidArgumentException('Amount cannot be negative');
        }

        if (strlen($currency) !== 3) {
            throw new InvalidArgumentException('Currency must be a 3-letter code');
        }
    }

    /**
     * Create from a decimal amount.
     */
    public static function fromDecimal(float $amount, string $currency = 'EUR'): self
    {
        return new self((int) round($amount * 100), $currency);
    }

    /**
     * Get the decimal representation.
     */
    public function toDecimal(): float
    {
        return $this->amount / 100;
    }

    /**
     * Format as currency string.
     */
    public function format(): string
    {
        return number_format($this->toDecimal(), 2, ',', ' ').' '.$this->currency;
    }

    /**
     * Add another money amount.
     */
    public function add(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->amount + $other->amount, $this->currency);
    }

    /**
     * Subtract another money amount.
     */
    public function subtract(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->amount - $other->amount, $this->currency);
    }

    /**
     * Multiply by a factor.
     */
    public function multiply(float $factor): self
    {
        return new self((int) round($this->amount * $factor), $this->currency);
    }

    /**
     * Apply a percentage discount.
     */
    public function applyDiscount(float $percentage): self
    {
        return $this->multiply(1 - ($percentage / 100));
    }

    /**
     * Check if amounts are equal.
     */
    public function equals(self $other): bool
    {
        return $this->currency === $other->currency && $this->amount === $other->amount;
    }

    /**
     * Assert that both amounts have the same currency.
     */
    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException('Cannot operate on different currencies');
        }
    }
}
