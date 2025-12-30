<?php

declare(strict_types=1);

namespace J4F\SkyNet;

require_once __DIR__ . '/../../vendor/autoload.php';

use DateTime;
use DateTimeZone;
use J4F\SkyNet\DTOs\SpatioTemporalLocation;
use J4F\SkyNet\DTOs\Target;

Terminator::build()
    ->setTarget(target: new Target(
        name: 'Sarah Connor',
        yearOfBirth: 1965,
        occupation: 'Big Jeff\'s waitress',
        location: new SpatioTemporalLocation(
            latitude: 34.0522,
            longitude: 118.2437,
            timeline: new DateTime(
                datetime: '1984-05-12 01:52:00',
                timezone: new DateTimeZone(timezone: 'America/Los_Angeles')
            )
        )
    ))
    ->relocate()
    ->accomplish();
