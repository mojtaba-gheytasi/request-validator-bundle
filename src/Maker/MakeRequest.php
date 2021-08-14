<?php

namespace MojtabaGheytasi\RequestValidatorBundle\Maker;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

class MakeRequest extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:request';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a request validation class';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription(self::getCommandDescription())
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the request validation class (e.g. <fg=yellow>ExampleRequest</>)');

        $inputConfig->setArgumentAsNonInteractive('name');
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $storyClassNameDetails = $generator->createClassNameDetails(
            $input->getArgument('name'),
            'Request',
            'Request'
        );

        $generator->generateClass(
            $storyClassNameDetails->getFullName(),
            __DIR__.'/../Resources/skeleton/Request.tpl.php',
            []
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text([
            'Next: Open your request class and write your constraints.',
            'Find the documentation at https://github.com/mojtaba-gheytasi/request-validator-bundle#readme',
        ]);
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
    }
}
