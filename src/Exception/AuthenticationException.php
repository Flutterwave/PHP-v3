<?php

declare(strict_types=1);

namespace Flutterwave\Exception;

class AuthenticationException extends \Exception
{
    public function invalidBearerToken(): void
    {
        $this->message = "Invalid Secret Key passed.";
    }

    public function unauthorizedAccess(): void
    {
        $this->message = "You currently do not have permission to access this feature.
         kindly reachout to the Account owner.";
    }
}
