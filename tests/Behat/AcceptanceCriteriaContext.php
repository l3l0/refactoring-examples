<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

final class AcceptanceCriteriaContext implements Context
{
    private KernelInterface $kernel;
    private ?string $currentUserId;

    /**
     * @var array{user: string, pass: string}
     */
    private array $authorization = [];
    /**
     * @var array<string, mixed>
     */
    private array $medicalResultsResponse = [];

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @beforeScenario
     */
    public function clearDatabase(): void
    {
        /**
         * @var Registry $registry
         */
        $registry = $this->kernel->getContainer()->get('doctrine');
        /**
         * @var EntityManagerInterface $manager
         */
        $manager = $registry->getManager();
        $purger = new ORMPurger($manager);
        $purger->purge();
    }

    /**
     * @Given I am medical assistant with id :assistantId
     */
    public function iAmMedicalAssistantWithId(string $assistantId): void
    {
        $this->currentUserId = $assistantId;
    }

    /**
     * @Given I am logged in using password :password
     */
    public function iAmLoggedInUsingPassword(string $password): void
    {
        if (!$this->currentUserId) {
            throw new LogicException(
                'Cannot login without define to who account we want to log in first.
                Please use "I am medical assistant with id" step first.
            ');
        }
        $this->authorization = [
            'user' => $this->currentUserId,
            'pass' => $password
        ];
    }

    /**
     * @Given I already issued such medical examination order:
     */
    public function iAlreadyIssuedSuchMedicalExaminationOrder(TableNode $table): void
    {
        $response = $this->kernel->handle(Request::create(
            '/api/medical-examination-order',
            'POST',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'PHP_AUTH_USER' => $this->authorization['user'],
                'PHP_AUTH_PW' => $this->authorization['pass']
            ],
            json_encode([
                'patientIdentificationNumber' => $table->getRowsHash()['patientIdentificationNumber'],
                'agreementNumber' => $table->getRowsHash()['agreementNumber'],
            ], JSON_THROW_ON_ERROR)
        ));
        Assert::eq($response->getStatusCode(), 200);
    }

    /**
     * @Given such medical result for agreement number :agreementNumber was issued:
     * @throws \JsonException
     */
    public function suchMedicalResultForAgreementNumberWasIssued(string $agreementNumber, TableNode $table): void
    {
    }

    /**
     * @When I decide that treatment is needed for medical result with agreement number :agreementNumber
     */
    public function iDecideThatTreatmentIsNeededForMedicalResultWithAgreementNumber(string $agreementNumber): void
    {
        if (!isset($this->medicalResultsResponse[$agreementNumber])) {
            throw new LogicException(
                sprintf('Medical results response not found for %s agreement number', $agreementNumber)
            );
        }

        $response = $this->kernel->handle(Request::create(
            sprintf('/api/medical-result/%s/decision', $this->medicalResultsResponse[$agreementNumber]['token']),
            'POST',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'PHP_AUTH_USER' => $this->authorization['user'],
                'PHP_AUTH_PW' => $this->authorization['pass']
            ]
        ));

        Assert::eq($response->getStatusCode(), 200);
        Assert::eq(
            json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR)['message'] ?? '',
            "decision updated"
        );
    }
}
