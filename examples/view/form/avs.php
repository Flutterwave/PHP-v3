<?php

$html = <<<HTML
    <div class="pin-form">
        <h3> $instruction</h3>
            <form method="POST">
                <input type="hidden" name="mode" value="avs">
                <input type="text" name="city" value="San Francisco" readonly>
                <input type="text" name="address" value="69 Fremont Street" readonly>
                <input type="text" name="state" value="CA" readonly>
                <input type="text" name="country" value="US" readonly>
                <input type="zipcode" name="zipcode" value="94105" readonly>
                <input type="submit" value="Proceed" >
            </form>
    </div>
HTML;

echo $html;