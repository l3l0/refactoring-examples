<?php

declare(strict_types=1);

namespace App\MedicalTreatment\Domain;

use DateTimeImmutable;

class MedicalResult
{
    protected ?int $id = null;
    protected ?string $treatmentDecision = null;
    protected ?DateTimeImmutable $decisionDate = null;
    protected string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function isAlreadyDecidedAboutTreatment(): bool
    {
        return $this->treatmentDecision !== null;
    }

    public function decideAboutTreatment(
        TreatmentDecision $treatmentDecision,
        DateTimeImmutable $decisionDate
    ): self
    {
        $this->treatmentDecision = $treatmentDecision->value;
        $this->decisionDate = $decisionDate;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}