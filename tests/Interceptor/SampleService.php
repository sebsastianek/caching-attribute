<?php

namespace Tests\Interceptor;

use CachingAttribute\Attribute\Cached;

class SampleService
{
    #[Cached(ttl: 600, userSpecific: true)]
    public function annotatedMethod(string $arg): string
    {
        return "original-result";
    }

    public function nonAnnotatedMethod(string $arg): string
    {
        return "original-result";
    }
}
