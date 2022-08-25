<?php

declare(strict_types=1);

namespace App\MedicalTreatment\Query\ApiMedicalResultQuery;

use DateTimeImmutable;
use JsonSerializable;

class MedicalResult implements JsonSerializable
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $treatmentDecision,
        public readonly ?DateTimeImmutable $treatmentDecisionAt
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'treatmentDecision' => $this->treatmentDecision,
            'treatmentDecisionAt' => $this->treatmentDecisionAt?->format(DATE_ATOM)
        ];
    }
}