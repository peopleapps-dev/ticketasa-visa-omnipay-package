<?php

namespace Omnipay\Ticketasavisa;

use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Http\ClientInterface;
use Omnipay\Ticketasavisa\Support\ParametersInterface;

class Gateway extends AbstractGateway implements ParametersInterface
{

    public function __construct(ClientInterface $httpClient = null, HttpRequest $httpRequest = null)
    {
        parent::__construct(null, $httpRequest);
    }

    public function getName()
    {
        return Constants::DRIVER_NAME;
    }

    /**
     * @param array $options
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function purchase(array $options = []): \Omnipay\Common\Message\AbstractRequest
    {
        return $this->createRequest("\Omnipay\Ticketasavisa\Message\HostedPage", $options);
    }

    /**
     * @param array $options
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function fetchTransaction(array $options = []): \Omnipay\Common\Message\AbstractRequest
    {
        return $this->createRequest("\Omnipay\Ticketasavisa\Message\TransactionStatus", $options);
    }

    /**
     * @param array $options
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function refund(array $options = []): \Omnipay\Common\Message\AbstractRequest
    {
        return $this->createRequest("\Omnipay\Ticketasavisa\Message\RefundPayment", $options);
    }

    public function setNotifyURL($url)
    {
        //$this->setReturnUrl($url);
        return $this->setParameter(Constants::CONFIG_KEY_NOTIFY_URL, $url);
    }

    public function getNotifyURL()
    {
        return $this->getParameter(Constants::CONFIG_KEY_NOTIFY_URL);
    }

    public function setReturnURL($url)
    {
        //$this->setReturnUrl($url);
        return $this->setParameter(Constants::CONFIG_KEY_RETURN_URL, $url);
    }

    public function getReturnURL()
    {
        return $this->getParameter(Constants::CONFIG_KEY_RETURN_URL);
    }

    public function getMerchantId()
    {
        return $this->getParameter(Constants::CONFIG_MERCHANT_ID);
    }

    public function setMerchantId($value)
    {
        return $this->setParameter(Constants::CONFIG_MERCHANT_ID, $value);
    }

    public function getPublicKey()
    {
        return $this->getParameter(Constants::CONFIG_PUBLIC_KEY);
    }

    public function setPublicKey($value)
    {
        return $this->setParameter(Constants::CONFIG_PUBLIC_KEY, $value);
    }

    public function getPrivateKey()
    {
        return $this->getParameter(Constants::CONFIG_PRIVATE_KEY);
    }

    public function setPrivateKey($value)
    {
        return $this->setParameter(Constants::CONFIG_PRIVATE_KEY, $value);
    }


    public function setOrderNumberPrefix($value)
    {
        return $this->setParameter(Constants::GATEWAY_ORDER_IDENTIFIER_PREFIX, $value);
    }

    public function getOrderNumberPrefix()
    {
        return $this->getParameter(Constants::GATEWAY_ORDER_IDENTIFIER_PREFIX);
    }

    public function setDiscount($value)
    {
        return $this->setParameter(Constants::CONFIG_APPLY_DISCOUNT, $value);
    }

    public function getDiscount()
    {
        return $this->getParameter(Constants::CONFIG_APPLY_DISCOUNT);
    }

    public function setOrderIdentifier($value)
    {
        return $this->setParameter(Constants::GATEWAY_ORDER_IDENTIFIER, $value);
    }

    public function getOrderIdentifier()
    {
        return $this->getParameter(Constants::GATEWAY_ORDER_IDENTIFIER);
    }
}
