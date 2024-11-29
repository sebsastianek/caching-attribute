<?php

namespace Tests\Cache;

use CachingAttribute\Cache\CacheKeyGenerator;
use CachingAttribute\Implementation\DefaultUserIdentityProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class CacheKeyGeneratorTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testGenerateWithoutUserSpecificKey(): void
    {
        $mockUserIdentityProvider = $this->createMock(DefaultUserIdentityProvider::class);
        $mockUserIdentityProvider->expects($this->never())
            ->method('getUserIdentifier');

        $keyGenerator = new CacheKeyGenerator($mockUserIdentityProvider);

        $method = 'testMethod';
        $args = ['arg1', 'arg2'];
        $userSpecific = false;

        $key = $keyGenerator->generate($method, $args, $userSpecific);

        $expectedKey = $method . ':' . md5(serialize($args));
        $this->assertSame($expectedKey, $key);
    }

    /**
     * @throws Exception
     */
    public function testGenerateWithUserSpecificKey(): void
    {
        $mockUserIdentityProvider = $this->createMock(DefaultUserIdentityProvider::class);
        $mockUserIdentityProvider->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('user123'); // Simulated user identifier

        $keyGenerator = new CacheKeyGenerator($mockUserIdentityProvider);

        $method = 'testMethod';
        $args = ['arg1', 'arg2'];
        $userSpecific = true;

        $key = $keyGenerator->generate($method, $args, $userSpecific);

        $expectedKey = $method . ':' . md5(serialize($args)) . ':user123';
        $this->assertSame($expectedKey, $key);
    }

    /**
     * @throws Exception
     */
    public function testGenerateWithDifferentArguments(): void
    {
        $mockUserIdentityProvider = $this->createMock(DefaultUserIdentityProvider::class);

        $keyGenerator = new CacheKeyGenerator($mockUserIdentityProvider);

        $method = 'testMethod';

        $args1 = ['arg1', 'arg2'];
        $key1 = $keyGenerator->generate($method, $args1, false);

        $args2 = ['arg1', 'arg3'];
        $key2 = $keyGenerator->generate($method, $args2, false);

        $this->assertNotSame($key1, $key2);
    }
}
