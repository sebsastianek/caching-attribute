<?php

namespace Tests\Cache;

use CachingAttribute\Cache\CacheFactory;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;


class CacheFactoryTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testGetCacheCreatesNewInstance(): void
    {
        $cacheBuilder = $this->createCallableMock();
        $cacheBuilder->expects($this->once())
            ->method('__invoke')
            ->with('testCache')
            ->willReturn($this->createMock(CacheItemPoolInterface::class));

        $cacheFactory = new CacheFactory($cacheBuilder);

        $cache = $cacheFactory->getCache('testCache');

        $this->assertInstanceOf(CacheItemPoolInterface::class, $cache);
    }

    /**
     * @throws Exception
     */
    public function testGetCacheReusesExistingInstance(): void
    {
        $cacheBuilder = $this->createCallableMock();
        $cacheBuilder->expects($this->once())
            ->method('__invoke')
            ->with('sharedCache')
            ->willReturn($this->createMock(CacheItemPoolInterface::class));

        $cacheFactory = new CacheFactory($cacheBuilder);

        $cache1 = $cacheFactory->getCache('sharedCache');
        $cache2 = $cacheFactory->getCache('sharedCache');

        $this->assertSame($cache1, $cache2);
    }

    public function testGetCacheCreatesDifferentInstancesForDifferentNames(): void
    {
        $cacheBuilder = $this->createCallableMock();

        $cacheBuilder->expects($this->exactly(2))
            ->method('__invoke')
            ->with($this->callback(function ($name) {
                return in_array($name, ['cacheA', 'cacheB'], true);
            }))
            ->willReturnCallback(function ($name) {
                return $this->createMock(CacheItemPoolInterface::class);
            });

        $cacheFactory = new CacheFactory($cacheBuilder);

        $cacheA = $cacheFactory->getCache('cacheA');
        $cacheB = $cacheFactory->getCache('cacheB');

        $this->assertNotSame($cacheA, $cacheB);
    }

    /**
     * @throws Exception
     */
    private function createCallableMock(): mixed
    {
        return $this->createMock(CallableInterface::class);
    }
}
