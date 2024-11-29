<?php

namespace CachingAttribute\Cache;

use CachingAttribute\Interface\CacheKeyGeneratorInterface;
use CachingAttribute\Interface\UserIdentityProviderInterface;

class CacheKeyGenerator implements CacheKeyGeneratorInterface
{
    private UserIdentityProviderInterface $userIdentityProvider;

    public function __construct(UserIdentityProviderInterface $userIdentityProvider)
    {
        $this->userIdentityProvider = $userIdentityProvider;
    }

    public function generate(string $method, array $args, bool $userSpecific): string
    {
        $baseKey = $method . ':' . md5(serialize($args));

        if ($userSpecific) {
            $userId = $this->userIdentityProvider->getUserIdentifier();
            $baseKey .= ':' . $userId;
        }

        return $baseKey;
    }
}
