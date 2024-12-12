<?php

namespace CachingAttribute\Trait;


use CachingAttribute\Interceptor\CacheInterceptor;
use Psr\Cache\InvalidArgumentException;

trait CachingTrait {
    private CacheInterceptor $cacheInterceptor;

    public function setCacheInterceptor(CacheInterceptor $cacheInterceptor): void {
        $this->cacheInterceptor = $cacheInterceptor;
    }

    /**
     * @throws \ReflectionException
     * @throws InvalidArgumentException
     */
    public function __call(string $method, array $args): mixed {
        if (method_exists($this, $method)) {
            return $this->cacheInterceptor->intercept($this, $method, $args);
        }

        throw new \BadMethodCallException("Method {$method} does not exist");
    }
}
