<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Console;

use League\CLImate\Exceptions\InvalidArgumentException;

class Console
{
    public function __construct(private ClimateFactory $climateFactory) 
    {

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
            $climate->error($exception->getMessage());
            
            $climate->green()->usage();
            
            $climate->green()->info('Available commands:');
            $climate->green()->border('*', 40);
            $climate->green()->columns([
                ['mock:command1'],
                ['mock:command2'],
                ['mock2:command1'],
            ]);
            $climate->green()->border('*', 40);
        }
    }
}
