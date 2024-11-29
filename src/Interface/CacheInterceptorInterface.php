<?php

namespace CachingAttribute\Interface;

interface CacheInterceptorInterface
{
    public function intercept(object $object, string $method, array $args): mixed;
}
