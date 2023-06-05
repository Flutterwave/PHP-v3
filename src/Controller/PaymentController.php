<?php

declare(strict_types=1);

namespace Flutterwave\Controller;

use Flutterwave\EventHandlers\ModalEventHandler;
use Flutterwave\EventHandlers\EventHandlerInterface;
use Flutterwave\Flutterwave;
use Flutterwave\Entities\Payload;
use Flutterwave\Library\Modal;
use Flutterwave\Service\Transactions;

final class PaymentController
{
    private string $requestMethod;

    private EventHandlerInterface $handler;
    private Flutterwave $client;
    private string $modalType;

    protected array $routes = [
        'process' => 'POST',
        'callback' => 'GET'
    ];

    public function __construct(
        Flutterwave $client,
        EventHandlerInterface $handler,
        string $modalType
    ) {
        Flutterwave::bootstrap();
        $this->requestMethod =  $this->getRequestMethod();
        $this->handler = $handler;
        $this->client = $client;
        $this->modalType = $modalType;
    }

    private function getRequestMethod(): string
    {
        return ($_SERVER["REQUEST_METHOD"] === "POST") ? 'POST' : 'GET';
    }

    public function __call(string $name, array $args)
    {
        if ($this->routes[$name] !== $this->$requestMethod) {
            // Todo: 404();
            echo "Unauthorized page!";
        }
        call_user_method_array($name, $this, $args);
    }

    private function handleSessionData( array $request )
    {
        $_SESSION['success_url'] = $request['success_url'];
        $_SESSION['failure_url'] = $request['failure_url'];
        $_SESSION['currency'] = $request['currency'];
        $_SESSION['amount'] = $request['amount'];
    }

    public function process(array $request)
    {
        $this->handleSessionData($request);
        
        try {
            $_SESSION['p'] = $this->client;

            if('inline' === $this->modalType ) {
                echo $this->client
                    ->eventHandler($this->handler)
                    ->render(Modal::POPUP)->with($request)->getHtml();
            } else {
                $paymentLink = $this->client
                    ->eventHandler($this->handler)
                    ->render(Modal::STANDARD)->with($request)->getUrl();
                header('Location: ' . $paymentLink);
            }
            
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function callback(array $request)
    {
        $tx_ref = $request['tx_ref'];
        $status = $request['status'];

        if (empty($tx_ref)) {
            session_destroy();
        }

        if (!isset($_SESSION['p'])) {
            echo "session expired!. please refresh you browser.";
            exit();
        }

        $payment = $_SESSION['p'];

        // $payment::setUp([
        //     'secret_key' => 'FLWSECK_TEST-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX-X',
        //     'public_key' => 'FLWPUBK_TEST-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX-X',
        //     'encryption_key' => 'FLWSECK_XXXXXXXXXXXXXXXX',
        //     'environment' => 'staging'
        // ]);

        $payment::bootstrap();

        if ('cancelled' === $status) {
            $payment
                ->eventHandler($this->handler)
                ->paymentCanceled($tx_ref);
        }

        if ('successful' === $status && isset($request['transaction_id'])) {
            $tx_id = $request['transaction_id'];

            if (empty($tx_id) && !empty($tx_ref)) {
                // get tx_id with the transaction service.
                $response = (new Transactions())->verifyWithTxref($tx_ref);

                if ('success' === $response->status) {
                    $tx_id = $response->data->id;
                }
            }

            $payment->logger->notice('Payment completed. Now requerying payment.');
            $payment
                ->eventHandler($this->handler)
                ->requeryTransaction($tx_id);
        }
    }
}
