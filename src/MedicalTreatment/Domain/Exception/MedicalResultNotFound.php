<?php

declare(strict_types=1);

namespace App\MedicalTreatment\Domain\Exception;

use LogicException;

class MedicalResultNotFound extends LogicException
{
    public static function forToken(string $token): self
    {
        return new self(
            sprintf('Medical result for %s not found', $token)
        );
    }
}
