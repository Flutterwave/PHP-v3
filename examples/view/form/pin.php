<?php

$html = <<<HTML
    <div class="pin-form">
        <h3> $instruction</h3>
            <form method="POST">
                <input type="hidden" name="mode" value="pin">
                <input type="numbers" name="pin" value="1234" readonly>
                <input type="submit" value="Proceed" >
            </form>
    </div>
HTML;

echo $html;

//echo "<pre>";
//print_r($_SERVER);