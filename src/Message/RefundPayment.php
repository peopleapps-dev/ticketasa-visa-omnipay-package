<?php

namespace Omnipay\Ticketasavisa\Message;

use Omnipay\Ticketasavisa\Constants;
use Omnipay\Ticketasavisa\Exception\InvalidResponseData;

class RefundPayment extends AbstractRequest
{

    const PARAM_IDENTIFIER          = 'TransactionIdentifier';
    const PARAM_TOTAL_AMOUNT        = 'TotalAmount';
    const PARAM_REFUND              = 'Refund';
    const PARAM_CURRENCYCODE        = 'CurrencyCode';
    const PARAM_SOURCE              = 'Source';
    const PARAM_SOURCE_CARD_PRESENT = 'CardPresent';
    const PARAM_SOURCE_CARD_EMV     = 'CardEmvFallback';
    const PARAM_SOURCE_CARD_MAN     = 'ManualEntry';
    const PARAM_SOURCE_CARD_DEB     = 'Debit';
    const PARAM_SOURCE_CARD_CONT    = 'Contactless';
    const PARAM_SOURCE_CARD_CARD    = 'CardPan';
    const PARAM_SOURCE_CARD_MK      = 'MaskedPan';
    const PARAM_TERMINAL            = 'TerminalCode';
    const PARAM_TERMINAL_SERIAL     = 'TerminalSerialNumber';
    const PARAM_EXTERNAL_IDENTIFIER = 'ExternalIdentifier';
    const PARAM_ADDRESS             = 'AddressMatch';
    const PARAM_ORDER_IDENTIFIER    = 'OrderIdentifier';
    protected $TransactionDetails = [];

    public function getData()
    {
        $this->TransactionDetails[self::PARAM_REFUND] = true;
        $this->TransactionDetails[self::PARAM_IDENTIFIER] = $this->getTransactionId(); // str_replace("-", "", $this->guidv4()) ;
        $this->TransactionDetails[self::PARAM_TOTAL_AMOUNT] = $this->getAmount();
        $this->TransactionDetails[self::PARAM_CURRENCYCODE] = "320";
        $this->TransactionDetails[self::PARAM_SOURCE][self::PARAM_SOURCE_CARD_PRESENT] = false;
        $this->TransactionDetails[self::PARAM_SOURCE][self::PARAM_SOURCE_CARD_EMV] = false;
        $this->TransactionDetails[self::PARAM_SOURCE][self::PARAM_SOURCE_CARD_MAN] = false;
        $this->TransactionDetails[self::PARAM_SOURCE][self::PARAM_SOURCE_CARD_DEB] = false;
        $this->TransactionDetails[self::PARAM_SOURCE][self::PARAM_SOURCE_CARD_CONT] = false;
        $this->TransactionDetails[self::PARAM_SOURCE][self::PARAM_SOURCE_CARD_CARD] = "";
        $this->TransactionDetails[self::PARAM_SOURCE][self::PARAM_SOURCE_CARD_MK] = "";
        $this->TransactionDetails[self::PARAM_TERMINAL] = "";
        $this->TransactionDetails[self::PARAM_TERMINAL_SERIAL] = "";
        $this->TransactionDetails[self::PARAM_EXTERNAL_IDENTIFIER] = $this->getTransactionId();
        $this->TransactionDetails[self::PARAM_ADDRESS] = false;
        $this->TransactionDetails[self::PARAM_ORDER_IDENTIFIER] = $this->getOrderIdentifier();

        $this->validateTransactionDetails();
        $this->setCredentials();

        return $this->data;
    }

    protected function validateTransactionDetails()
    {
        if (!empty($this->getTransactionId())) {
            if (!empty($this->getAmount()) && is_numeric($this->getAmount())) {
                if (!empty($this->getMerchantId()) && !empty($this->getPublicKey()) && !empty($this->getPrivateKey())) {

                    $this->data = $this->TransactionDetails;
                } else {
                    throw new InvalidResponseData("Merchant Credentials are invalid");
                }
            } else {
                throw new InvalidResponseData("Total Amount is not valid");
            }
        } else {
            throw new InvalidResponseData("Transaction Identifier is not valid");
        }
    }

    protected function setCredentials()
    {
        $this->data[Constants::CONFIG_MERCHANT_ID] = $this->getMerchantId();
        $this->data[Constants::CONFIG_PUBLIC_KEY] = $this->getPublicKey();
        $this->data[Constants::CONFIG_PRIVATE_KEY] = $this->getPrivateKey();
    }

    protected function guidv4($data = null)
    {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
