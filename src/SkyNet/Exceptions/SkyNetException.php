<?php

declare(strict_types=1);

namespace J4F\SkyNet\Exceptions;

final class SkyNetException extends \Exception
{
    public static function throw(string $message): SkyNetException
    {
        throw new self($message);
    }
}
