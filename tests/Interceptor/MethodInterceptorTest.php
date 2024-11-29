<?php

namespace Tests\Interceptor;

use CachingAttribute\Interceptor\CacheInterceptor;
use CachingAttribute\Interceptor\MethodInterceptor;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class MethodInterceptorTest extends TestCase
{
    /**
     * @throws \ReflectionException
     * @throws Exception
     */
    public function testInterceptsAnnotatedMethod(): void
    {
        $realTarget = new SampleService();
        $mockCacheInterceptor = $this->createMock(CacheInterceptor::class);

        $mockCacheInterceptor->expects($this->once())
            ->method('intercept')
            ->with($realTarget, 'annotatedMethod', ['arg1'])
            ->willReturn('cached-result');

        $interceptor = new MethodInterceptor($realTarget, $mockCacheInterceptor);

        $result = $interceptor->__call('annotatedMethod', ['arg1']);

        $this->assertSame('cached-result', $result);
    }

    /**
     * @throws \ReflectionException
     * @throws Exception
     */
    public function testCallsNonAnnotatedMethodDirectly(): void
    {
        $realTarget = new SampleService();

        $mockCacheInterceptor = $this->createMock(CacheInterceptor::class);
        $mockCacheInterceptor->expects($this->never())
            ->method('intercept');

        $interceptor = new MethodInterceptor($realTarget, $mockCacheInterceptor);

        $result = $interceptor->__call('nonAnnotatedMethod', ['arg1']);
        $this->assertSame('original-result', $result);
    }
}
