<?php

declare(strict_types=1);

namespace App\Tests\MedicalTreatment\OtherApplicationIntegration;

use App\Entity\MedicalResult;
use App\MedicalTreatment\Domain\TreatmentDecision;
use App\MedicalTreatment\OtherApplicationIntegration\MedicalResultRepository;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\HttpClient;

class MedicalResultRepositoryTest extends TestCase
{
    public function testThatMedicalResultRepositoryMakeRequest(): void
    {
        // arrange

        $entity = new MedicalResult();
        $entity->setId(666);
        $entity->setToken('token');
        $entity->setAgreementNumber('UMOWA-01');
        $entity->setTreatmentDecision(TreatmentDecision::YES->value);
        $entity->setDecisionDate(new \DateTimeImmutable('2022-03-01T21:00:00+000Z'));

        // Create a configuration that reflects the server that was started. You can create a custom MockServerConfigInterface if needed.
        $config  = new MockServerEnvConfig();
        $builder = new InteractionBuilder($config);

        // Create your expected request from the consumer.
        $request = new ConsumerRequest();
        $request
            ->setMethod('GET')
            ->setPath('/other-api/medical-result/' . $entity->getToken())
            ->addHeader('Content-Type', 'application/json');

        // Create your expected response from the provider.
        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'id' => (string) $entity->getId(),
                'decisionDate' => $entity->getDecisionDate() ? $entity->getDecisionDate()->format(DATE_ATOM) : null,
                'requiredDecisionDate' => $entity->getRequiredDecisionDate() ? $entity->getRequiredDecisionDate()->format(DATE_ATOM) : null,
                'agreementNumber' => $entity->getAgreementNumber() ? $entity->getAgreementNumber()->toString() : null,
                'clientIpAddress' => $entity->getClientIpAddress(),
                'resultDocumentId' => $entity->getResultDocumentId(),
                'treatmentDecision' => $entity->getTreatmentDecision(),
                'treatmentDecisionType' => $entity->getTreatmentDecisionType(),
                'token' => $entity->getToken(),
            ]);

        // Create your expected request from the consumer.
        $requestPost = new ConsumerRequest();
        $requestPost
            ->setMethod('POST')
            ->setPath('/other-api/medical-result')
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'id' => (string) $entity->getId(),
                'decisionDate' => $entity->getDecisionDate() ? $entity->getDecisionDate()->format(DATE_ATOM) : null,
                'requiredDecisionDate' => $entity->getRequiredDecisionDate() ? $entity->getRequiredDecisionDate()->format(DATE_ATOM) : null,
                'agreementNumber' => $entity->getAgreementNumber() ? $entity->getAgreementNumber()->toString() : null,
                'clientIpAddress' => $entity->getClientIpAddress(),
                'resultDocumentId' => $entity->getResultDocumentId(),
                'treatmentDecision' => $entity->getTreatmentDecision(),
                'treatmentDecisionType' => $entity->getTreatmentDecisionType(),
                'token' => $entity->getToken(),
            ])
        ;

        // Create your expected response from the provider.
        $responsePost = new ProviderResponse();
        $responsePost
            ->setStatus(200)
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'id' => (string) $entity->getId()
            ]);

        $builder
            ->uponReceiving('A post request to /other-api/medical-result')
            ->with($requestPost)
            ->willRespondWith($responsePost);
        $builder
            ->uponReceiving('A get request to /other-api/medical-result/{token}')
            ->with($request)
            ->willRespondWith($response);

        $repository = new MedicalResultRepository(
            HttpClient::create(),
            new NullLogger(),
            (string) $config->getBaseUri()
        );

        // act
        $repository->add($entity, true);
        $medicalResult = $repository->getOneByToken($entity->getToken());

        $builder->verify(); // This will verify that the interactions took place.

        // assert
        self::assertEquals(666, $medicalResult->getId());
    }
}
