<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Template;

class TemplateEngine
{   
    /**
     * @var array<string, list<string>> $packagePaths
     */
    private array $packagePaths = [];

    public function addPackagePath(string $packageName, string $rootPath): void
    {
        $this->packagePaths[$packageName] ??= [];

        array_unshift($this->packagePaths[$packageName], $rootPath);
    }

    public function render(string $fileName, string $packageName): string
    {
        $filePath = $this->getFilePath($fileName, $packageName);

        ob_start();

        include $filePath;

        return ob_get_clean() ?: '';
    }

    private function getFilePath(string $fileName, string $packageName): string
    {
        foreach ($this->packagePaths[$packageName] as $path) {
            $filePath = realpath("{$path}/{$fileName}") ?: '';

            if (file_exists($filePath)) {
                return $filePath;
            }
        }
        
        return '';
    }
}