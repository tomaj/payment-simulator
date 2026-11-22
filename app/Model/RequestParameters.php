<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Reads the parameters of a simulated bank request and remembers which required ones are missing,
 * so the page can list all of them instead of failing on the first one.
 */
final class RequestParameters
{
    /** @var list<string> */
    private array $missing = [];

    /**
     * @param array<mixed> $values query or POST parameters
     */
    public function __construct(
        private readonly array $values,
    ) {}

    /**
     * The value of a required parameter; '' (and the name is recorded as missing) when it is absent or not a string.
     */
    public function required(string $name): string
    {
        $value = $this->values[$name] ?? null;
        if (!is_string($value)) {
            $this->missing[] = $name;
            return '';
        }

        return $value;
    }

    /**
     * The value of an optional parameter, '' when it is absent or not a string.
     */
    public function optional(string $name): string
    {
        $value = $this->values[$name] ?? null;

        return is_string($value) ? $value : '';
    }

    /**
     * @return list<string> names of the required parameters that were absent, in the order they were read
     */
    public function missing(): array
    {
        return $this->missing;
    }

    /**
     * @return array<mixed>
     */
    public function all(): array
    {
        return $this->values;
    }
}
