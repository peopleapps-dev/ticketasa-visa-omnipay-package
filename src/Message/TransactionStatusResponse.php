<?php

namespace Omnipay\Ticketasavisa\Message;

class TransactionStatusResponse extends AbstractResponse
{

    public function isSuccessful()
    {
        if (!empty($this->getData()["code"] == "400") && !empty($this->getData()["code"] == "404")) {
            return true;
        }

        return false;
    }

    public function isPaid()
    {
        return $this->isSuccessful();
    }

    public function getTransactionId()
    {
        return $this->getData()["data"]["reconciliationId"];
    }

    public function getTotalAmount()
    {
        return $this->getData()["data"]["orderInformation"]["amountDetails"]["totalAmount"];
    }

    public function getAuthorizationCode()
    {
        return $this->getData()["data"]["applicationInformation"]["reasonCode"];
    }

    public function getTransactionDateTime()
    {
        return $this->getData()["data"]["submitTimeUTC"];
    }

    public function getMessage()
    {
        return $this->getData()["data"]["applicationInformation"]["applications"][0]["rMessage"];
    }
}
