<?php

declare(strict_types=1);

namespace App\Bootstrap;

use RuntimeException;

final class Container
{
    /** @var array<string, mixed> */
    private array $parameters;

    /** @var array<string, callable(self): mixed> */
    private array $factories;

    /** @var array<string, mixed> */
    private array $instances = [];

    /**
     * @param array<string, mixed> $parameters
     * @param array<string, callable(self): mixed> $factories
     */
    public function __construct(array $parameters = [], array $factories = [])
    {
        $this->parameters = $parameters;
        $this->factories = $factories;
    }

    public function get(string $id): mixed
    {
        if (array_key_exists($id, $this->parameters)) {
            return $this->parameters[$id];
        }

        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }

        if (array_key_exists($id, $this->factories)) {
            $this->instances[$id] = ($this->factories[$id])($this);

            return $this->instances[$id];
        }

        throw new RuntimeException('Service not found: ' . $id);
    }

    public function set(string $id, mixed $value): void
    {
        $this->instances[$id] = $value;
    }

    public function bind(string $id, callable $factory): void
    {
        $this->factories[$id] = $factory;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->parameters) || array_key_exists($id, $this->instances) || array_key_exists($id, $this->factories);
    }
}