<?php

declare(strict_types=1);

namespace MojtabaGheytasi\RequestValidatorBundle\Tests\Fixtures\Request;

use MojtabaGheytasi\RequestValidatorBundle\Request\RequestWithValidation;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class ExampleRequestWithArrayParameter extends RequestWithValidation
{
    protected function constraints(): array
    {
        return [
            'flatArray' => new Collection([
                'fields' => [
                    'title' => [
                        new NotBlank(),
                        new Type('string'),
                    ],
                    'price' => [
                        new NotBlank(),
                        new Type('integer'),
                    ],
                ],
            ]),
            '2DArray' => new Collection([
                'fields' => [
                    'title' => [
                        new NotBlank(),
                        new Type('string'),
                    ],
                    'attributes' => new Collection([
                        'fields' => [
                            'color' => [
                                new NotBlank(),
                                new Type('string'),
                            ],
                            'size' => [
                                new NotBlank(),
                                new Type('integer'),
                            ],
                        ],
                    ]),
                ],
            ]),
        ];
    }
}
