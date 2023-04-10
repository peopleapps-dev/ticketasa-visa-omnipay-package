# Omnipay - TicketAsa Visa 1.0.0

**TicketAsaGT Commerce gateway for the Omnipay PHP payment processing library**

![Packagist License](https://img.shields.io/packagist/l/cloudcogsio/omnipay-firstatlanticcommerce-gateway) ![Packagist Version](https://img.shields.io/packagist/v/cloudcogsio/omnipay-firstatlanticcommerce-gateway) ![Packagist PHP Version Support (specify version)](https://img.shields.io/packagist/php-v/cloudcogsio/omnipay-firstatlanticcommerce-gateway/dev-master) ![GitHub issues](https://img.shields.io/github/issues/cloudcogsio/omnipay-firstatlanticcommerce-gateway) ![GitHub last commit](https://img.shields.io/github/last-commit/cloudcogsio/omnipay-firstatlanticcommerce-gateway)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment processing library for
PHP 5.3+. This package implements TicketAsaGT 2.4 support for Omnipay.

## Installation

Via Composer

``` bash
$ composer require danbart/omnipay-ticketasavisa
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

```php
{
  "code": "200",
  "message": "OK",
  "data": {
    "id": "6696797642706350904957",
    "rootId": "6696797642706350904957",
    "reconciliationId": "6696797642706350904957",
    "submitTimeUTC": "2022-11-28T23:56:04Z",
    "merchantId": "ticketasa",
    "applicationInformation": {
      "reasonCode": 100,
      "applications": [
        {
          "name": "ics_auth",
          "reasonCode": "100",
          "rCode": "1",
          "rFlag": "SOK",
          "reconciliationId": "6696797642706350904957",
          "rMessage": "Request was processed successfully.",
          "returnCode": 1010000
        }
      ]
    },
    "clientReferenceInformation": {
      "code": "f780fd1b-30c6-47f6-aab0-a796161c7bde",
      "applicationName": "REST API",
      "applicationVersion": "1.0"
    },
    "orderInformation": {
      "amountDetails": {
        "totalAmount": "1",
        "currency": "GTQ",
        "taxAmount": "0",
        "authorizedAmount": "1"
      },
      "lineItems": [
        {
          "productCode": "default",
          "taxAmount": 0,
          "quantity": 1,
          "unitPrice": 1
        }
      ]
    },
    "paymentInformation": {
      "paymentType": {
        "name": "vguatemala",
        "type": "credit card",
        "method": "VI"
      },
      "card": {
        "suffix": "3495",
        "prefix": "491681",
        "expirationMonth": "02",
        "expirationYear": "2026",
        "type": "001"
      },
    },
    "processingInformation": {
      "paymentSolution": "Visa",
      "commerceIndicator": "7",
      "commerceIndicatorLabel": "internet",
      },
      "fundingOptions": {
        "firstRecurringPayment": false
      },
      "ecommerceIndicator": "7"
    },
    "riskInformation": {
      "profile": {
        "name": "Perfil de Pruebas CVV2",
        "decision": "ACCEPT"
      },
      "score": {
        "factorCodes": [
          "C",
          "H",
          "P",
          "F"
        ],
        "result": 48
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

```php
{
  "code": "200",
  "message": "OK",
  "data": {
    "clientReferenceInformation": {
      "code": "f780fd1b-30c6-47f6-aab0-a796161c7bde"
    },
    "id": "6697696378546596504952",
    "orderInformation": {
      "amountDetails": {
        "currency": "GTQ"
      }
    },
    "reconciliationId": "6694214339196447904951",
    "refundAmountDetails": {
      "currency": "GTQ",
      "refundAmount": "1.00"
    },
    "status": "PENDING",
    "submitTimeUtc": "2022-11-30T00:53:58Z"
  }
}
```

## Support

If you are having general issues with Omnipay, we suggest posting on [Stack Overflow](http://stackoverflow.com/). Be
sure to add the [omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you believe you have found a bug, please report it using
the [GitHub issue tracker](https://github.com/danbart/ticketasavisa.git/issues), or better yet, fork the library
and submit a pull request.