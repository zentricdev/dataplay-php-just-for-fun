<?php

/** phpstan level 9 approved! */

declare(strict_types=1);

namespace J4F\SkyNet;

use DateInterval;
use DateTime;
use J4F\SkyNet\DTOs\SpatioTemporalLocation;
use J4F\SkyNet\DTOs\Target;
use J4F\SkyNet\Exceptions\SkyNetException;

final class Terminator extends Core
{
    /** @var array<string> */
    protected array $log = [];

    protected string $unit = 'Terminator';
    protected string $series = 'T-800';
    protected string $model = '101';
    protected ?Target $target;
    protected ?SpatioTemporalLocation $location;
    protected ?DateTime $timeline;
    protected float $missionClock;

    public function __construct()
    {
        $this->missionClock = microtime(true);
        $this->timeline = new DateTime('2029-07-11 22:38:14');

        $this->log("BUILDING UNIT $this->unit SERIES $this->series MODEL $this->model");
        sleep(1);
    }

    public static function build(): static
    {
        return new self;
    }

    public function setTarget(Target $target): static
    {
        $this->target = $target;

        $this->log("TARGET SET TO $target");

        return $this;
    }

    public function relocate(): static
    {
        $this->location = $this->target?->location;
        $this->timeline = $this->target?->location->timeline;

        $this->log("UNIT RELOCATED TO {$this->target?->location}");

        return $this;
    }

    /** Infinite recursion because a Terminator has no plan B */
    public function accomplish(): void
    {
        static $attempts = 0;

        try {
            while ($this->target !== null) {
                $this->log('ACQUIRING TARGET...');
                $attempts++;
                sleep(  random_int(0, 2));
                $success = random_int(0, 5) > 4;
                if (! $success) {
                    SkyNetException::throw('MISSION FAILED - TARGET ESCAPED');
                }

                $this->target = null;
                $count = $attempts === 1 ? 'AT FIRST ATTEMPT' : "AFTER $attempts ATTEMPTS";
                $this->log("MISSION ACCOMPLISHED - TARGET TERMINATED $count");
            }
        } catch (SkyNetException $exception) {
            $this->log("{$exception->getMessage()} - I'LL BE BACK");
            $this->accomplish();
        }
    }

    protected function log(string $message): void
    {
        $seconds = round(microtime(true) - $this->missionClock);
        $interval = "PT{$seconds}S";
        $timestamp = $this->timeline
            ?->add(new DateInterval($interval))
            ->format('Y-m-d H:i:s');

        $color = "\033[0;32m";
        $reset = "\033[0m";
        $this->log[] = $message = "$timestamp {$color}$message{$reset}";
        $this->output($message);
    }

    protected function output(string $message): void
    {
        echo $message . PHP_EOL;
    }
}
