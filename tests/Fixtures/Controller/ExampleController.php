<?php

declare(strict_types=1);

namespace MojtabaGheytasi\RequestValidatorBundle\Tests\Fixtures\Controller;

use MojtabaGheytasi\RequestValidatorBundle\Tests\Fixtures\Request\ExampleRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ExampleController
{
    /**
     * @Route("/api/search", methods={"GET"})
     */
    public function search(ExampleRequest $request): JsonResponse
    {
        if ($request->hasError()) {
            return new JsonResponse($request->getErrors());
        }

        return new JsonResponse($request->validated());
    }

    /**
     * @Route("/api/store", methods={"POST"})
     */
    public function store(ExampleRequest $request): JsonResponse
    {
        if ($request->hasError()) {
            return new JsonResponse($request->getErrors());
        }

        return new JsonResponse($request->validated());
    }
}
