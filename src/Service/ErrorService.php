<?php

namespace App\Service;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ErrorService
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