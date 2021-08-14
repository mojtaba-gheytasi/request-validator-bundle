<?php

declare(strict_types=1);

namespace MojtabaGheytasi\RequestValidatorBundle\Tests\Fixtures\Request;

use MojtabaGheytasi\RequestValidatorBundle\Request\RequestWithValidation;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class ExampleRequest extends RequestWithValidation
{
    protected function constraints(): array
    {
        return [
            'name' => [
                new NotBlank(),
                new Length(['min' => 10]),
            ],
            'email' => [
                new NotBlank(),
                new Email(),
            ],
            'about' => [
                new Type('string'),
            ],
        ];
    }
}
