<?php

namespace App\Security;

use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CredentialsInterface;

class EmptyPassword implements CredentialsInterface
{
    public function isResolved(): bool
    {
        return true;
    }
}
