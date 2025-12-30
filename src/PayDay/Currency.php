<?php

declare(strict_types=1);

namespace J4F\PayDay;

class Currency
{
    public function __construct(
        public readonly string $code,
        /** @var array<float> $coins */
        public readonly array $coins,
        /** @var array<float> $notes */
        public readonly array $notes
    ) {}

    /** @param array{code: string, coins: array<float>, notes: array<float>} $config */
    public static function fromArray(array $config): self
    {
        return new self(
            $config['code'],
            $config['coins'],
            $config['notes']
        );
    }

    public function type(float $value): string
    {
        return match (true) {
            \in_array($value / 100, $this->notes) => 'note',
            \in_array($value / 100, $this->coins) => 'coin',
            default => "unknown $value"
        };
    }

    /** @return array<float|int> */
    public function denominations(): array
    {
        return array_map(fn ($unit) => $unit * 100, [
            ...$this->coins,
            ...$this->notes,
        ]);
    }
}
