<?php

namespace CachingAttribute\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Cached
{
    public function __construct(
        public int $ttl = 3600,
        public bool $userSpecific = false
    ) {}
}
