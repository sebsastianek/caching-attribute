<?php

namespace Tests\Cache;

interface CallableInterface
{
    public function __invoke(string $name): mixed;
}
