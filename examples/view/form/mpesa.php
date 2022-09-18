<?php


$html = <<<HTML
        <h3> $instruction </h3>
        <div class="bank-info">
            <span>TransactionId:    $transactionId;</span>
            <span>STATUS:  pending</span>
        </div>
        <button class="confirm-mpesa-payment">Confirm Mpesa Payment</button>
        <script>
            var reference = "$transactionId";
        </script>
HTML;

return $html;