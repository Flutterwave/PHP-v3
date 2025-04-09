<?php 

return json_decode('
    {
  "status": "success",
  "message": "Charge initiated",
  "data": {
    "id": 4197118,
    "tx_ref": "12345test_05",
    "flw_ref": "ZZYO0021678723801871881",
    "device_fingerprint": "N/A",
    "amount": 200,
    "charged_amount": 200,
    "app_fee": 2.8,
    "merchant_fee": 0,
    "processor_response": "pending",
    "auth_model": "ENAIRA",
    "currency": "NGN",
    "ip": "54.75.161.64",
    "narration": "Flutterwave Developers",
    "status": "pending",
    "payment_type": "enaira",
    "fraud_status": "ok",
    "charge_type": "normal",
    "created_at": "2023-03-13T16:10:00.000Z",
    "account_id": 20937,
    "customer": {
      "id": 1953337,
      "phone_number": "08092269174",
      "name": "Wisdom Joshua",
      "email": "wsdmjsh@gmail.com",
      "created_at": "2023-01-18T13:22:14.000Z"
    },
    "meta": {
      "authorization": {
        "mode": "redirect",
        "redirect": "https://camltest.azurewebsites.net/enairapay/?invoiceId=01GVDVRTG80MVSRJJQQYRFTZK3&amount=200&token=438890"
      }
    }
  }
}
');