<?php

declare(strict_types=1);

namespace App\MedicalTreatment\OtherApplicationIntegration;

use App\Entity\MedicalResult;
use App\MedicalTreatment\Domain\Exception\MedicalResultNotFound;
use App\MedicalTreatment\Domain\MedicalResultRepository as MedicalResultRepositoryInterface;
use App\MedicalTreatment\Domain\TreatmentDecision;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MedicalResultRepository implements MedicalResultRepositoryInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        private readonly string $otherServiceBaseUrl
    ) {}

    public function getOneByToken(string $token): MedicalResult
    {
        try {
            $response = $this
                ->httpClient
                ->request(
                    'GET',
                    sprintf('%s/other-api/medical-result/%s', $this->otherServiceBaseUrl, $token),
                    [
                        'headers' => [
                            'Content-Type' => 'application/json'
                        ]
                    ]
                )
                ->toArray()
            ;
        } catch (HttpExceptionInterface|ClientExceptionInterface $exception) {
            $this->logger->error('Error when fetching medical result', ['exception' => $exception]);
            throw MedicalResultNotFound::forToken($token);
        }

        $medicalResult = new MedicalResult();
        $medicalResult->setToken($token);
        $medicalResult->setId((int) $response['id']);
        if ($response['treatmentDecisionAt'] && $response['treatmentDecision']) {
            $medicalResult->decideAboutTreatment(
                TreatmentDecision::from($response['treatmentDecision']),
                new DateTimeImmutable($response['treatmentDecisionAt'])
            );
        }
        $medicalResult->setAgreementNumber($response['agreementNumber'] ?: '');

        return $medicalResult;
    }

    public function add(MedicalResult $entity, bool $flush = false): void
    {
        $response = $this
            ->httpClient
            ->request(
                'POST',
                sprintf('%s/other-api/medical-result', $this->otherServiceBaseUrl),
                [
                    'json' => [
                        'id' => (string) $entity->getId(),
                        'decisionDate' => $entity->getDecisionDate() ? $entity->getDecisionDate()->format(DATE_ATOM) : null,
                        'requiredDecisionDate' => $entity->getRequiredDecisionDate() ? $entity->getRequiredDecisionDate()->format(DATE_ATOM) : null,
                        'agreementNumber' => $entity->getAgreementNumber() ? $entity->getAgreementNumber()->toString() : null,
                        'clientIpAddress' => $entity->getClientIpAddress(),
                        'resultDocumentId' => $entity->getResultDocumentId(),
                        'treatmentDecision' => $entity->getTreatmentDecision(),
                        'treatmentDecisionType' => $entity->getTreatmentDecisionType(),
                        'token' => $entity->getToken(),
                    ]
                ]
            )
            ->toArray()
        ;

        if (!$entity->getId()) {
            $entity->setId((int) $response['id']);
        }
    }
}