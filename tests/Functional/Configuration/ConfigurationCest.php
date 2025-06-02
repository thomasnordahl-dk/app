<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Configuration;

use Ricotta\App\Module\Configuration\Configuration;
use Ricotta\App\Module\Configuration\ConfigurationException;
use Ricotta\App\Module\Configuration\JsonConfiguration;
use Ricotta\App\Tests\Support\FunctionalTester;
use Ricotta\Container\Container;

class ConfigurationCest
{
    public function jsonConfiguration(FunctionalTester $I): void
    {
        $I->getApp()
            ->bootstrap[Configuration::class]
            ->register()
            ->callback(fn () => new JsonConfiguration(__DIR__ . "/test-config.json"));

        $container = new Container($I->getApp()->bootstrap);

        $container->call(function (Configuration $configuration) use ($I) {
            $I->assertSame("hello", $configuration->string("ricotta.test.string"));
            $I->assertSame(2, $configuration->int("ricotta.test.int"));
            $I->assertSame(2.2, $configuration->float("ricotta.test.float"));
            $I->assertSame(false, $configuration->bool("ricotta.test.bool1"));
            $I->assertSame(true, $configuration->bool("ricotta.test.bool2"));
            $I->assertSame(
                [
                    "test",
                    3,
                    3.3,
                    [
                        "indexed" => "value"
                    ]
                ],
                $configuration->array("ricotta.test.array")
            );

            $I->expectThrowable(
                ConfigurationException::class,
                fn () => $configuration->int("ricotta.test.string")
            );

            $I->expectThrowable(
                ConfigurationException::class,
                fn () => $configuration->float("ricotta.test.string")
            );

            $I->expectThrowable(
                ConfigurationException::class,
                fn () => $configuration->bool("ricotta.test.string")
            );

            $I->expectThrowable(
                ConfigurationException::class,
                fn () => $configuration->array("ricotta.test.string")
            );


            $I->expectThrowable(
                ConfigurationException::class,
                fn () => $configuration->string("ricotta.test.int")
            );

            $I->expectThrowable(
                ConfigurationException::class,
                fn () => $configuration->int("ricotta.test.float")
            );

            $I->assertSame(2.0, $configuration->float("ricotta.test.int"));

            $I->expectThrowable(
                ConfigurationException::class,
                fn () => $configuration->string("ricotta.test.nonexistent")
            );
        });
    }

    public function nonExistentConfiguration(FunctionalTester $I): void
    {
        $I->getApp()
            ->bootstrap[Configuration::class]
            ->register()
            ->callback(fn () => new JsonConfiguration(__DIR__ . "/doesnt-exist-config.json"));

        $container = new Container($I->getApp()->bootstrap);

        $container->call(function (Configuration $configuration) use ($I) {
            $I->expectThrowable(ConfigurationException::class, fn () => $configuration->int("ricotta.test.int"));
        });
    }

    public function badConfiguration(FunctionalTester $I): void
    {
        $I->getApp()
            ->bootstrap[Configuration::class]
            ->register()
            ->callback(fn () => new JsonConfiguration(__DIR__ . "/bad-config.json"));

        $container = new Container($I->getApp()->bootstrap);

        $container->call(function (Configuration $configuration) use ($I) {
            $I->expectThrowable(ConfigurationException::class, fn () => $configuration->int("ricotta.test.int"));
        });
    }


    public function invalidJsonConfiguration(FunctionalTester $I): void
    {
        $I->getApp()
            ->bootstrap[Configuration::class]
            ->register()
            ->callback(fn () => new JsonConfiguration(__DIR__ . "/invalid-json-config.json"));

        $container = new Container($I->getApp()->bootstrap);

        $container->call(function (Configuration $configuration) use ($I) {
            $I->expectThrowable(ConfigurationException::class, fn () => $configuration->int("ricotta.test.int"));
        });
    }
}
