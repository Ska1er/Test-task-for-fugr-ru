<?php
namespace App\Services;

use DateTime;

class Validator {
    
    public function verifyDate($date): DateTime|bool
    {
        $dateTime = DateTime::createFromFormat('Y-m-d', $date);
        $errors = DateTime::getLastErrors();
        if (!empty($errors['warning_count'])) {
            return false;
        }
        return $dateTime;
    }
}