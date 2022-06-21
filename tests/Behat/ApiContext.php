<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\MedicalResult;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

final class ApiContext implements Context
{
    private KernelInterface $kernel;
    private ?Response $lastResponse = null;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @beforeScenario
     */
    public function clearDatabase(): void
    {
        $manager = $this->getManager();
        $purger = new ORMPurger($manager);
        $purger->purge();
    }

    /**
     * @Given do :method request to :url following data:
     */
    public function doRequestToWithFollowingData(
        string $method,
        string $url,
        TableNode $table
    ): void
    {
        $headersWithValues = explode(
            ',',
            $table->getRowsHash()['headers'] ?? ''
        );
        $headers = array_reduce(
            $headersWithValues,
            static function (array $carry, string $headerWithValue) {
                $header = explode('=', trim($headerWithValue));

                $carry[strtoupper($header[0])] = $header[1];

                return $carry;
            },
            []
        );
        $this->lastResponse = $this->kernel->handle(Request::create(
            $url,
            $method,
            [],
            [],
            [],
            $headers,
            $table->getRowsHash()['json'] ?? null
        ));
    }

    /**
     * @Given Medical assistant :userId already issued such medical examination order:
     */
    public function iAlreadyIssuedSuchMedicalExaminationOrder(string $userId, TableNode $table): void
    {
        $response = $this->kernel->handle(Request::create(
            '/api/medical-examination-order',
            'POST',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'PHP_AUTH_USER' => $userId,
                'PHP_AUTH_PW' => 'test123'
            ],
            json_encode([
                'patientIdentificationNumber' => $table->getRowsHash()['patientIdentificationNumber'],
                'agreementNumber' => $table->getRowsHash()['agreementNumber'],
            ], JSON_THROW_ON_ERROR)
        ));
        Assert::eq($response->getStatusCode(), 200);
    }

    /**
     * @Given Medical assistant :userId already issued such medical result with :agreementNumber agreement number:
     * @throws \JsonException
     */
    public function suchMedicalResultForAgreementNumberWasIssued(string $userId, string $agreementNumber, TableNode $table): void
    {
        $response = $this->kernel->handle(Request::create(
            '/api/medical-result',
            'POST',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'PHP_AUTH_USER' => $userId,
                'PHP_AUTH_PW' => 'test123'
            ],
            json_encode([
                'agreementNumber' => $agreementNumber,
                'resultDocumentId' => $table->getRowsHash()['resultDocumentId'],
                'requiredDecisionDate' => $table->getRowsHash()['requiredDecisionDate']
            ], JSON_THROW_ON_ERROR)
        ));
        Assert::eq($response->getStatusCode(), 200);

        if ($table->getRowsHash()['token'] ?? false) {
            $medicalResult = $this
                ->getManager()
                ->getRepository(MedicalResult::class)
                ->findOneBy(['agreementNumber' => $agreementNumber])
            ;
            $medicalResult->setToken($table->getRowsHash()['token']);
            $this->getManager()->flush();
        }
    }

    private function getRegistry(): Registry
    {
        return $this->kernel->getContainer()->get('doctrine');
    }

    /**
     * @return EntityManagerInterface
     */
    private function getManager(): ObjectManager
    {
        return $this->getRegistry()->getManager();
    }

    /**
     * @Then request status code is :code
     */
    public function requestStatusCodeIs(int $code): void
    {
        Assert::notNull($this->lastResponse, 'Last response is set');
        Assert::eq(
            $this->lastResponse->getStatusCode(),
            $code,
            'Status code equals. Expected a value equal to %2$s. Got: %s'
        );
    }
}
