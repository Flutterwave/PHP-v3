<?php

declare(strict_types=1);

namespace Flutterwave\EventHandlers;

use Flutterwave\Service\Transactions;

class ModalEventHandler implements EventHandlerInterface
{
    private ?string $success_url = null;
    private ?string $failure_url = null;

    /**
     * This is called when the Rave class is initialized
     * */
    public function onInit($initializationData): void
    {
        echo "This Event Handler is an Implementation of " . __NAMESPACE__ . "\EventHandlerInterface";
        // Save the transaction to your DB.
    }

    public function getSuccessUrl(): ?string
    {
        return $this->success_url;
    }

    public function setSuccessUrl(?string $success_url): void
    {
        $this->success_url = $success_url;
    }

    public function getFailureUrl(): ?string
    {
        return $this->failure_url;
    }

    public function setFailureUrl(?string $failure_url): void
    {
        $this->failure_url = $failure_url;
    }

    /**
     * This is called only when a transaction is successful
     * */
    public function onSuccessful($transactionData): void
    {
        // Get the transaction from your DB using the transaction reference (txref)
        // Check if you have previously given value for the transaction. If you have, redirect to your successpage else, continue
        // Comfirm that the transaction is successful
        // Confirm that the chargecode is 00 or 0
        // Confirm that the currency on your db transaction is equal to the returned currency
        // Confirm that the db transaction amount is equal to the returned amount
        // Update the db transaction record (includeing parameters that didn't exist before the transaction is completed. for audit purpose)
        // Give value for the transaction.
        // Update the transaction to note that you have given value for the transaction.
        // You can also redirect to your success page from here.
        if ($transactionData->status === 'successful') {
            $currency = $_SESSION['currency'] ?? ($transactionData->currency ?? null);
            $amount   = $_SESSION['amount'] ?? ($transactionData->amount ?? null);

            if ($currency !== null && $amount !== null) {
                if ($transactionData->currency === $currency && floatval($transactionData->amount)  === floatval($amount)) {
                    if (!empty($_SESSION['success_url'])) {
                        header('Location: ' . $_SESSION['success_url']);
                    }
                    session_destroy();
                }

                if ($transactionData->currency === $currency && floatval($transactionData->amount) < floatval($amount)) {
                    // TODO: replace this a custom action.
                    echo "This Event Handler is an Implementation of " . __NAMESPACE__ . "\EventHandlerInterface </br>";
                    echo "Partial Payment Made ! replace this with your own action! ";
                    session_destroy();
                }

                if ($transactionData->currency !== $currency && floatval($transactionData->amount) === floatval($amount)) {
                    // TODO: replace this a custom action.
                    echo "This Event Handler is an Implementation of " . __NAMESPACE__ . "\EventHandlerInterface </br>";
                    echo "Currency mismatch. please look into it ! replace this with your own action ";
                    session_destroy();
                }
            } else {
                // Fallback when session metadata is unavailable.
                echo "This Event Handler is an Implementation of " . __NAMESPACE__ . "\EventHandlerInterface </br>";
                echo "Transaction successful.";
            }
        } else {
            $this->onFailure($transactionData);
        }
    }

    /**
     * This is called only when a transaction failed
     * */
    public function onFailure($transactionData): void
    {
        // Get the transaction from your DB using the transaction reference (txref)
        // Update the db transaction record (includeing parameters that didn't exist before the transaction is completed. for audit purpose)
        // You can also redirect to your failure page from here.
        // TODO: replace this a custom action.
        if (!empty($_SESSION['failure_url'])) {
            header('Location: ' . $_SESSION['failure_url']);
        }
        session_destroy();
    }

    /**
     * This is called when a transaction is requeryed from the payment gateway
     * */
    public function onRequery($transactionReference): void
    {
        // do not include any business logic here, this function is likely to be depricated.
    }

    /**
     * This is called a transaction requery returns with an error
     * */
    public function onRequeryError($requeryResponse): void
    {
        echo "Flutterwave: error querying the transaction.";
        // trigger webhook notification from Flutterwave.
        $service = new Transactions();
        $transactionId = is_object($requeryResponse) && isset($requeryResponse->id)
            ? (string) $requeryResponse->id
            : (string) $requeryResponse;

        $service->resendFailedHooks($transactionId);
        header('Location: ' . $_SERVER['HTTP_ORIGIN']);
    }

    /**
     * This is called when a transaction is canceled by the user
     * */
    public function onCancel($transactionReference): void
    {
        // TODO: replace this a custom action.
        echo "This Event Handler is an Implementation of " . __NAMESPACE__ . "\EventHandlerInterface </br>";
        echo "Payment was cancelled ! replace this with your own action.";
        session_destroy();
    }

    /**
     * This is called when a transaction doesn't return with a success or a failure response. This can be a timedout transaction on the Rave server or an abandoned transaction by the customer.
     * */
    public function onTimeout($transactionReference, $data): void
    {
        // trigger webhook notification from Flutterwave.
        $service = new Transactions();
        $service->resendFailedHooks((string) ($data->id ?? ''));
        header('Location: ' . $_SERVER['HOST']);
    }
}
