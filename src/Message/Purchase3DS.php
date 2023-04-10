<?php

namespace Omnipay\Ticketasavisa\Message;

class Purchase3DS extends HostedPage
{

    const PARAM_NOTIFY_URL     = "notifyResponseURL";
    const PARAM_APPLY_DISCOUNT = "applyDiscount";

    public function getData()
    {
        parent::getData();
        $this->applyNotifyResponseURL();
        $this->applyDiscount();

        return $this->data;
    }

    protected function applyNotifyResponseURL()
    {
        $this->data[ucfirst(self::PARAM_NOTIFY_URL)] = $this->getNotifyUrl();
    }

    protected function applyDiscount()
    {

        $this->data[self::PARAM_APPLY_DISCOUNT] = $this->getDiscount();
    }
}
