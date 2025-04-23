<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Template;

use Closure;
use Ricotta\Container\Container;
use Throwable;

class TemplateEngine
{
    /**
     * @var array<string, list<string>> $packagePaths
     */
    private array $packagePaths = [];

    public function __construct(private readonly Container $container)
    {
    }

    public function addPackagePath(string $packageName, string $rootPath): void
    {
        $this->packagePaths[$packageName] ??= [];

        array_unshift($this->packagePaths[$packageName], $rootPath);
    }

    /**
     * @param string               $fileName
     * @param string               $packageName
     * @param array<string, mixed> $injections
     *
     * @return string
     * @throws TemplateException
     */
    public function render(string $fileName, string $packageName, array $injections = []): string
    {
        $startLevel = ob_get_level();
        ob_start();

        try {
            $filePath = $this->getFilePath($fileName, $packageName);
            extract($injections);

            $value = include $filePath;

            if (is_callable($value)) {
                $this->container->call(Closure::fromCallable($value), $injections);
            }

            $contents = ob_get_clean() ?: '';

            return $contents;
        } catch (Throwable $error) {
            while (ob_get_level() > $startLevel) {
                ob_end_clean();
            }

            throw $error;
        }
    }

    /**
     * @throws TemplateException
     */
    private function getFilePath(string $fileName, string $packageName): string
    {
        foreach ($this->packagePaths[$packageName] ?? [] as $path) {
            $filePath = "{$path}/{$fileName}.php";

            if (file_exists($filePath)) {
                return $filePath;
            }


            $filePath = "{$path}/{$fileName}.html";

            if (file_exists($filePath)) {
                return $filePath;
            }
        }

        return throw new TemplateException(
            "Could not find template {$fileName}\n" . implode("\n", $this->packagePaths[$packageName] ?? [])
        );
    }
}
