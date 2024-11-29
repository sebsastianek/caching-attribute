<?php

namespace CachingAttribute\Interceptor;

use CachingAttribute\Attributes\Cached;
use CachingAttribute\Interface\CacheInterceptorInterface;
use ReflectionMethod;

class MethodInterceptor
{
    private object $target;
    private CacheInterceptorInterface $cacheInterceptor;

    public function __construct(object $target, CacheInterceptorInterface $cacheInterceptor)
    {
        $this->target = $target;
        $this->cacheInterceptor = $cacheInterceptor;
    }

    /**
     * @throws \ReflectionException|\Psr\Cache\InvalidArgumentException
     */
    public function __call(string $method, array $arguments): mixed
    {
        $reflection = new ReflectionMethod($this->target, $method);

        $attributes = $reflection->getAttributes(Cached::class);

        if (!empty($attributes)) {
            return $this->cacheInterceptor->intercept($this->target, $method, $arguments);
        }

        return $reflection->invokeArgs($this->target, $arguments);
    }
}
