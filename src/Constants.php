<?php

namespace Omnipay\Ticketasavisa;

class Constants
{

    const API_STAGING                      = 'https://apitest.cybersource.com/';
    const API_PRODUCTION                   = 'https://api.cybersource.com/';
    const SPI_STAGING                      = '';
    const SPI_PRODUCTION                   = '';
    const DRIVER_NAME                      = "TicketAsaVisa - Payment Gateway";
    const PLATFORM_TA_UAT                  = 'https://ticketasa-visa.mypeopleapps.com';
    const PLATFORM_TA_PROD                 = 'https://visa.ticketasa.gt';
    const CONFIG_MERCHANT_ID               = 'MerchantId';
    const CONFIG_PUBLIC_KEY                = 'PublicKey';
    const CONFIG_PRIVATE_KEY               = 'PrivateKey';
    const CONFIG_KEY_NOTIFY_URL            = 'notifyURL';
    const CONFIG_KEY_RETURN_URL            = 'returnURL';
    const GATEWAY_ORDER_IDENTIFIER_PREFIX  = 'orderNumberPrefix';
    const GATEWAY_ORDER_IDENTIFIER_AUTOGEN = 'orderNumberAutoGen';
    const GATEWAY_ORDER_IDENTIFIER         = 'orderIdentifier';
    const CONFIG_APPLY_DISCOUNT            = "Discount";
    const CONFIG_TRANSACTION_IDENTIFIER    = "setTransactionId";
    const CONFIG_PASSWORD                  = "PasswordEncrypt";
    const PASSWORD_SUFFLED                 = "peopleapps2021";
    const PREFIX_ORDER                     = "ASA-";
    const PARAM_HTTP_METHOD                = 'HttpMethod';
    //  const CONFIG_KEY_TRANSID = 'PWTId';
}
