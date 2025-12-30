<?php

declare(strict_types=1);

namespace J4F\PayDay;

class Cash
{
    protected array $denominations = [];
    protected array $breakdown = [];
    protected float $pending;
    protected bool $processing = false;

    public function __construct(protected Currency $currency) {}

    protected function reset(float $amount)
    {
        $this->pending = $amount * 100;

        $this->denominations = $this->currency->denominations();
        rsort($this->denominations, SORT_NUMERIC);

        $this->breakdown = [];
    }

    public function breakdown(float $amount): array
    {
        if (! $this->processing) {
            $this->reset($amount);
            $this->processing = true;
        }

        while (! empty($this->denominations)) {
            $denomination = array_shift($this->denominations);
            $count = (int) floor($this->pending / $denomination);

            $reduce = $count * $denomination;
            $this->pending = round($this->pending - $reduce);

            $this->breakdown[] = [
                'denomination' => $denomination,
                'count' => $count,
            ];

            $this->breakdown($this->pending);
        }

        $this->processing = false;

        return array_values($this->breakdown);
    }

    public function breakdownCollection(iterable $collection, ?string $attributeName = 'amount'): array
    {
        foreach ($collection as $item) {
            $breakdown = $this->breakdown($item[$attributeName]);

            foreach ($breakdown as $result) {
                $key = (string) $result['denomination'];
                if (! isset($results[$key])) {
                    $results[$key] = [
                        'count' => 0,
                        'denomination' => $result['denomination'] / 100,
                        'type' => $this->currency->type($result['denomination']),
                        'amount' => 0,
                    ];
                }

                $results[$key]['count'] += $result['count'];
            }
        }

        $results = array_map(function($item) {
            $item['amount'] = $item['count'] * $item['denomination'];

            return $item;
        }, array_filter($results, fn ($item) => $item['count'] > 0));

        return $results;
    }
}
