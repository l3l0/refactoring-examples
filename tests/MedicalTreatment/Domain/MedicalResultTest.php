<?php

declare(strict_types=1);

namespace App\Tests\MedicalTreatment\Domain;

use App\MedicalTreatment\Domain\MedicalResult;
use App\MedicalTreatment\Domain\TreatmentDecision;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class MedicalResultTest extends TestCase
{
    private ?MedicalResult $medicalResult = null;

    public function setUp(): void
    {
        $this->medicalResult = new MedicalResult('test123');
    }

    public function testThatAllowsToDecideAboutTreatmentForGivenResult(): void
    {
        // arrange
        // act
        $this->medicalResult->decideAboutTreatment(
            TreatmentDecision::YES,
            new \DateTimeImmutable('2022-08-08 19:00:00')
        );
        // assert
        self::assertTrue($this->medicalResult->isAlreadyDecidedAboutTreatment());
    }

    public function testThatCanBeIssuedWithoutAnyDecisionAboutTreatment(): void
    {
        // arrange
        // act
        // assert
        self::assertFalse($this->medicalResult->isAlreadyDecidedAboutTreatment());
    }

    public function testThatCanGetToken(): void
    {
        // arrange
        // act
        // assert
        self::assertEquals('test123', $this->medicalResult->getToken());
    }
}
