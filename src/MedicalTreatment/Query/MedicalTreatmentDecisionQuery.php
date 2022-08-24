<?php

namespace App\MedicalTreatment\Query;

use App\MedicalTreatment\Domain\AgreementNumber;
use App\MedicalTreatment\Query\MediclaTreatmentDecisionQuery\AgreementNumberNotFound;
use Symfony\Component\Uid\Uuid;

interface MedicalTreatmentDecisionQuery
{
    /**
     * @param string $medicalResultToken
     * @param Uuid $accountId
     */
    public function medicalExaminationOrderExists(string $medicalResultToken, Uuid $accountId): bool;
}
