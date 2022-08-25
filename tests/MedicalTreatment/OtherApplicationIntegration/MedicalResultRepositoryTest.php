<?php

declare(strict_types=1);

namespace App\Tests\MedicalTreatment\OtherApplicationIntegration;

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

        // Create your expected request from the consumer.
        $request = new ConsumerRequest();
        $request
            ->setMethod('GET')
            ->setPath('/other-api/medical-result/test1234')
            ->addHeader('Content-Type', 'application/json');

        // Create your expected response from the provider.
        $response = new ProviderResponse();
        $response
            ->setStatus(201)
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'id' => 666,
                'treatmentDecision' => TreatmentDecision::YES->value,
                'treatmentDecisionAt' => '2022-03-01T21:00:00+000Z'
            ]);

        // Create a configuration that reflects the server that was started. You can create a custom MockServerConfigInterface if needed.
        $config  = new MockServerEnvConfig();
        $builder = new InteractionBuilder($config);
        $builder
            ->uponReceiving('A get request to /other-api/medical-result/{token}')
            ->with($request)
            ->willRespondWith($response); // This has to be last. This is what makes an API request to the Mock Server to set the interaction.

        $repository = new MedicalResultRepository(
            HttpClient::create(),
            new NullLogger(),
            (string) $config->getBaseUri()
        );

        // act
        $medicalResult = $repository->getOneByToken('test1234');

        // assert
        $builder->verify(); // This will verify that the interactions took place.

        self::assertEquals(666, $medicalResult->getId());
    }
}
