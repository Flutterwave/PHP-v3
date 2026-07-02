<?php

namespace Unit\Checkout;

use PHPUnit\Framework\TestCase;
use Flutterwave\Flutterwave;
use Flutterwave\EventHandlers\ModalEventHandler;

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
            ->eventHandler(new ModalEventHandler())
            ->setAmount('1000')
            ->setCurrency(\Flutterwave\Util\Currency::NGN)
            ->setCountry('NG')
            ->setEmail('test@example.com')
            ->setFirstname('John')
            ->setLastname('Doe')
            ->setPhoneNumber('+2349067985861')
            ->setRedirectUrl('https://example.com/callback')
            ->setTitle('Test Payment')
            ->setDescription('Testing initialize XSS fix')
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

        $this->assertStringNotContainsString('<script>alert(1)</script>', $output);
        $this->assertStringContainsString('\u003C', $output);
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

        $this->assertStringContainsString('card,banktransfer', $output);
    }

    public function testInitializeIsDeprecated(): void
    {
        $instance = $this->buildInstance();

        $deprecationTriggered = false;
        set_error_handler(function (int $errno, string $errstr) use (&$deprecationTriggered) {
            if ($errno === E_USER_DEPRECATED && str_contains($errstr, 'initialize() is deprecated')) {
                $deprecationTriggered = true;
            }
            return true;
        });

        ob_start();
        $instance->initialize();
        ob_get_clean();

        restore_error_handler();

        $this->assertTrue($deprecationTriggered, 'Expected a deprecation notice for initialize()');
    }
}