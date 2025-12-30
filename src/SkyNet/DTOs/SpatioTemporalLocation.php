<?php

declare(strict_types=1);

namespace J4F\SkyNet\DTOs;

use DateTime;

final readonly class SpatioTemporalLocation
{
    public function __construct(
        public float $latitude,
        public float $longitude,
        public DateTime $timeline
    ) {}

    public function __toString(): string
    {
        return \sprintf(
            'Lat: %s, Lon: %s, %s (%s)',
            $this->latitude,
            $this->longitude,
            $this->timeline->format('Y-m-d H:i:s'),
            $this->timeline->getTimezone()->getName()
        );
    }
}
