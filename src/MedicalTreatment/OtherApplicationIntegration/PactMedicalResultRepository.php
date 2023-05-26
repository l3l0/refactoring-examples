<?php

declare(strict_types=1);

namespace App\MedicalTreatment\OtherApplicationIntegration;

use App\Entity\MedicalResult;
use App\MedicalTreatment\Domain\MedicalResultRepository as MedicalResultRepositoryInterface;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\HttpClient;

class PactMedicalResultRepository implements MedicalResultRepositoryInterface
{
    private array $entites = [];
    private MockServerEnvConfig $config;
    private InteractionBuilder $builder;
    private MedicalResultRepository $repository;

    public function __construct()
    {
        $this->config  = new MockServerEnvConfig();
        $this->builder = new InteractionBuilder($this->config);
        $this->repository = new MedicalResultRepository(
            HttpClient::create(),
            new NullLogger(),
            (string) $this->config->getBaseUri()
        );
    }

    public function getOneByToken(string $token): MedicalResult
    {
        $request = new ConsumerRequest();
        $request
            ->setMethod('GET')
            ->setPath('/other-api/medical-result/' . $token)
            ->addHeader('Content-Type', 'application/json');

        // Create your expected response from the provider.
        $response = new ProviderResponse();

        if (array_key_exists($token, $this->entites)) {
            /**
             * @var MedicalResult $entity
             */
            $entity = $this->entites[$token];
            $response
                ->setStatus(200)
                ->addHeader('Content-Type', 'application/json')
                ->setBody([
                    'id' => array_search($entity, array_values($this->entites)) + 1,
                    'treatmentDecision' => $entity->getTreatmentDecision(),
                    'treatmentDecisionAt' =>  $entity->getDecisionDate()->format(DATE_ATOM),
                    'agreementNumber' => $entity->getAgreementNumber()
                ]);
        } else {
            $response
                ->setStatus(404)
                ->addHeader('Content-Type', 'application/json');
        }

        // Create a configuration that reflects the server that was started. You can create a custom MockServerConfigInterface if needed.
        $this->builder
            ->uponReceiving('A get request to /other-api/medical-result/{token}')
            ->with($request)
            ->willRespondWith($response); // This has to be last. This is what makes an API request to the Mock Server to set the interaction.


        $results = $this->repository->getOneByToken($token);
        $this->builder->verify();

        return $results;
    }

    public function add(MedicalResult $entity, bool $flush = false): void
    {
        $this->entites[$entity->getToken()] = $entity;

        $request = new ConsumerRequest();
        $request
            ->setMethod('POST')
            ->setPath('/other-api/medical-result')
            ->addHeader('Content-Type', 'application/json')
            ->setBody(json_encode([
                'id' => (string) $entity->getId(),
                'decisionDate' => $entity->getDecisionDate() ? $entity->getDecisionDate()->format(DATE_ATOM) : null,
                'requiredDecisionDate' => $entity->getRequiredDecisionDate() ? $entity->getRequiredDecisionDate()->format(DATE_ATOM) : null,
                'agreementNumber' => $entity->getAgreementNumber() ? $entity->getAgreementNumber()->toString() : null,
                'clientIpAddress' => $entity->getClientIpAddress(),
                'resultDocumentId' => $entity->getResultDocumentId(),
                'treatmentDecision' => $entity->getTreatmentDecision(),
                'treatmentDecisionType' => $entity->getTreatmentDecisionType(),
                'token' => $entity->getToken(),
            ]));

        $response = new ProviderResponse();
        $response
            ->setStatus(201)
            ->addHeader('Content-Type', 'application/json');

        $this->repository->add($entity, $flush);

        $this->builder->verify();
    }
}