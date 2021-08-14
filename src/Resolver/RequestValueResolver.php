<?php

declare(strict_types=1);

namespace MojtabaGheytasi\RequestValidatorBundle\Resolver;

use Generator;
use MojtabaGheytasi\RequestValidatorBundle\Request\RequestWithValidation;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class RequestValueResolver implements ArgumentValueResolverInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return is_subclass_of($argument->getType(), RequestWithValidation::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        $requestClass = $argument->getType();

        $failedValidationObject = $this->container->has('request_validator.failed_validation') ?
                                   $this->container->get('request_validator.failed_validation') :
                                   null;

        yield new $requestClass(
            $failedValidationObject,
            $request->query->all(),
            $request->request->all(),
            $request->attributes->all(),
            $request->cookies->all(),
            $request->files->all(),
            $request->server->all(),
            $request->getContent()
        );
    }
}
