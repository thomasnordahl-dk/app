<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Template;

use Closure;
use Ricotta\Container\Container;

class TemplateEngine
{   
    public function __construct(private readonly Container $container) {}
    /**
     * @var array<string, list<string>> $packagePaths
     */
    private array $packagePaths = [];

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
        $filePath = $this->getFilePath($fileName, $packageName);

        ob_start();

        $value = include $filePath;

        if (is_callable($value)) {
            $this->container->call(Closure::fromCallable($value), $injections);
        }

        return ob_get_clean() ?: '';
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
        
        return throw new TemplateException("Could not find template {$fileName}\n" . implode("\n", $this->packagePaths[$packageName] ?? []));
    }
}