<?php

namespace Omnipay\Ticketasavisa\Message;

use Omnipay\Ticketasavisa\Constants;
use Omnipay\Ticketasavisa\Exception\InvalidResponseData;

class TransactionStatus extends AbstractRequest
{

    const PARAM_IDENTIFIER = 'TransactionIdentifier';
    protected $TransactionDetails = [];

    public function getData()
    {
        $this->TransactionDetails[self::PARAM_IDENTIFIER] = $this->getTransactionId();

        $this->validateTransactionDetails();
        $this->setCredentials();

        return $this->data;
    }

    protected function validateTransactionDetails()
    {
        if (!empty($this->getTransactionId())) {
            if (!empty($this->getMerchantId()) && !empty($this->getPublicKey()) && !empty($this->getPrivateKey())) {

                $this->data = $this->TransactionDetails;
            } else {
                throw new InvalidResponseData("Merchant Credentials are invalid");
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
}
