<?php

declare(strict_types=1);

namespace Ricotta\App;

use Composer\InstalledVersions;
use InvalidArgumentException;
use Ricotta\App\Module\Console\ClimateFactory;
use Ricotta\App\Module\Console\Console;
use Ricotta\App\Module\Web\WebModule;
use Ricotta\App\Module\Web\Routes;
use Ricotta\App\Module\Web\Routing\Router;
use Ricotta\App\Module\Web\Server;
use Ricotta\App\Module\Module;
use Ricotta\App\Module\Template\TemplateModule;
use Ricotta\App\Utility\Environment;
use Ricotta\Container\Bootstrapping;
use Ricotta\Container\Container;

class App
{
    public const string MIDDLEWARE_STACK = 'ricotta.app.middleware_stack';

    public private(set) Bootstrapping $bootstrap;

    public private(set) Routes $routes;

    public function __construct(private Environment $environment = new Environment())
    {
        $router = new Router();
        $this->routes = $router;

        $this->bootstrap = new Bootstrapping();

        //TODO split up bootstrapping
        $this->bootstrap[ClimateFactory::class]->register();
        $this->bootstrap[Console::class]->register();

        $this->add(new WebModule($router));
        $this->add(new TemplateModule());
    }

    public function run(): void
    {
        $container = new Container($this->bootstrap);

        $container->call(fn(Server $server) => $server->dispatch());
    }

    public function add(Module $module): void
    {
        $module->register($this);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function load(string $path): void
    {
        if (! file_exists($path) || substr($path, -3) !== 'php') {
            throw new InvalidArgumentException("{$path} is not a PHP file");
        }

        loadFileOutOfClassScope($this, $path);
    }

    public function loadModules(): void
    {
        $packagePaths = $this->getRicottaPackagePaths();

        foreach ($packagePaths as $packagePath) {
            $this->loadPackageFile($packagePath, "common.php");

            if ($this->environment->isCli()) {
                $this->loadPackageFile($packagePath, "cli.php");
            } else {
                $this->loadPackageFile($packagePath, "web.php");
            }
        }
    }

    private function loadPackageFile(string $packagePath, string $fileName): void
    {
        $filePath = $packagePath . DIRECTORY_SEPARATOR . $fileName;

        if (file_exists($filePath)) {
            loadFileOutOfClassScope($this, $filePath);
        }
    }

    /**
     * @return string[]
     */
    private function getRicottaPackagePaths(): array
    {
        $paths = array_map(
            fn ($packageName) => InstalledVersions::getInstallPath($packageName),
            InstalledVersions::getInstalledPackagesByType('ricotta')
        );

        return array_filter($paths, fn ($value) => $value !== null);
    }
}

/**
 * Load the file with the $app bound to the scope.
 *
 * Ideally the path would not be bound to the file scope at all, but this can only be done using eval(), so an
 * obscure variable name is used instead as the lesser evil.
 */
function loadFileOutOfClassScope(App $app, string $___path): void
{
    require $___path;
}
