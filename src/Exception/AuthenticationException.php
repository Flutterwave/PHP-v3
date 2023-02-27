<?php

declare(strict_types=1);

namespace Flutterwave\Exception;

class AuthenticationException extends \Exception
{
    public function InvalidBearerToken() {
        $this->message = "Invalid Secret Key passed.";
    }

    public function UnauthorizedAccess() {
        $this->message = "You currently do not have permission to access this feature. kindly reachout to the Account owner.";
    }
}
