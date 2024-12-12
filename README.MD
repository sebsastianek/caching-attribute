# CachingAttribute Library

## Still in development!

## Overview

AnnotatedCache is a PHP library that provides an attribute-driven caching solution. 
It allows developers to cache function or method results dynamically based on provided attributes.

## Features

- **Attribute-Based Caching**: Use PHP attributes to define caching behavior on methods.
- **Custom Cache Key Generation**: Generate cache keys dynamically based on method parameters and user context.
- **Pluggable Implementations**: Easily integrate with frameworks like Laminas and Symfony.
- **User-Specific Caching**: Optionally cache results separately for different users.

## Installation

Install the library via Composer:

```bash
composer require annotated-cache
```

## Usage

### Example with a Plain PHP Service

Here’s a basic example of using AnnotatedCache with the CachingTrait in a standalone PHP application:

```php
use AnnotatedCache\Attributes\Cached; 
use AnnotatedCache\Cache\CacheFactory; 
use AnnotatedCache\Cache\CacheKeyGenerator; 
use AnnotatedCache\Interceptor\CacheInterceptor; 
use Psr\Cache\CacheItemPoolInterface; 
use AnnotatedCache\Trait\CachingTrait; 
 
class MyService 
{ 
    use CachingTrait; 
    
    #[Cached(ttl: 600, userSpecific: true)] 
    public function getData(string $key): string 
    { 
        return 'Data for ' . $key; 
    } 
} 
 
$cacheBuilder = function (string $name): CacheItemPoolInterface { 
    return new SomeCacheImplementation(); // Replace with actual PSR-6 implementation 
}; 

$userIdentityProvider = new SomeUserIdentityProvider(); // Replace with actual user provider 
$cacheFactory = new CacheFactory($cacheBuilder); 
$keyGenerator = new CacheKeyGenerator($userIdentityProvider); 
$cacheInterceptor = new CacheInterceptor($cacheFactory, $keyGenerator); 
 
$service = new MyService(); 
$service->setCacheInterceptor($cacheInterceptor); 
 
$result = $service->getData('myKey'); 
echo $result; // Cached result 
```

## Framework Integrations

### Laminas Integration

To integrate AnnotatedCache into a Laminas application, you can use a factory pattern with a delegator.

#### Configuration Example

```php
return [
    'dependencies' => [
        'factories' => [
            CacheInterceptor::class => function ($container) {
                return new CacheInterceptor(
                    $container->get(CacheFactory::class),
                    $container->get(CacheKeyGeneratorInterface::class)
                );
            },
        ],
        'delegators' => [
            MyService::class => [
                MethodInterceptorDelegatorFactory::class,
            ],
        ],
    ],
];
```

#### `MethodInterceptorDelegatorFactory`

```php
namespace MyApp\Factory;

use AnnotatedCache\Interceptor\MethodInterceptor;
use Psr\Container\ContainerInterface;

class MethodInterceptorDelegatorFactory
{
    public function __invoke(ContainerInterface $container, string $name, callable $callback)
    {
        $service = $callback();
        $cacheInterceptor = $container->get(CacheInterceptor::class);
        return new MethodInterceptor($service, $cacheInterceptor);
    }
}
```

### Symfony Integration

To integrate AnnotatedCache into a Symfony application, use a compiler pass to wrap your services.

#### Configuration Example

```yaml
services:
  AnnotatedCache\Interceptor\CacheInterceptor:
    arguments:
      $cacheFactory: '@App\Cache\CacheFactory'
      $keyGenerator: '@App\Service\CacheKeyGenerator'

  App\Service\MyService:
    tags:
      - { name: 'annotated_cache.intercept' }

  App\Service\AnotherService:
    tags:
      - { name: 'annotated_cache.intercept' }
```

#### Custom Compiler Pass

```php
namespace App\DependencyInjection\Compiler;

use AnnotatedCache\Interceptor\MethodInterceptor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AnnotatedCachePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('annotated_cache.intercept') as $serviceId => $tags) {
            $originalServiceId = $serviceId . '.original';
            $container->setAlias($originalServiceId, $serviceId);

            $container->getDefinition($serviceId)
                ->setDecoratedService($originalServiceId)
                ->setClass(MethodInterceptor::class)
                ->setArguments([
                    new Reference($originalServiceId),
                    new Reference('AnnotatedCache\Interceptor\CacheInterceptor'),
                ]);
        }
    }
}

```

#### Register the Compiler Pass

```php
// src/Kernel.php

use App\DependencyInjection\Compiler\AnnotatedCachePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    protected function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AnnotatedCachePass());
    }
}
```

## Testing

Run tests with PHPUnit:

```bash
vendor/bin/phpunit
```

## License

This library is open-sourced software licensed under the MIT license.

## Contributing

Contributions are welcome! If you’d like to contribute, please fork the repository and submit a pull request.

## Support

If you encounter any issues or have questions, feel free to open an issue in the GitHub repository.
