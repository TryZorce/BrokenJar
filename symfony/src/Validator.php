<?php

namespace App;

class Validator {
    public static function isValidCardNumber(string $cardNumber): bool {
        $sum = 0;
        $shouldDouble = false;

        for ($i = strlen($cardNumber) - 1; $i >= 0; $i--) {
            $digit = intval($cardNumber[$i]);

            if ($shouldDouble) {
                $digit *= 2;
                if ($digit > 9) $digit -= 9;
            }

            $sum += $digit;
            $shouldDouble = !$shouldDouble;
        }

        return $sum % 10 === 0;
    }

    public static function isValidFineNumber(string $fineNumber): bool {
        $currentYear = date('Y');
        $regex = "/^(?:(?=([A-Y])([A-Z]))\\1\\2)" . $currentYear . "_(\\d{1,2})_(\\d{1,2})$/";
    
        if (preg_match($regex, $fineNumber, $matches)) {
            $firstLetter = $matches[1];
            $secondLetter = $matches[2];
    
            if (ord($firstLetter) >= ord($secondLetter)) {
                return false;
            }
    
            $firstDigitGroup = intval($matches[3]);
            $secondDigitGroup = intval($matches[4]);
            
            return $firstDigitGroup + $secondDigitGroup === 100;
        }
    
        return false;
    }
    
}
