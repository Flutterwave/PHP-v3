<?php

namespace Unit\Checkout;

use PHPUnit\Framework\TestCase;
use Flutterwave\Flutterwave;
use Flutterwave\Test\Resources\Setup\Config;

class InitializeTest extends TestCase
{
    protected function setUp(): void
    {
        Flutterwave::bootstrap();
    }

    private function buildInstance(): Flutterwave
    {
        $instance = new Flutterwave();
        $instance
            ->setAmount('1000')
            ->setCurrency('NGN')
            ->setCountry('NG')
            ->setEmail('test@example.com')
            ->setFirstname('John')
            ->setLastname('Doe')
            ->setPhoneNumber('+2349012345678')
            ->setRedirectUrl('https://mysite.com/callback')
            ->setTitle('Test Payment')
            ->setDescription('Test Description')
            ->setLogo('https://mysite.com/logo.png')
            ->setPaymentOptions('card,banktransfer');

        return $instance;
    }

    public function testInitializeEscapesScriptTagInTitle(): void
    {
        $instance = $this->buildInstance();
        $instance->setTitle('</script><script>alert(1)</script>');

        ob_start();
        $instance->initialize();
        $output = ob_get_clean();

        $this->assertStringNotContainsString('</script><script>', $output);
        $this->assertStringContainsString('\u003C', $output); // JSON_HEX_TAG encoding of 
    }

    public function testInitializeEscapesQuotesInCustomerName(): void
    {
        $instance = $this->buildInstance();
        $instance->setFirstname("O'Brien");
        $instance->setLastname('"Hacker"');

        ob_start();
        $instance->initialize();
        $output = ob_get_clean();

        $this->assertStringNotContainsString('"Hacker"', $output);
        $this->assertStringNotContainsString("'Brien", $output);
    }

    public function testInitializeEscapesRedirectUrl(): void
    {
        $instance = $this->buildInstance();
        $instance->setRedirectUrl('https://evil.com","public_key":"leaked');

        ob_start();
        $instance->initialize();
        $output = ob_get_clean();

        $this->assertStringNotContainsString('"public_key":"leaked', $output);
    }

    public function testInitializeCastsAmountToFloat(): void
    {
        $instance = $this->buildInstance();
        $instance->setAmount('100');

        ob_start();
        $instance->initialize();
        $output = ob_get_clean();

        $this->assertStringContainsString('"amount":100.0', $output);
    }

    public function testInitializeUsesSetPaymentOptions(): void
    {
        $instance = $this->buildInstance();
        $instance->setPaymentOptions('card,banktransfer');

        ob_start();
        $instance->initialize();
        $output = ob_get_clean();

        $this->assertStringContainsString('"payment_options":"card,banktransfer"', $output);
    }
}