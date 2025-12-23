<?php

namespace AppModules\Shared\src\ValueObjects;

use InvalidArgumentException;

final readonly class Email
{
    public function __construct(
        public string $value
    ) {
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email address: {$value}");
        }
    }

    /**
     * Get the domain part of the email.
     */
    public function domain(): string
    {
        return substr(strrchr($this->value, '@'), 1);
    }

    /**
     * Get the local part of the email.
     */
    public function local(): string
    {
        return strstr($this->value, '@', true);
    }

    /**
     * Convert to string.
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
