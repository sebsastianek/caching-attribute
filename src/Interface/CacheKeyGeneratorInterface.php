<?php

namespace CachingAttribute\Interface;

interface CacheKeyGeneratorInterface
{
    public function generate(string $method, array $args, bool $userSpecific): string;
}
