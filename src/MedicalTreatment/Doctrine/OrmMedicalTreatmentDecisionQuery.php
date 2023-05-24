<?php

declare(strict_types=1);

namespace App\MedicalTreatment\Doctrine;

use App\Entity\MedicalExaminationOrder;
use App\Entity\MedicalResult;
use App\MedicalTreatment\Domain\AgreementNumber;
use App\MedicalTreatment\Domain\Exception\MedicalResultNotFound;
use App\MedicalTreatment\Domain\MedicalResultRepository;
use App\MedicalTreatment\Query\MedicalTreatmentDecisionQuery;
use App\MedicalTreatment\Query\MediclaTreatmentDecisionQuery\AgreementNumberNotFound;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

class OrmMedicalTreatmentDecisionQuery implements MedicalTreatmentDecisionQuery
{
    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly LoggerInterface $logger,
        private readonly MedicalResultRepository $medicalResultRepository
    ) {}

    /**
     * @param string $medicalResultToken
     * @param Uuid $accountId
     * @throws AgreementNumberNotFound
     * @return bool
     */
    public function medicalExaminationOrderExists(string $medicalResultToken, Uuid $accountId): bool
    {
        try {
            $order = $this->manager->getRepository(MedicalExaminationOrder::class)->findOneBy([
                'agreementNumber' => $this->getAgreementNumber($medicalResultToken)->toString(),
                'accountId' => $accountId->toRfc4122()
            ]);
        } catch (AgreementNumberNotFound $notFound) {
            $this->logger->error($notFound->getMessage());
            return false;
        }

        return $order !== null;
    }

    private function getAgreementNumber(string $medicalResultToken): AgreementNumber
    {
        try {
            $result = $this->medicalResultRepository->getOneByToken($medicalResultToken);
        } catch (MedicalResultNotFound $notFound) {
            throw AgreementNumberNotFound::whenNotFoundMedicalResultForToken($medicalResultToken);
        }

        if ($result->getAgreementNumber() === null) {
           throw AgreementNumberNotFound::whenMedicalResultHasEmptyAgreementNumber($medicalResultToken);
        }

        return $result->getAgreementNumber();
    }
}
