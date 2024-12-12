<?php

namespace CachingAttribute\Interceptor;

use CachingAttribute\Interface\CacheInterceptorInterface;
use CachingAttribute\Interface\CacheKeyGeneratorInterface;
use CachingAttribute\Attribute\Cached;
use CachingAttribute\Cache\CacheFactory;
use Psr\Cache\InvalidArgumentException;
use ReflectionMethod;

class CacheInterceptor implements CacheInterceptorInterface
{
    private CacheFactory $cacheFactory;
    private CacheKeyGeneratorInterface $keyGenerator;

    public function __construct(CacheFactory $cacheFactory, CacheKeyGeneratorInterface $keyGenerator)
    {
        $this->cacheFactory = $cacheFactory;
        $this->keyGenerator = $keyGenerator;
    }

    /**
     * @throws \ReflectionException
     * @throws InvalidArgumentException
     */
    public function intercept(object $object, string $method, array $args): mixed
    {
        $reflection = new ReflectionMethod($object, $method);

        $attributes = $reflection->getAttributes(Cached::class);

        if (empty($attributes)) {
            return $reflection->invokeArgs($object, $args);
        }

        $cacheAttribute = $attributes[0]->newInstance();
        $cacheKey = $this->keyGenerator->generate($method, $args, $cacheAttribute->userSpecific);

        $cache = $this->cacheFactory->getCache('default');
        $cacheItem = $cache->getItem($cacheKey);

        if (!$cacheItem->isHit()) {
            $result = $reflection->invokeArgs($object, $args);
            $cacheItem->set($result)->expiresAfter($cacheAttribute->ttl);
            $cache->save($cacheItem);
            return $result;
        }

        return $cacheItem->get();
    }
}
