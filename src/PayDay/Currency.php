<?php

declare(strict_types=1);

namespace J4F\PayDay;

class Currency
{
    public function __construct(
        public readonly string $code,
        public readonly array $coins,
        public readonly array $notes
    ) {}

    public static function fromArray(array $config): static
    {
        return new self(
            $config['code'],
            $config['coins'],
            $config['notes']);
    }

    public function type($value): string
    {
        return match (true) {
            \in_array($value / 100, $this->notes) => 'note',
            \in_array($value / 100, $this->coins) => 'coin',
            default => "unknown $value"
        };
    }

    public function denominations(): array
    {
        // return [...$this->coins, ...$this->notes];

        return array_map(fn ($unit) => $unit * 100, [
            ...$this->coins,
            ...$this->notes,
        ]);
    }
}
