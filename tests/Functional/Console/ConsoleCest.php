<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Console;

use Mock\ProjectA\MockService;
use Psy\Shell;
use Ricotta\App\Module\Console\Console;
use Ricotta\App\Module\Console\ConsoleModule;
use Ricotta\App\Tests\Support\FunctionalTester;
use Ricotta\Container\Container;
use RuntimeException;

class ConsoleCest
{
    public function outputHelpScreen(FunctionalTester $I): void
    {
        $I->runShellCommand('bin/ricotta');

        $I->seeResultCodeIs(0);

        $expected = <<<SHELL
        The following arguments are required: [-c command name].
        Usage: bin/ricotta [-c command name]

        Required Arguments:
        \t-c command name
        \t\tThe name of the command to run
        Available commands:
        ****************************************
        a:test-command     Run the test command from mock/project-a
        ****************************************
        SHELL;

        $I->seeInShellOutput($expected);
    }

    public function registerWrongType(FunctionalTester $I): void
    {
        $I->getApp()->add(new ConsoleModule());
        $I->getApp()
            ->bootstrap[Console::class]
            ->configure(fn (Console $console) => $console->register(MockService::class));

        $container = new Container($I->getApp()->bootstrap);

        $I->expectThrowable(RuntimeException::class, fn () => $container->get(Console::class));
    }

    public function callTestCommand(FunctionalTester $I): void
    {
        $I->runShellCommand("bin/ricotta -c a:test-command");

        $I->seeResultCodeIs(0);
        $I->seeInShellOutput("Output from test command from mock/project-a");

        $I->runShellCommand("bin/ricotta -c a:test-command -o=\"optional value\"");

        $I->seeResultCodeIs(0);
        $I->seeInShellOutput("Output from test command from mock/project-a");
        $I->seeInShellOutput("optional value");
    }
}
