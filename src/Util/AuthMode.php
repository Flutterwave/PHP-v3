<?php

namespace Flutterwave\Util;

class AuthMode
{
    const PIN = "pin";
    const REDIRECT = "redirect";
    const VALIDATE = "validate";
    const CALLBACK = "callback";
    const OTP = "otp";
    const USSD = "ussd";
    const AVS = "avs_noauth";
}