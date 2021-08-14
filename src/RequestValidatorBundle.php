<?php

declare(strict_types=1);

namespace MojtabaGheytasi\RequestValidatorBundle;

use MojtabaGheytasi\RequestValidatorBundle\DependencyInjection\RequestValidatorExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RequestValidatorBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new RequestValidatorExtension();
        }

        return $this->extension;
    }
}
