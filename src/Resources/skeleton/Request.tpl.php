<?php echo "<?php\n"; ?>

namespace <?php echo $namespace; ?>;

use MojtabaGheytasi\RequestValidatorBundle\Request\RequestWithValidation;

final class <?php echo $class_name; ?> extends RequestWithValidation
{
    /**
    * Get the validation constraints that apply to the request.
    */
    protected function constraints(): array
    {
        return [
            // TODO: write your constraints here (https://github.com/mojtaba-gheytasi/request-validator-bundle#usage)
        ];
    }
}
