<?php

declare(strict_types=1);

namespace MojtabaGheytasi\RequestValidatorBundle\Request;

use MojtabaGheytasi\RequestValidatorBundle\Contract\FailedValidationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */
abstract class RequestWithValidation extends Request
{
    protected array $constraints = [];

    protected array $validated = [];

    protected array $errors = [];

    private ?FailedValidationInterface $failedValidation;

    protected ValidatorInterface $validator;

    /**
     * {@inheritDoc}
     */
    public function __construct(
        FailedValidationInterface $failedValidation = null,
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ) {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);

        $this->failedValidation = $failedValidation;

        $this->handleValidationProcess();
    }

    /**
     * Get the validated data from the request.
     *
     * @return array|mixed
     */
    public function validated(string $key = null)
    {
        if (null === $key) {
            return $this->validated;
        }

        if (\array_key_exists($key, $this->validated)) {
            return $this->validated[$key];
        }

        throw new \InvalidArgumentException();
    }

    /**
     * Returns true if the request has validation errors, otherwise false.
     */
    public function hasError(): bool
    {
        return ! empty($this->errors);
    }

    /**
     * Return request validation errors.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    private function handleValidationProcess()
    {
        $this->validator = $this->getValidatorInstance();

        $this->constraints = $this->constraints();

        $request = $this->getPreparedRequest();

        $this->validate($request);

        if (
            $this->hasError() &&
            $this->failedValidation instanceof FailedValidationInterface
        ) {
            $this->failedValidation->onFailedValidation($this->getErrors());
        }
    }

    /**
     * Returns normalized and filtered current request.
     */
    private function getPreparedRequest(): array
    {
        $request = $this->getRequestData();

        $normalizedRequest = $this->normalizeRequest($request, $this->constraints);

        return $this->filterRequest($normalizedRequest);
    }

    /**
     * Validate the given request with the constraints.
     *
     * @param $request
     */
    private function validate($request): void
    {
        foreach ($request as $key => $value) {
            $violations = $this->validator->validate($value, $this->constraints[$key]);

            0 === $violations->count() ?
                $this->addToValidated($key, $value) :
                $this->addToErrors($key, $violations);
        }
    }

    /**
     * Get the validator instance for the request.
     */
    private function getValidatorInstance(): ValidatorInterface
    {
        return Validation::createValidator();
    }

    /**
     * Ignore the fields that are not provided in constraints and return filtered request.
     *
     * @param $request
     */
    private function filterRequest($request): array
    {
        return array_filter($request, function ($parameter) {
            return \array_key_exists($parameter, $this->constraints);
        }, \ARRAY_FILTER_USE_KEY);
    }

    /**
     * This method will merge missing constraints (from request) with current
     * request for further validation. This way validation will be based
     * on constraints and all constraints will be considered.
     */
    private function normalizeRequest(array $request, array $constraints): array
    {
        $constraints = array_fill_keys(array_keys($constraints), null);

        return array_merge($constraints, $request);
    }

    private function addToErrors(string $field, ConstraintViolationListInterface $violations): void
    {
        foreach ($violations as $violation) {
            if ($this->valueIsScalarType($violation)) {
                $this->addScalarParameterValueError($field, $violation);

                continue;
            }

            if ($this->valueIsFlatArray($violation)) {
                $this->addFlatArrayParameterValueError($field, $violation);

                continue;
            }

            if ($this->valueIs2DArray($violation)) {
                $this->add2DArrayParameterValueError($field, $violation);

                continue;
            }

            throw new \InvalidArgumentException();
        }
    }

    private function addScalarParameterValueError(string $field, ConstraintViolation $violation)
    {
        $this->errors[$field][] = $violation->getMessage();
    }

    private function addFlatArrayParameterValueError(string $field, ConstraintViolation $violation)
    {
        $key = $this->getKeyOfFlatArrayValue($violation->getPropertyPath());
        $this->errors[$field][$key][] = $violation->getMessage();
    }

    private function add2DArrayParameterValueError(string $field, ConstraintViolation $violation)
    {
        [$parentKey, $key] = $this->getKeysOf2DArrayValue($violation->getPropertyPath());
        $this->errors[$field][$parentKey][$key][] = $violation->getMessage();
    }

    /**
     * @param $value
     */
    private function addToValidated(string $parameter, $value): void
    {
        $this->validated[$parameter] = $value;
    }

    private function getKeyOfFlatArrayValue(string $name): string
    {
        return substr($name, 1, -1);
    }

    private function getKeysOf2DArrayValue(string $name): array
    {
        return explode('][', substr($name, 1, -1));
    }

    /**
     * Returns true if provided value of parameter is an scalar, otherwise false.
     */
    private function valueIsScalarType(ConstraintViolation $violation): bool
    {
        return '' === $violation->getPropertyPath();
    }

    /**
     * Returns true if provided value of parameter is an array, otherwise false.
     */
    private function valueIsArray(ConstraintViolation $violation, int $dimension): bool
    {
        return '' !== $violation->getPropertyPath() &&
            $dimension === substr_count($violation->getPropertyPath(), '[');
    }

    /**
     * Returns true if provided value of parameter is a flat array, otherwise false.
     */
    private function valueIsFlatArray(ConstraintViolation $violation): bool
    {
        return $this->valueIsArray($violation, 1);
    }

    /**
     * Returns true if provided value of parameter is a two dimensional (2D) array, otherwise false.
     */
    private function valueIs2DArray(ConstraintViolation $violation): bool
    {
        return $this->valueIsArray($violation, 2);
    }

    /**
     * Indicates whether current request has a form.
     */
    private function hasFormData(): bool
    {
        return ! \in_array($this->getRealMethod(), [self::METHOD_GET, self::METHOD_HEAD]);
    }

    /**
     * Get data to be validated from the request.
     */
    private function getRequestData(): array
    {
        return $this->hasFormData() ?
            array_merge($this->request->all(), $this->query->all()) :
            $this->query->all();
    }

    /**
     * Get the validation constraints that apply to the request.
     */
    abstract protected function constraints(): array;
}
