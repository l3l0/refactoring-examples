<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\MedicalExaminationOrder;
use App\Entity\MedicalResult;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MedicalResultManager
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function addMedicalResult(MedicalResult $result): MedicalResult
    {
        $this->checkIfAlreadyExistsTheSameMedicalResult($result);
        $this->checkIfExistsExaminationOrder($result);

        $this->em->persist($result);

        return $this->updateMedicalResult($result);
    }

    public function updateMedicalResult(MedicalResult $result): MedicalResult
    {
        $this->em->flush();

        return $result;
    }

    public function getOneMedicalResultByToken(string $token): ?MedicalResult
    {
        return $this->em->getRepository(MedicalResult::class)->findOneBy([
            'token' => $token,
        ]);
    }

    private function checkIfAlreadyExistsTheSameMedicalResult(MedicalResult $result): void
    {
        $alreadyAdded = $this->em->getRepository(MedicalResult::class)->findOneBy([
            'agreementNumber' => $result->getAgreementNumber(),
            'resultDocumentId' => $result->getResultDocumentId()
        ]);

        if (null !== $alreadyAdded) {
            throw new HttpException(200, 'Istnieje już rekord dla wskazanego materiału w tym dokumencie.');
        }
    }

    private function checkIfExistsExaminationOrder(MedicalResult $result): void
    {
        $contract = $this->em
            ->getRepository(MedicalExaminationOrder::class)
            ->findOneBy(['agreementNumber' => $result->getAgreementNumber()]);

        if (null === $contract) {
            throw new NotFoundHttpException('Nie znaleziono rekordu zamówienia.');
        }
    }
}
