<?php

namespace CachingAttribute\Interface;

interface UserIdentityProviderInterface
{
    public function getUserIdentifier(): string;
}
