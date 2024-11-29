<?php

namespace CachingAttribute\Interface;

use Psr\Cache\CacheItemPoolInterface;

interface CacheFactoryInterface
{
    public function getCache(string $name): CacheItemPoolInterface;
}
