<?php

namespace App\Utils;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ErrorFormatter
{
    public function formatError(ConstraintViolationListInterface $errors): array
    {
        $validationError = array();

        foreach ($errors as $item) {
            $validationError[] = $item;
        }
        return $validationError;
    }
}