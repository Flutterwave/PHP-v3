<?php

declare(strict_types=1);

namespace Flutterwave\Traits\PayloadOperations;

use Flutterwave\AbstractPayment;

trait Prepare
{
    /**
     * Generates a transaction reference number for the transactions
     *
     * @return Prepare|AbstractPayment
     */
    public function createReferenceNumber(): self
    {
        $this->logger->notice('Generating Reference Number....');
        $this->txref = uniqid($this->transactionPrefix);
        $this->logger->notice('Generated Reference Number....' . $this->txref);
        return $this;
    }

    /**
     * Generates a checksum value for the information to be sent to the payment gateway
     * */
    public function createCheckSum(): void
    {
        $this->logger->notice('Generating Checksum....');
        $options = [
            'public_key' => self::$config->getPublicKey(),
            'amount' => $this->amount,
            'tx_ref' => $this->txref,
            'currency' => $this->currency,
            'payment_options' => 'card,mobilemoney,ussd',
            'customer' => [
                'email' => $this->customerEmail,
                'phone_number' => $this->customerPhone,
                'name' => $this->customerFirstname . ' ' . $this->customerLastname,
            ],
            'redirect_url' => $this->redirectUrl,
            'customizations' => [
                'description' => $this->customDescription,
                'logo' => $this->customLogo,
                'title' => $this->customTitle,
            ],
        ];

        ksort($options);

        // $this->transactionData = $options;

        // $hashedPayload = '';

        // foreach($options as $key => $value){
        //     $hashedPayload .= $value;
        // }

        // echo $hashedPayload;
        // $completeHash = $hashedPayload.$this->secretKey;
        // $hash = hash('sha256', $completeHash);

        // $this->integrityHash = $hash;
        // return $this;
    }
}
