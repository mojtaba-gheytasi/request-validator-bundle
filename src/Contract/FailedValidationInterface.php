<?php

namespace MojtabaGheytasi\RequestValidatorBundle\Contract;

interface FailedValidationInterface
{
    public function onFailedValidation(array $errors);
}
