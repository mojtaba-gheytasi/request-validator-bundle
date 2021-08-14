<?php

declare(strict_types=1);

namespace MojtabaGheytasi\RequestValidatorBundle\Tests\Fixtures\Request;

use MojtabaGheytasi\RequestValidatorBundle\Contract\FailedValidationInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class CustomFailedValidation implements FailedValidationInterface
{
    public function onFailedValidation(array $errors)
    {
        throw new ValidatorException();
    }
}
