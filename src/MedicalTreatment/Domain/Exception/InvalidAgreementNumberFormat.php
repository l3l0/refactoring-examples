<?php

declare(strict_types=1);

namespace App\MedicalTreatment\Domain\Exception;

use InvalidArgumentException;

class InvalidAgreementNumberFormat extends InvalidArgumentException
{
    public static function forValue(string $value): self
    {
        return new self(
            sprintf('Value is %s valid agreement number', $value)
        );
    }
}