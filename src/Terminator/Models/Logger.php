<?php

declare(strict_types=1);

namespace J4F\Terminator\Models;

use DateInterval;
use DateTime;

class Logger
{
    protected float $timelineClock;

    /** @var array<int, array{timestamp: DateTime, message: string}> */
    protected array $messages = [];

    public function __construct(protected DateTime $timeline)
    {
        $this->timelineClock = microtime(true);
    }

    public function setTimeline(DateTime $timeline): static
    {
        $this->timeline = $timeline;
        $this->timelineClock = microtime(true);

        return $this;
    }

    public function message(string $message): static
    {
        $seconds = round(microtime(true) - $this->timelineClock);
        $interval = "PT{$seconds}S";
        $timestamp = $this->timeline->add(new DateInterval($interval));

        $this->messages[] = [
            'timestamp' => $timestamp,
            'message' => $message,
        ];

        $this->output($timestamp, $message);

        return $this;
    }

    protected function output(DateTime $timestamp, string $message): void
    {
        $color = "\033[0;32m";
        $reset = "\033[0m";

        $message = "{$timestamp->format('Y-m-d H:i:s')} {$color}$message{$reset}";

        echo $message . PHP_EOL;
    }
}
