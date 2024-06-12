// tests/CardFineTest.php
<?php

use PHPUnit\Framework\TestCase;
use App\Validator;

class CardFineTest extends TestCase {
    /**
     * @covers \App\Validator::isValidCardNumber
     */
    public function testValidCardNumber() {
        $cardNumber1 = '4532015112830366';
        $cardNumber2 = '6011514433546201';

        // Enlever les printf pour éviter les tests risqués
        $this->assertTrue(Validator::isValidCardNumber($cardNumber1), sprintf("Card Number %s should be valid", $cardNumber1));
        $this->assertTrue(Validator::isValidCardNumber($cardNumber2), sprintf("Card Number %s should be valid", $cardNumber2));
    }

    /**
     * @covers \App\Validator::isValidCardNumber
     */
    public function testInvalidCardNumber() {
        $cardNumber1 = '4532015112830367';
        $cardNumber2 = '6011514433546202';

        $this->assertFalse(Validator::isValidCardNumber($cardNumber1), sprintf("Card Number %s should be invalid", $cardNumber1));
        $this->assertFalse(Validator::isValidCardNumber($cardNumber2), sprintf("Card Number %s should be invalid", $cardNumber2));
    }

    /**
     * @covers \App\Validator::isValidFineNumber
     */
    public function testValidFineNumber() {
        $year = date('Y');
        $fineNumber1 = "AB{$year}_50_50";
        $fineNumber2 = "CD{$year}_25_75";

        $this->assertTrue(Validator::isValidFineNumber($fineNumber1), sprintf("Fine Number %s should be valid", $fineNumber1));
        $this->assertTrue(Validator::isValidFineNumber($fineNumber2), sprintf("Fine Number %s should be valid", $fineNumber2));
    }

    /**
     * @covers \App\Validator::isValidFineNumber
     */
    public function testInvalidFineNumber() {
        $year = date('Y');
        $fineNumber1 = "AB{$year}_49_50";
        $fineNumber2 = "CD2023_20_80";
        $fineNumber3 = "FE{$year}_20_80";
        $fineNumber4 = "GH{$year}_00_00";

        $this->assertFalse(Validator::isValidFineNumber($fineNumber1), sprintf("Fine Number %s should be invalid", $fineNumber1));
        $this->assertFalse(Validator::isValidFineNumber($fineNumber2), sprintf("Fine Number %s should be invalid", $fineNumber2));
        $this->assertFalse(Validator::isValidFineNumber($fineNumber3), sprintf("Fine Number %s should be invalid", $fineNumber3));
        $this->assertFalse(Validator::isValidFineNumber($fineNumber4), sprintf("Fine Number %s should be invalid", $fineNumber4));
    }
}
