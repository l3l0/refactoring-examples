<?php

namespace MedicalTreatment\OtherApplicationIntegration;

use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PHPUnit\Framework\TestCase;

class ProviderTest extends TestCase
{
    /**
     * This test will run after the web server is started.
     */
    // public function testPactVerifyConsumer()
    // {
    //     $config = new VerifierConfig();
    //     $config
    //         ->setProviderName('someProvider') // Providers name to fetch.
    //         ->setProviderVersion('1.0.0') // Providers version.
    //         ->setProviderBranch('main') // Providers git branch
    //         ->setProviderBaseUrl(new Uri('http://localhost:80/')) // URL of the Provider.
    //     ; // Flag the verifier service to publish the results to the Pact Broker.

    //     // Verify that the Consumer 'someConsumer' that is tagged with 'master' is valid.
    //     $verifier = new Verifier($config);
    //     $verifier->verifyFiles([__DIR__ . '/../../pact-output/other-someprovider.json']);

    //     // This will not be reached if the PACT verifier throws an error, otherwise it was successful.
    //     $this->assertTrue(true, 'Pact Verification has failed.');
    // }
}