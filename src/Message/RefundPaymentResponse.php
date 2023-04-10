<?php

namespace Omnipay\Ticketasavisa\Message;

class RefundPaymentResponse extends AbstractResponse
{

    public function isSuccessful()
    {
        if (!empty($this->getData()["code"] == "400") && !empty($this->getData()["code"] == "404")) {
            return true;
        }

        return false;
    }

    public function getTotalAmount()
    {
        return $this->getData()["data"]["refundAmountDetails"]["refundAmount"];
    }

    public function getIdentifier()
    {
        return $this->getData()["data"]["id"];
    }

    public function getOrderIdentifier()
    {
        return $this->getData()["data"]["clientReferenceInformation"]["code"];
    }

    public function getReconciliationIdentifier()
    {
        return $this->getData()["data"]["reconciliationId"];
    }

    public function getStatus()
    {
        return $this->getData()["data"]["status"];
    }

    public function getResponseMessage()
    {
        return $this->getData()["message"];
    }
}
