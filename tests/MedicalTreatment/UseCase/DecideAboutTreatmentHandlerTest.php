<?php

declare(strict_types=1);

namespace App\Tests\MedicalTreatment\UseCase;

use App\Entity\MedicalResult;
use App\MedicalTreatment\Domain\Exception\MedicalResultNotFound;
use App\MedicalTreatment\Domain\Exception\TreatmentDecisionAlreadyDone;
use App\MedicalTreatment\Domain\MedicalResultRepository;
use App\MedicalTreatment\Domain\TreatmentDecision;
use App\MedicalTreatment\UseCase\DecideAboutTreatmentCommand;
use App\MedicalTreatment\UseCase\DecideAboutTreatmentHandler;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class DecideAboutTreatmentHandlerTest extends TestCase
{
    private ?MedicalResultRepository $medicalResultRepository = null;
    private ?DecideAboutTreatmentHandler $commandHandler = null;

    public function setUp(): void
    {
        $this->medicalResultRepository = new class implements MedicalResultRepository {
            private array $medicalResultsByToken = [];
            public function getOneByToken(string $token): MedicalResult
            {
                if (isset($this->medicalResultsByToken[$token])) {
                    return $this->medicalResultsByToken[$token];
                }

                throw MedicalResultNotFound::forToken($token);
            }

            public function add(MedicalResult $entity, bool $flush = false): void
            {
                $this->medicalResultsByToken[$entity->getToken()] = $entity;
            }
        };
        $this->commandHandler = new DecideAboutTreatmentHandler($this->medicalResultRepository);
    }

    public function testThatWhenCannotFindMedicalResultByTokenPassThrowingException(): void
    {
        // assert
        $this->expectException(MedicalResultNotFound::class);

        // act
        $this->commandHandler->__invoke(new DecideAboutTreatmentCommand(
            'token123',
            '9fdd546c-2efc-4d46-8913-142b46578a22'
        ));
    }

    public function testThatCannotAddDecisionToMedicalResultWhichAlreadyHasTreatmentDecision(): void
    {
        // arrange
        $medicalResult = new MedicalResult();
        $medicalResult->setToken('token123');
        $medicalResult->decideAboutTreatment(TreatmentDecision::YES, new DateTimeImmutable());
        $this->medicalResultRepository->add($medicalResult);

        // assert
        $this->expectException(TreatmentDecisionAlreadyDone::class);

        // act
        $this->commandHandler->__invoke(new DecideAboutTreatmentCommand(
            'token123',
            '9fdd546c-2efc-4d46-8913-142b46578a22'
        ));
    }

    public function testThatSuccessfullyDecideAboutTreatment(): void
    {
        // arrange
        $medicalResult = new MedicalResult();
        $medicalResult->setToken('token123');
        $this->medicalResultRepository->add($medicalResult);

        // act
        $this->commandHandler->__invoke(new DecideAboutTreatmentCommand(
            'token123',
            '9fdd546c-2efc-4d46-8913-142b46578a22'
        ));

        // assert
        self::assertTrue($medicalResult->isAlreadyDecidedAboutTreatment());
    }
}
