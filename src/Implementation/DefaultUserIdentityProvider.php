<?php

namespace CachingAttribute\Implementation;

use CachingAttribute\Interface\UserIdentityProviderInterface;

class DefaultUserIdentityProvider implements UserIdentityProviderInterface
{
    public function getUserIdentifier(): string
    {
        return $_SESSION['user_id'] ?? 'guest';
    }
}
