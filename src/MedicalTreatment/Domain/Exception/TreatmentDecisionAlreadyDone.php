<?php

declare(strict_types=1);

namespace App\MedicalTreatment\Domain\Exception;

use LogicException;

class TreatmentDecisionAlreadyDone extends LogicException
{
    public static function forMedicalResultId(int $id): self
    {
        return new self(
            sprintf('Treatment decision already done for medical result with id %d', $id)
        );
    }
}
