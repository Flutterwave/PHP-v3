<?php


$html = <<<HTML
        <h3> $instruction </h3>
        <div class="bank-info">
            <span>REFERENCE:    $transfer_reference;</span>
            <span>ACCOUNT:      $transfer_account;</span>
            <span>BANK:         $transfer_bank;</span>
            <span>AMOUNT:       $transfer_amount;</span>
            <span>EXPIRES:      $account_expiration;</span>
        </div>
        <button class="confirm-bank-transfer">Confirm Bank transfer Payment</button>
        <script>
            var reference = "$transfer_reference";
        </script>
HTML;

return $html;