# Omnipay - TicketAsa Visa 1.0.0

**TicketAsaGT Commerce gateway for the Omnipay PHP payment processing library**

![Packagist License](https://img.shields.io/packagist/l/cloudcogsio/omnipay-firstatlanticcommerce-gateway) ![Packagist Version](https://img.shields.io/packagist/v/cloudcogsio/omnipay-firstatlanticcommerce-gateway) ![Packagist PHP Version Support (specify version)](https://img.shields.io/packagist/php-v/cloudcogsio/omnipay-firstatlanticcommerce-gateway/dev-master) ![GitHub issues](https://img.shields.io/github/issues/cloudcogsio/omnipay-firstatlanticcommerce-gateway) ![GitHub last commit](https://img.shields.io/github/last-commit/cloudcogsio/omnipay-firstatlanticcommerce-gateway)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment processing library for
PHP 5.3+. This package implements TicketAsaGT 2.4 support for Omnipay.

## Installation

Via Composer

``` bash
$ composer require peopleapps-dev/ticketasa-visa-omnipay-package
```

## Gateway Operation Defaults

This gateway driver operates in 3DS mode by default and requires a notify URL to be provided via the '**setNotifyURL**'
method.

## Usage

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay) repository.

### 3DS Transactions (Direct Integration)

'**NotifyURL**' required. URL must be **https://**

``` php

use Omnipay\Omnipay;
try {
    $gateway = Omnipay::create('Ticketasavisa');
    $gateway
        ->setTestMode(true)  // false to use productions links  , true to use test links 
        ->setMerchantId('xxxxxxxx') 
        ->setPublicKey('xxxxxxxx') 
        ->setPrivateKey('xxxxxxxx')
        // **Required and must be https://
        ->setNotifyUrl('https://localhost/webhook.php')
        // **Required and must be https://    
        ->setReturnUrl('https://localhost/webhook.php')        
        ->setDiscount(false);
        

    $cardData = [
         'firstName' => 'Gabriel', //optional 
         'LastName' => 'Arzu', // optional
    ];

    $transactionData = [
        'card' => $cardData,
        'amount' => '1.00',   // Mandatory
        'TransactionId' => '2100001',  // mandatory, must be unique in each transaction
    ];

    $response = $gateway->purchase($transactionData)->send();

    if($response->isSuccessful())
         $response->getHostedPageURL();  // return the link with encrypted params 

         $response->redirectToHostedPage(); //Redirect automatically to payment form 

} catch (Exception $e){
    $e->getMessage();
}
```

***webhook response***
Response transaction from TicketasaGT.

```php
{
    "clientReferenceInformation": {
        "code": "VlISZ4VI2kKFcz07JELCTw2"
    },
    "id": "6638930703846085404953",
    "orderInformation": {
        "amountDetails": {
            "totalAmount": "1.00",
            "currency": "GTQ"
        }
    },
    "reconciliationId": "6638927696246293304951",
    "status": "PENDING",
    "submitTimeUtc": "2022-09-23T00:31:10Z"
}
```

### Fetch status Transactions (Direct Integration)

'**fetchTransaction**' required. TransactionId

``` php

use Omnipay\Omnipay;
try {
    $gateway = Omnipay::create('Ticketasavisa');
    $gateway
        ->setTestMode(true)  // false to use productions links  , true to use test links 
        ->setMerchantId('xxxxxxxx') 
        ->setPublicKey('xxxxxxxx') 
        ->setPrivateKey('xxxxxxxx');
        

    
    $transactionData = [               
        'TransactionId' => '2100001',  // mandatory, must be unique in each transaction
    ];

    $response = $gateway->fetchTransaction($transactionData)->send();
    
    
    $response->getData();  //return the response object
    $response->isSuccessful() //  if IsoResponseCode is 00 return true 
    $response->getTransactionId() // return transactionId from object response
    $response->getTotalAmount() // return Amount from object response
    $response->getAuthorizationCode() // return authorizationCode from object response
    $response->getLastCaptureDateTime() // return date capture payment from object response
    $response->getTransactionDateTime() // return date transaction payment from object response

} catch (Exception $e){
    $e->getMessage();
}
```

***Visa Cybersource response***
Response fetch transaction from powerTranz.

```json
{
   "success":true,
   "message":"OK",
   "statusCode":200,
   "data":{
      "id":"6814001314506170404951",
      "rootId":"6814001314506170404951",
      "reconciliationId":"6814001314506170404951",
      "merchantId":"visanetgt_ticketasa",
      "submitTimeUTC":"2023-04-13T15:35:31Z",
      "applicationInformation":{
         "status":"PENDING",
         "reasonCode":"100",
         "applications":[
            {
               "name":"ics_auth",
               "reasonCode":"100",
               "rCode":"1",
               "rFlag":"SOK",
               "reconciliationId":"6814001314506170404951",
               "rMessage":"Request was processed successfully.",
               "returnCode":1010000
            }
         ]
      },
      "orderInformation":{
         "amountDetails":{
            "totalAmount":"1",
            "currency":"GTQ",
            "taxAmount":"0",
            "authorizedAmount":"1"
         }
      },
      "paymentInformation":{
         "paymentType":{
            "name":"vdcguatemala",
            "type":"credit card",
            "method":"VI"
         },
         "card":{
            "suffix":"1005",
            "prefix":"445653",
            "expirationMonth":"12",
            "expirationYear":"2031",
            "type":"001"
         }
      },
      "processingInformation":{
         "paymentSolution":"Visa",
         "commerceIndicator":"5",
         "commerceIndicatorLabel":"vbv"
      },
      "processorInformation":{
         "processor":{
            "name":"vdcguatemala"
         },
         "networkTransactionId":"016153570198200",
         "retrievalReferenceNumber":"310315180334",
         "approvalCode":"831000",
         "responseCode":"00"
      },
      "riskInformation":{
         "profile":{
            "name":"Perfil de Pruebas CVV2",
            "decision":"ACCEPT"
         },
         "score":{
            "factorCodes":[
               "V",
               "H",
               "F"
            ],
            "result":91
         }
      }
   }
}
```

### Refund Payment (Direct Integration)

'**fetchTransaction**' required. TransactionId, OrderIdentifier and amount

``` php

use Omnipay\Omnipay;
try {
    $gateway = Omnipay::create('Ticketasavisa');
    $gateway
        ->setTestMode(true)  // false to use productions links  , true to use test links 
        ->setMerchantId('xxxxxxxx') 
        ->setPublicKey('xxxxxxxx') 
        ->setPrivateKey('xxxxxxxx');
    
    $transactionData = [      
         'amount' => '1.00',   // Mandatory         
         'TransactionId' => '6694214339196447904951',  // mandatory, must be unique in each transaction
        'OrderIdentifier' => '2100001',  // mandatory, must be unique in each transaction
    ];

    $response = $gateway->refund($transactionData)->send();
    
    $response->getData();  //return the response object
    $response->isSuccessful(); //  if Approved response
    $response->getIdentifier(); // return transactionId from object response
    $response->getTotalAmount(); // return Amount from object response
    $response->getOrderIdentifier(); // return transactionId from object response
    $response->getReconciliationIdentifier(); // return transactionId from object response
    $response->getStatus(); // return the iso Code
    $response->getResponseMessage(); // return  the error general message

} catch (Exception $e){
    $e->getMessage();
}
```

***Visa Cybersource response***
Response refund Transaction from powerTranz.

```json
{
   "success":true,
   "message":"OK",
   "statusCode":201,
   "data":{
      "id":"6814069291496607104953",
      "submitTimeUtc":"2023-04-13T17:28:49Z",
      "status":"PENDING",
      "reconciliationId":"6814004474776245104951",
      "clientReferenceInformation":{
         "code":"68ef8f5d-f3d9-4ed2-98a6-a12f57dfa27f"
      },
      "refundAmountDetails":{
         "refundAmount":"1.00",
         "currency":"GTQ"
      }
   }
}
```

## Support

If you are having general issues with Omnipay, we suggest posting on [Stack Overflow](http://stackoverflow.com/). Be
sure to add the [omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you believe you have found a bug, please report it using
the [GitHub issue tracker](https://github.com/danbart/ticketasavisa.git/issues), or better yet, fork the library
and submit a pull request.