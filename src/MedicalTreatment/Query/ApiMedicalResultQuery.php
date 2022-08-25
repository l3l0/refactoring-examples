<?php

declare(strict_types=1);

namespace App\MedicalTreatment\Query;

use App\MedicalTreatment\Query\ApiMedicalResultQuery\MedicalResult;

interface ApiMedicalResultQuery
{
    public function getOneByToken(string $token): MedicalResult;
}