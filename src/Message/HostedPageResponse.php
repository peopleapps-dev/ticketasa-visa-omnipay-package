<?php

namespace Omnipay\Ticketasavisa\Message;

use Omnipay\Ticketasavisa\Constants;

class HostedPageResponse extends AbstractResponse
{

    public function isSuccessful()
    {
        if (!empty($this->getEncript())) {
            return true;
        }

        return false;
    }

    public function isRedirect()
    {
        return $this->isSuccessful();
    }

    public function getRedirectUrl()
    {
        return $this->getHostedPageURL();
    }

    public function isPending()
    {
        return true;
    }

    public function getTransactionReference()
    {
        return $this->request->getTransactionId();
    }

    public function getHostedPageURL()
    {
        if ($this->isSuccessful()) {

            return ($this->request->getTestMode() ? Constants::PLATFORM_TA_UAT : Constants::PLATFORM_TA_PROD)
                . '/' . ($this->request->getDiscount() ? "discount" : "normal") . "?data=" . $this->getEncript();
        }

        return null;
    }

    public function redirectToHostedPage()
    {
        header("Location: " . $this->getHostedPageURL());
        exit;
    }
}
