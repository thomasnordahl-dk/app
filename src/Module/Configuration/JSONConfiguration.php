<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Configuration;

class JSONConfiguration implements Configuration
{
    /**
     * @var null|array<mixed>
     */
    private ?array $values = null;

    public function __construct(private readonly string $filePath)
    {
    }

    public function int(string $name): int
    {
        $value = $this->getValue($name);

        if (! is_int($value)) {
            throw new ConfigurationException("{$name} is not an integer value");
        }

        return $value;
    }
    public function float(string $name): float
    {
        $value = $this->getValue($name);

        if (! is_float($value) && ! is_int($value)) {
            throw new ConfigurationException("{$name} is not a valid float value");
        }

        return (float) $value;
    }

    public function bool(string $name): bool
    {
        $value = $this->getValue($name);

        if (! is_bool($value)) {
            throw new ConfigurationException("{$name} is not a boolean value");
        }

        return $value;
    }

    public function string(string $name): string
    {
        $value = $this->getValue($name);

        if (! is_string($value)) {
            throw new ConfigurationException("{$name} is not a string value");
        }

        return $value;
    }

    public function array(string $name): array
    {
        $value = $this->getValue($name);

        if (! is_array($value)) {
            throw new ConfigurationException("{$name} is not an array");
        }

        return $value;
    }

    private function loadValues(): void
    {
        if ($this->values === null) {
            if (! file_exists($this->filePath)) {
                throw new ConfigurationException("File not found {$this->filePath}");
            }

            $content = file_get_contents($this->filePath) ?: '{}';

            $values = json_decode($content, true);

            if (! is_array($values)) {
                throw new ConfigurationException("Invalid JSON in file {$this->filePath}");
            }

            $this->values = $values;
        }
    }

    private function getValue(string $name): mixed
    {
        $this->loadValues();

        $parts = explode(".", $name);

        $nested = $this->values;
        foreach ($parts as $part) {
            if (! is_array($nested) || ! array_key_exists($part, $nested)) {
                throw new ConfigurationException("{$name} is not defined");
            }

            $nested = $nested[$part];
        }

        return $nested;
    }
}
