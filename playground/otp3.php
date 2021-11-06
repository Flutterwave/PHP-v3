
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>Document</title>
</head>
<?php
        switch (isset($_POST['otp'])) {
            case 'true':
                $otp = $_POST['otp'];
                $flwref = $_GET['ref'];
                require("../library/Preauth.php");
                $filePath = getcwd().DIRECTORY_SEPARATOR."payment.txt";
                if (file_exists($filePath)){
                    $paymento = file_get_contents($filePath);
                    $payment = unserialize($paymento);           
                    if (!empty($payment)){
                        $validate = $payment->validateTransaction($otp, $flwref);

                            echo '<body>';
                            echo '<div class="alert alert-primary role="alert">
                            <h1>Validate Result: </h1>
                            <p><b> '.print_r($validate, true).'</b></p>
                            </div>';
                            $id = $_GET['id'];
                        $verify = $payment->verifyTransaction($id);
                        echo '<div class="alert alert-primary role="alert">
                        <h1>Verified Result: </h1>
                        <p><b> '.print_r($verify, true).'</b></p>
                        </div>';

                            echo '<script>
                            var submitBtn = $("#btn-1");
                        
                            submitBtn.click(function(){
                                $(".first").hide();
                            });
                        
                        </script>';
                            echo '</body>';
                    }

                
                }

                break;
            
            default:
                # code...
                break;
        }
        
?>
<body class="first">
    <div class="container-lg">
        <form action="" method="post">
        <input type="password" name="otp">
        <input id="btn-1" class="btn btn-warning" type="submit" value="Validate OTP">
        </form>
    </div>
    


    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<script>
    var submitBtn = $('#btn-1');

    submitBtn.click(function(){
        $('.first').hide();
    });

</script>
</body>
</html>