<?php

declare(strict_types=1);

namespace App\Tests\MedicalTreatment\Domain;

use App\MedicalTreatment\Domain\AgreementNumber;
use App\MedicalTreatment\Domain\Exception\InvalidAgreementNumberFormat;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class AgreementNumberTest extends TestCase
{
    public function testThatCannotBeCreatedFromTooShortNumber(): void
    {
        $this->expectExceptionObject(InvalidAgreementNumberFormat::forValue('UM'));
        new AgreementNumber('UM');
    }

    /**
     * @dataProvider validAgreementNumbersProviders
     */
    public function testThatValidAgreementNumber(string $number): void
    {
        self::assertInstanceOf(AgreementNumber::class, new AgreementNumber($number));
    }

    public function validAgreementNumbersProviders(): array
    {
        return [
            ['UMOWA-01'],
            ['TEST1111']
        ];
    }
}
