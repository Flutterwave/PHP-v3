<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\EventHandlers\EventTracker;
use Unirest\Exception;

class Otps extends Service
{
    use EventTracker;
    private string $name = 'otps';

    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
    }

    /**
     * @throws Exception
     */
    public function create(\Flutterwave\Payload $payload): \stdClass
    {
        $this->checkPayloadOTP($payload);

        $payload = $payload->toArray();

        $body = [
            'length' => $payload['length'],
            'customer' => [
                'name' => $payload['fullname'] ?? 'flw customer',
                'email' => $payload['email'],
                'phone' => $payload['phone_number'],
            ],
            'sender' => $payload['sender'] ?? 'Flutterwave-PHP',
            'send' => $payload['send'] ?? false,
            'medium' => $payload['medium'] ?? ['sms', 'whatsapp'],
        ];

        $this->logger->notice('OTP Service::Creating an OTP.');
        self::startRecording();
        $response = $this->request($body, 'POST', $this->name);
        $this->logger->notice('OTP Service::Created OTP Successfully.');
        self::setResponseTime();
        return $response;
    }

    public function validate(?string $otp = null, ?string $reference = null): \stdClass
    {
        if (is_null($otp)) {
            $this->logger->error('OTP Service::Please pass an OTP.');
            throw new \InvalidArgumentException('OTP Service::Please pass OTP.');
        }

        if (is_null($reference)) {
            $this->logger->error('OTP Service::Please pass a reference.');
            throw new \InvalidArgumentException('OTP Service::Please pass a reference.');
        }

        $body = ['otp' => $otp];
        $this->logger->notice('OTP Service::Validating OTP.');
        self::startRecording();
        $response = $this->request($body, 'POST', $this->name."/{$reference}/validate");
        $this->logger->notice('OTP Service::Validated OTP Successfully.');
        self::setResponseTime();
        return $response;
    }

    private function checkPayloadOTP(\Flutterwave\Payload $payload): void
    {
        if (! $payload->has('length')) {
            throw new \InvalidArgumentException("OTP Service:: Required Parameter 'length'.
          This is Integer length of the OTP being generated. Expected values are between 5 and 7.");
        }

        if (! $payload->has('customer')) {
            throw new \InvalidArgumentException("OTP Service:: Required Parameter 'customer'.
          This is customer object used to include the recipient information.");
        }

        if (! $payload->has('sender')) {
            throw new \InvalidArgumentException("OTP Service:: Required Parameter 'sender'.
          This is your merchant/business name. It would display when the OTP is sent.");
        }

        if (! $payload->has('send')) {
            throw new \InvalidArgumentException("OTP Service:: Required Parameter 'send'.
          Set to true to send otp to customer..");
        }

        if (! $payload->has('medium')) {
            throw new \InvalidArgumentException("OTP Service:: Required Parameter 'medium'.
          Pass the medium you want your customers to receive the OTP on. Expected values are sms, email and whatsapp.");
        }
    }
}
