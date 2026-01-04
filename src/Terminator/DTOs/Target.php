<?php

declare(strict_types=1);

namespace J4F\Terminator\DTOs;

final readonly class Target
{
    public function __construct(
        public string $name,
        public int $yearOfBirth,
        public string $occupation,
        public SpatioTemporalLocation $location
    ) {}

    public function __toString(): string
    {
        return \sprintf(
            '%s, %s, Born %s',
            $this->name,
            $this->occupation,
            $this->yearOfBirth
        );
    }
}
