<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\MedicalResult;
use App\MedicalTreatment\Domain\Exception\MedicalResultNotFound;
use App\MedicalTreatment\Query\ApiMedicalResultQuery;
use App\Repository\MedicalResultRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MedicalResultController extends AbstractController
{
    public function __construct(
        private ApiMedicalResultQuery $apiMedicalResultQuery,
        private MedicalResultRepository $medicalResultRepository
    ) {}

    #[Route(path: '/other-api/medical-result/{token}', methods: ['GET'])]
    public function getOneByToken(string $token): JsonResponse
    {
        try {
            return $this->json(
                $this->apiMedicalResultQuery->getOneByToken($token)
            );
        } catch (MedicalResultNotFound $notFound) {
            return $this->json('', 404);
        }
    }

    #[Route(path: '/other-api/medical-result', methods: ['POST'])]
    public function save(Request $request): JsonResponse
    {
        $medicalResult = new MedicalResult();

        $statusCode = 201;
        // update
        if ($request->get('id')) {
            $statusCode = 200;
            $medicalResult = $this->getMedicalResult((int) $request->get('id'));
        }
        $decisionDate = $request->get('decisionDate') ?
            new DateTimeImmutable($request->get('decisionDate')) : null;
        $requiredDecisionDate = $request->get('requiredDecisionDate') ?
            new DateTimeImmutable($request->get('requiredDecisionDate')) : null;
        $medicalResult->setTreatmentDecision($request->get('treatmentDecision'));
        $medicalResult->setAgreementNumber($request->get('agreementNumber'));
        $medicalResult->setClientIpAddress($request->get('clientIpAddress'));
        $medicalResult->setDecisionDate($decisionDate);
        $medicalResult->setRequiredDecisionDate($requiredDecisionDate);
        $medicalResult->setTreatmentDecisionType($request->get('treatmentDecisionType'));
        $medicalResult->setResultDocumentId($request->get('resultDocumentId'));
        if ($request->get('token')) {
            $medicalResult->setToken($request->get('token'));
        }
        $this->medicalResultRepository->add($medicalResult, true);

        return $this->json(['id' => $medicalResult->getId()], $statusCode);
    }

    private function getMedicalResult(int $id): MedicalResult
    {
        $medicalResult = $this->medicalResultRepository->find($id);
        if (!$medicalResult) {
            throw $this->createNotFoundException('Not found');
        }

        return $medicalResult;
    }
}