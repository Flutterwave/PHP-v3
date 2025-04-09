<?php 

return json_decode('
    {
        "status": "success",
        "message": "Charge initiated",
        "data": {
            "id": 2615403,
            "tx_ref": "MC-TEST-1234568_success_mock",
            "flw_ref": "RQFA6549001367743",
            "device_fingerprint": "gdgdhdh738bhshsjs",
            "amount": 10,
            "charged_amount": 10,
            "app_fee": 0.38,
            "merchant_fee": 0,
            "processor_response": "Payment token retrieval has been initiated",
            "auth_model": "GOOGLEPAY_NOAUTH",
            "currency": "USD",
            "ip": "54.75.56.55",
            "narration": "Test Google Pay charge",
            "status": "pending",
            "auth_url": "https://rave-api-v2.herokuapp.com/flwv3-pug/getpaid/api/short-url/XPtNw-WkQ",
            "payment_type": "googlepay",
            "fraud_status": "ok",
            "charge_type": "normal",
            "created_at": "2022-05-11T20:36:15.000Z",
            "account_id": 20937,
            "customer": {
            "id": 955307,
            "phone_number": null,
            "name": "Example User",
            "email": "user@example.com",
            "created_at": "2022-05-11T20:36:14.000Z"
            },
            "meta": {
            "authorization": {
                "mode": "redirect",
                "redirect": "https://rave-api-v2.herokuapp.com/flwv3-pug/getpaid/api/short-url/XPtNw-WkQ"
            }
            }
        }
        }
');