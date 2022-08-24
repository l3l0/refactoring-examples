<?php

declare(strict_types=1);

namespace App\MedicalTreatment\Domain;

use App\MedicalTreatment\Domain\Exception\InvalidAgreementNumberFormat;

final class AgreementNumber
{
    public function __construct(private readonly string $number) {
        if (mb_strlen($this->number) <= 4) {
            throw InvalidAgreementNumberFormat::forValue($number);
        }
    }

    public function toString(): string
    {
        return $this->number;
    }
}
