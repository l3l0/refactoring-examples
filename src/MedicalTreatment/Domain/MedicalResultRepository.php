<?php

namespace App\MedicalTreatment\Domain;

use App\MedicalTreatment\Domain\Exception\MedicalResultNotFound;

interface MedicalResultRepository
{
    /**
     * @throws MedicalResultNotFound
     */
    public function getOneByToken(string $token): MedicalResult;
    public function add(MedicalResult $entity, bool $flush = false): void;
}
