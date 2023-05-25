<?php

declare(strict_types=1);

namespace App\MedicalTreatment\Doctrine;

use App\MedicalTreatment\Domain\Exception\MedicalResultNotFound;
use App\MedicalTreatment\Query\ApiMedicalResultQuery;
use App\MedicalTreatment\Query\ApiMedicalResultQuery\MedicalResult;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Exception;

class DbalApiMedicalResultQuery implements ApiMedicalResultQuery
{
    public function __construct(private Connection $connection)
    {}

    /**
     * @throws DBALException
     * @throws Exception
     */
    public function getOneByToken(string $token): MedicalResult
    {
        $result = $this->connection->fetchAssociative(
            'SELECT mr.id, mr.decision_date, mr.treatment_decision, mr.agreement_number
                   FROM medical_result mr WHERE mr.token = :token',
            [
                'token' => $token
            ]
        );

        if (!$result) {
            throw MedicalResultNotFound::forToken($token);
        }

        return new MedicalResult(
            (string) $result['id'],
            $result['treatment_decision'],
            $result['decision_date'] ? new DateTimeImmutable($result['decision_date']) : null,
            $result['agreement_number'] ?? ''
        );
    }
}