<?php

namespace App\Tests\GasStationVO\Step0;

use App\GasStationVO\Step0\GasAlertCalculator;
use PHPUnit\Framework\TestCase;

class GasAlertCalculatorTest extends TestCase
{
  public function testFor3Response()
  {
    $torunOffice = ['lat' => 53.02879633770021, 'long' => 18.63850934547079];
    $finder = new GasAlertCalculator([$torunOffice]);

    $alert = $finder->calculate(53.02390516137652, 18.634435851394855, 0.3, 10);

    self::assertEquals(3, $alert);
  }

  public function testFor5Response()
  {
    $torunOffice = ['lat' => 53.02879633770021, 'long' => 18.63850934547079];
    $finder = new GasAlertCalculator([$torunOffice]);

    $alert = $finder->calculate(53.02879633770021, 18.634435851394855, 0.1, 10);

    self::assertEquals(5, $alert);
  }

  public function testFor2Response()
  {
    $torunOffice = ['lat' => 53.02879633770021, 'long' => 18.63850934547079];
    $finder = new GasAlertCalculator([$torunOffice]);

    $alert = $finder->calculate(53.02879633770021, 18.634435851394855, 0.8, 10);

    self::assertEquals(0, $alert);
  }
}
