<?php

declare(strict_types=1);

namespace Mock\ProjectA;

use League\CLImate\CLImate;
use Ricotta\App\Module\Console\CliMateFactory;
use Ricotta\App\Module\Console\Command;

class TestCommand implements Command
{
    public static function getName(): string
    {
        return "a:test-command";
    }

    public static function getDescription(): string
    {
        return "Run the test command from mock/project-a";
    }

    public function run(): void
    {
        $climate = new CLImate();

        $climate->arguments->add([
            'optional' => [
                'prefix' => 'o',
            ]
        ]);
        $climate->output("Output from test command from mock/project-a");

        $climate->arguments->parse();

        if ($climate->arguments->defined("optional")) {
            $climate->output($climate->arguments->get("optional"));
        }
    }
}
