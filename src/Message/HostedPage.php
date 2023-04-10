<?php

namespace Omnipay\Ticketasavisa\Message;

use Omnipay\Ticketasavisa\Constants;
use Omnipay\Ticketasavisa\Exception\InvalidResponseData;

class HostedPage extends AbstractRequest
{

    const PARAM_SOURCE_HOLDER_NAME = "CardHolderName";
    const PARAM_TOTAL_AMOUNT       = 'TotalAmount';
    protected $TransactionDetails = [];
    const PARAM_TRANSACTION_IDENTIFIER = 'TransactionIdentifier';
    const PARAM_ORDER_IDENTIFIER       = 'OrderIdentifier';
    const PARAM_FIRST_NAME             = 'FirstName';
    const PARAM_LAST_NAME              = 'LastName';
    const PARAM_NOTIFY_URL             = 'NotifyResponseURL';
    const PARAM_RETURN_URL             = 'ReturnURL';

    public function getData()
    {

        $this->setTransactionDetails();
        $this->setCardDetails();
        $this->setCredentials();
        $this->setUrls();
        $this->setTransaction();

        return $this->data;
    }

    protected function setTransactionDetails()
    {

        // $this->TransactionDetails[self::PARAM_ORDER_IDENTIFIER] = $this->getOrderIdentifier();
        $this->TransactionDetails[self::PARAM_TOTAL_AMOUNT] = $this->getAmount();
        $this->validateTransactionDetails();
    }

    protected function validateTransactionDetails()
    {
        if (!empty($this->getTransactionId())) {
            if (!empty($this->getNotifyUrl())) {
                if (!empty($this->getReturnUrl())) {
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
                    throw new InvalidResponseData("Return Url is not valid");
                }
            } else {
                throw new InvalidResponseData("Notify Url is not valid");
            }
        } else {
            throw new InvalidResponseData("Transaction Identifier is not valid");
        }
    }

    protected function setCardDetails()
    {
        $CardDetails = [];
        $CreditCard = $this->getCard();

        if (isset($CreditCard)) {
            $this->data[self::PARAM_FIRST_NAME] = $CreditCard->getFirstName();
            $this->data[self::PARAM_LAST_NAME] = $CreditCard->getLastName();
        } else {
            $this->data[self::PARAM_FIRST_NAME] = "";
            $this->data[self::PARAM_LAST_NAME] = "";
        }
    }

    protected function setCredentials()
    {
        $this->data[Constants::CONFIG_MERCHANT_ID] = $this->getMerchantId();
        $this->data[Constants::CONFIG_PUBLIC_KEY] = $this->getPublicKey();
        $this->data[Constants::CONFIG_PRIVATE_KEY] = $this->getPrivateKey();
    }

    protected function setUrls()
    {
        $this->data[self::PARAM_NOTIFY_URL] = $this->getNotifyUrl();
        $this->data[self::PARAM_RETURN_URL] = $this->getReturnUrl();
    }

    protected function setTransaction()
    {
        $this->data[self::PARAM_TRANSACTION_IDENTIFIER] = $this->getTransactionId();
        $this->data[self::PARAM_ORDER_IDENTIFIER] = !empty($this->getTransactionId()) ? Constants::PREFIX_ORDER . $this->getTransactionId() : null;
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
