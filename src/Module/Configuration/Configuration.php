<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Configuration;

/**
 * Interface for fetching configuration values
 *
 * Implementations should throw ConfigurationExceptions on mismatching types or undefined values.
 */
interface Configuration
{
    /**
     * @throws ConfigurationException
     */
    public function int(string $name): int;

    /**
     * @throws ConfigurationException
     */
    public function float(string $name): float;

    /**
     * @throws ConfigurationException
     */
    public function bool(string $name): bool;

    /**
     * @throws ConfigurationException
     */
    public function string(string $name): string;

    /**
     * @return array<mixed>
     * @throws ConfigurationException
     */
    public function array(string $name): array;
}
