<?php

namespace Omnipay\Ticketasavisa\Support;

interface ParametersInterface
{
    public function setMerchantId($PWTID);

    public function getMerchantId();

    public function setPublicKey($PWD);

    public function getPublicKey();

    public function setPrivateKey($PWD);

    public function getPrivateKey();

    public function setDiscount($value);

    public function getDiscount();

    public function setOrderIdentifier($value);

    public function getOrderIdentifier();
}
