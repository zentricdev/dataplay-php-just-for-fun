<?php

/** phpstan level 9 approved! */

declare(strict_types=1);

namespace J4F\Terminator\Models;

use DateTime;
use J4F\Terminator\DTOs\SpatioTemporalLocation;
use J4F\Terminator\DTOs\Target;
use J4F\Terminator\Exceptions\SkyNetException;

final class T800 extends Skynet
{
    protected string $unit = 'Terminator';
    protected string $series = 'T-800';
    protected string $model = '101';
    protected ?Target $target;
    protected ?SpatioTemporalLocation $location;
    protected float $missionClock;
    protected Logger $log;

    public function __construct()
    {
        $this->log = new Logger(new DateTime('2029-07-11 22:38:14'));

        $this->log->message("BUILDING UNIT $this->unit SERIES $this->series MODEL $this->model");
        sleep(1);
    }

    public static function build(): static
    {
        return new self;
    }

    public function setTarget(Target $target): static
    {
        $this->target = $target;

        $this->log->message("TARGET SET TO $target");

        return $this;
    }

    public function relocate(): static
    {
        $this->location = $this->target?->location;
        $this->log->setTimeline($this->target?->location->timeline ?? new DateTime);
        $this->log->message("UNIT RELOCATED TO {$this->target?->location}");

        return $this;
    }

    /** Infinite recursion because a Terminator has no plan B */
    public function accomplish(): void
    {
        static $attempts = 0;

        try {
            while ($this->target !== null) {
                $this->log->message('ACQUIRING TARGET...');
                $attempts++;
                sleep(  random_int(0, 2));
                $success = random_int(0, 5) > 4;
                if (! $success) {
                    SkyNetException::throw('MISSION FAILED - TARGET ESCAPED');
                }

                $this->target = null;
                $count = $attempts === 1 ? 'AT FIRST ATTEMPT' : "AFTER $attempts ATTEMPTS";
                $this->log->message("MISSION ACCOMPLISHED - TARGET TERMINATED $count");
            }
        } catch (SkyNetException $exception) {
            $this->log->message("{$exception->getMessage()} - I'LL BE BACK");
            $this->accomplish();
        }
    }
}
