<?php

declare(strict_types=1);

namespace MojtabaGheytasi\RequestValidatorBundle\Tests\Unit;

use MojtabaGheytasi\RequestValidatorBundle\Tests\Fixtures\Request\CustomFailedValidation;
use MojtabaGheytasi\RequestValidatorBundle\Tests\Fixtures\Request\ExampleRequest;
use MojtabaGheytasi\RequestValidatorBundle\Tests\Fixtures\Request\ExampleRequestWithArrayParameter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\ValidatorException;

class RequestWithValidationTest extends TestCase
{
    public function test_it_can_validate_and_return_errors(): void
    {
        $expectedErrors = [
            'name' => ['This value should not be blank.'],
            'email' => ['This value should not be blank.'],
        ];

        $request = new ExampleRequest();

        self::assertEquals($expectedErrors, $request->getErrors());
    }

    public function test_it_can_validate_and_indicate_has_errors(): void
    {
        $request = new ExampleRequest();

        self::assertTrue($request->hasError());
    }

    public function test_it_fail_with_an_exception_when_input_key_is_not_exists_in_validated_request(): void
    {
        $queryParams = [
            'name' => 'Robert Martin',
            'email' => 'Robert@gmail.com',
        ];

        $request = new ExampleRequest(null, $queryParams);

        $this->expectException(\InvalidArgumentException::class);

        $request->validated('age');
    }

    public function test_it_can_validate_and_return_all_validated_parameters(): void
    {
        $queryParams = [
            'name' => 'Robert Martin',
            'email' => 'Robert@gmail.com',
            'about' => 'Dummy text',
        ];

        $request = new ExampleRequest(null, $queryParams);

        self::assertEquals(
            $queryParams,
            $request->validated()
        );
    }

    public function test_it_can_validate_and_return_a_validated_parameters(): void
    {
        $queryParams = [
            'name' => 'Robert Martin',
            'email' => 'Robert@gmail.com',
        ];

        $request = new ExampleRequest(null, $queryParams);

        self::assertEquals(
            $queryParams['name'],
            $request->validated('name')
        );
    }

    public function test_it_can_ignore_unnecessary_fields_which_sent_in_request()
    {
        $queryParams = [
            'name' => 'Robert Martin',
            'email' => 'Robert@gmail.com',
            'age' => 50,
        ];

        $request = new ExampleRequest(null, $queryParams);

        $this->expectException(\InvalidArgumentException::class);

        $request->validated('age');
    }

    public function test_it_can_normalize_requests_based_on_constraints_when_a_field_is_not_sent()
    {
        $queryParams = [
            'name' => 'Robert Martin',
            'email' => 'Robert@gmail.com',
        ];

        $request = new ExampleRequest(null, $queryParams);

        self::assertNull($request->validated('about'));
    }

    public function test_it_can_validate_request_with_array_parameter_and_return_validation_errors()
    {
        $queryParams = [
            'flatArray' => [
                'title' => 'Dummy title',
                'price' => 'WRONG VALUE',
            ],
            '2DArray' => [
                'title' => 'Dummy title',
                'attributes' => [
                    'color' => 'yellow',
                    'size' => 'WRONG VALUE',
                ],
            ],
        ];

        $expectedErrors = [
            'flatArray' => [
                'price' => ['This value should be of type integer.'],
            ],
            '2DArray' => [
                'attributes' => [
                    'size' => ['This value should be of type integer.'],
                ],
            ],
        ];

        $request = new ExampleRequestWithArrayParameter(null, $queryParams);

        self::assertEquals($expectedErrors, $request->getErrors());
    }

    public function test_it_can_validate_request_with_array_parameter_and_return_validated_parameters()
    {
        $queryParams = [
            'flatArray' => [
                'title' => 'Dummy title',
                'price' => 100,
            ],
            '2DArray' => [
                'title' => 'Dummy title',
                'attributes' => [
                    'color' => 'yellow',
                    'size' => 30,
                ],
            ],
        ];

        $request = new ExampleRequestWithArrayParameter(null, $queryParams);

        self::assertEquals($queryParams, $request->validated());
    }

    public function test_it_fails_and_execute_custom_failed_validation(): void
    {
        $this->expectException(ValidatorException::class);

        new ExampleRequest(new CustomFailedValidation());
    }
}
