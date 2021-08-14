<h2 align="center">
Symfony Request Validator Bundle
</h2>

<h5 align="center">For every Symfony clean coder and SOLID lover</h5>

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mojtaba-gheytasi/request-validator-bundle/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/mojtaba-gheytasi/request-validator-bundle/?branch=main)
[![Build Status](https://scrutinizer-ci.com/g/mojtaba-gheytasi/request-validator-bundle/badges/build.png?b=main)](https://scrutinizer-ci.com/g/mojtaba-gheytasi/request-validator-bundle/build-status/main)

<h2>What does it do? :)</h2>

This bundle allows you to validate request parameters based on your constraints and restrictions via request classes that contain validation logic.

<h2>Installation</h2>

```bash
composer require mojtaba-gheytasi/request-validator-bundle
```

<h2>Compatibility</h2>

* PHP v7.4 or above
* Symfony v4.4 or above

<h2>Usage</h2>

Suppose you want to validate incoming request parameters as follows:

```php
//Get api/product/search?brand=gucci&price[min]=100&price[max]=1000
[
    'brand' => 'gucci',
    'price' => [
        'min' => 100,
        'max' => 1000,
    ],
];
```
To start, create a request class by following command:
```console
$ bin/console make:request SearchProduct
```
**NOTE**: Creates SearchProductRequest.php in src/Request.

Now add your validation constraints to the constraints method:
```php
<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Positive;
use MojtabaGheytasi\RequestValidatorBundle\Request\RequestWithValidation;

class SearchProductRequest extends RequestWithValidation
{
    /**
     * Get the validation constraints that apply to the request.
     */
    protected function constraints(): array
    {
        return [
            'brand' => [
                new Length(['min' => 2]),
                new Type('string'),
            ],
            'price'  => new Collection([
                'fields' => [
                    'min' => [
                        new Type('integer'),
                        new Positive(),
                    ],
                    'max' => [
                        new Type('integer'),
                        new Positive(),
                    ],
                ],
            ]),
        ];
    }
}
```
So, how are the validation constraints evaluated? All you need to do is type-hint the request on your controller method. The incoming request data is validated before the controller method is called, meaning you do not need to clutter your controller with validation logic:

```php
<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductController 
{
    public function search(SearchProductRequest $request): Response
    {
        if ($request->hasError()) {
            return new JsonResponse($request->getErrors(), Response::HTTP_BAD_REQUEST);
        }
    
        // The incoming request is valid...
    
        // Retrieve one of the validated input data...
        $brand = $request->validated('brand'); //gucci
    
        // Retrieve all of the validated input data...
        $validated = $request->validated();
    //    [
    //        'brand' => 'gucci',
    //        'price' => [
    //            'min' => 100,
    //            'max' => 1000,
    //        ]
    //    ]
    }
}
```
If validate incoming request parameters as follows, can see error messages:

```php
    //Get api/product/search?brand=a&price[min]=dummy&price[max]=-10.5
    [
        'brand' => 'a',
        'price' => [
            'min' => 'dummy',
            'max' => -10.5,
        ],
    ];
```
```php
<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class ProductController 
{
    public function search(SearchProductRequest $request): Response
    {
        $request->hasError(); // true
        
        $request->getErrors(); // returns following array
//        [
//            "brand": [
//                "This value is too short. It should have 2 characters or more."
//            ],
//            "price": [
//                "min": [
//                    "This value should be of type integer.",
//                    "This value should be positive."
//                ],
//                "max": [
//                    "This value should be of type integer.",
//                    "This value should be positive."
//                ]
//            ]
//        ]   
    }
}
```
<h2>Suggestion</h2>
It's better to prevent duplicate below code in your controller methods,

```php
if ($request->hasError()) {
    return new JsonResponse($request->getErrors(), Response::HTTP_BAD_REQUEST);
}
```
RequestValidatorBundle provides a good solution for doing this, first create a class that implements MojtabaGheytasi\RequestValidatorBundle\Contract\FailedValidationInterface and writes your logic in the onFailedValidation method:
```php
<?php

namespace App\Request\CustomValidation;

use MojtabaGheytasi\RequestValidatorBundle\Contract\FailedValidationInterface;

class FailedValidation implements FailedValidationInterface
{
    public function onFailedValidation(array $errors)
    {
        // Recommend throw an custom exception and handle it with symfony listeners (listen on ExceptionEvent)
        // You can find more details on https://symfony.com/doc/4.4/event_dispatcher.html#creating-an-event-listener
    }
}
```

Now just make a yaml file in config/packages/ directory and define the above class to RequestValidatorBundle like the following:

```yaml
request_validator:
    failed_validation: 'App\Request\CustomValidation'
```

After doing these, when the request has an error, RequestValidatorBundle immediately executes the onFailedValidation method, and if you throw an exception or anyway make a response in the onFailedValidation method, the controller method codes don't execute. so you can write controller methods like below:

```php
<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class ProductController 
{
    public function search(SearchProductRequest $request): Response
    {
        // The incoming request is valid...
    
        // Retrieve one of the validated input data...
        $brand = $request->validated('brand'); //gucci
    }
}

```

<h2>Contributing <img class="emoji" alt="raising_hand" height="20" width="20" src="https://github.githubassets.com/images/icons/emoji/unicode/1f64b.png">
</h2> 

If you find an issue, or have a better way to do something, feel free to open an issue or a pull request.
