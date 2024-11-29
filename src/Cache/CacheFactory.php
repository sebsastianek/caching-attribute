<?php

namespace CachingAttribute\Cache;

use CachingAttribute\Interface\CacheFactoryInterface;
use Psr\Cache\CacheItemPoolInterface;

class CacheFactory implements CacheFactoryInterface
{
    private array $cacheInstances = [];
    private mixed $cacheBuilder;

    public function __construct(callable $cacheBuilder)
    {
        $this->cacheBuilder = $cacheBuilder;
    }

    public function getCache(string $name): CacheItemPoolInterface
    {
        if (!isset($this->cacheInstances[$name])) {
            $this->cacheInstances[$name] = ($this->cacheBuilder)($name);
        }

        return $this->cacheInstances[$name];
    }
}
