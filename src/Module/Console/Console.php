<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Console;

use Closure;
use League\CLImate\Exceptions\InvalidArgumentException;
use RuntimeException;

class Console
{
    /**
     * @var list<string> $commandNames
     */
    private array $commandNames = [];

    /**
     * @var array<string, class-string<Command>> $commandClasses
     */
    private array $commandClasses = [];

    /**
     * @var array<string, string> $commandDescriptions
     */
    private array $commandDescriptions = [];

    public function __construct(private ClimateFactory $climateFactory, private Closure $resolveCommand)
    {
    }

    /**
     * @param class-string<Command> $class
     */
    public function register(string $class): void
    {
        if (! is_subclass_of($class, Command::class)) {
            throw new RuntimeException("{$class} must implement " . Console::class);
        }

        $name = $class::getName();
        $description = $class::getDescription();

        $this->commandNames[] = $name;
        $this->commandClasses[$name] = $class;
        $this->commandDescriptions[$name] = $description;
    }

    public function run(): void
    {
        $climate = $this->climateFactory->create();

        $climate->arguments->add([
            'command name' => [
                'prefix' => 'c',
                'description' => 'The name of the command to run',
                'required' => true
            ]
        ]);

        try {
            $climate->arguments->parse();
        } catch (InvalidArgumentException $exception) {
            $commandList = [];

            foreach ($this->commandNames as $name) {
                $commandList[] = [$name, $this->commandDescriptions[$name]];
            }

            $climate->error($exception->getMessage());
            $climate->green()->usage();
            $climate->green()->info('Available commands:');
            $climate->green()->border('*', 40);
            $climate->green()->columns($commandList);
            $climate->green()->border('*', 40);

            return;
        }

        $name = $climate->arguments->get('command name');

        $class = $this->commandClasses[$name];

        $command = ($this->resolveCommand)($class);

        $command->run();
    }
}
