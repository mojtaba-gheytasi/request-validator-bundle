<?php

declare(strict_types=1);

namespace MojtabaGheytasi\RequestValidatorBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ExampleControllerTest extends WebTestCase
{
    public function test_it_can_resolve_request_successfully_and_return_errors(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/search');

        self::assertJsonStringEqualsJsonString(
            json_encode([
                'name' => ['This value should not be blank.'],
                'email' => ['This value should not be blank.'],
            ]),
            $client->getResponse()->getContent()
        );
    }

    public function test_it_can_resolve_request_successfully_and_return_validated_request_query_parameters(): void
    {
        $queryParams = [
            'name' => 'Robert Martin',
            'email' => 'Robert@gmail.com',
            'about' => 'Dummy text about Robert Martin',
        ];

        $client = static::createClient();
        $client->request('GET', '/api/search', $queryParams);

        self::assertJsonStringEqualsJsonString(
            json_encode($queryParams),
            $client->getResponse()->getContent()
        );
    }

    public function test_it_can_resolve_request_successfully_and_return_validated_request_parameters(): void
    {
        $body = [
            'name' => 'Robert Martin',
            'email' => 'Robert@gmail.com',
            'about' => 'Dummy text about Robert Martin',
        ];

        $client = static::createClient();
        $client->request('POST', '/api/store', $body);

        self::assertJsonStringEqualsJsonString(
            json_encode($body),
            $client->getResponse()->getContent()
        );
    }
}
