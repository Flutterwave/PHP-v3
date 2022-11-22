<?php

$validateEndpoint = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']. '/examples/endpoint/validate.php';

$html = <<<HTML
    <div class="otp-form">
        <h3> $instruction</h3>
            <form action='$validateEndpoint' method="POST">
                <input type="hidden" name="flw_ref" value="$flwTrasanctionReference">
                <input type="numbers" name="otp">
                <input type="submit" value="Validate Payment">
            </form>
    </div>
HTML;

echo $html;