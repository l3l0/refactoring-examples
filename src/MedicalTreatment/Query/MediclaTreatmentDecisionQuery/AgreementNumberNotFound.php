<?php

declare(strict_types=1);

namespace App\MedicalTreatment\Query\MediclaTreatmentDecisionQuery;

use LogicException;

class AgreementNumberNotFound extends LogicException
{
    public static function whenNotFoundMedicalResultForToken(string $token, ?\Throwable $previous = null): self
    {
        return new self(
            sprintf('Agreement number not found cause cannot find medical result with token %s', $token),
            0,
            $previous
        );
    }

    public static function whenMedicalResultHasEmptyAgreementNumber(string $token): self
    {
        return new self(
            sprintf('Agreement number is empty for medical result with token %s', $token)
        );
    }
}
