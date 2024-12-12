<?php

namespace CachingAttribute\Trait;


use CachingAttribute\Interface\CacheInterceptorInterface;

trait CachingTrait {
    private CacheInterceptorInterface $cacheInterceptor;

    public function setCacheInterceptor(CacheInterceptorInterface $cacheInterceptor): void {
        $this->cacheInterceptor = $cacheInterceptor;
    }

    public function __call(string $method, array $args): mixed {
        if (method_exists($this, $method)) {
            return $this->cacheInterceptor->intercept($this, $method, $args);
        }

        throw new \BadMethodCallException("Method {$method} does not exist");
    }
}
