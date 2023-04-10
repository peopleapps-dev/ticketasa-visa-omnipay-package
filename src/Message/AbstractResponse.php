<?php

/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\Ticketasavisa\Message;

use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\AbstractResponse as OmnipayAbstractResponse;
use Omnipay\Ticketasavisa\Constants;
use Omnipay\Ticketasavisa\Support\Cryptor;

abstract class AbstractResponse extends OmnipayAbstractResponse
{

    const AUTHORIZE_CREDIT_CARD_TRANSACTION_RESULTS = "CreditCardTransactionResults";
    const AUTHORIZE_BILLING_DETAILS                 = "BillingDetails";
    const AUTHORIZE_FRAUD_CONTROL_RESULTS           = "FraudControlResults";
    protected $encripted;

    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data = $data;

        parent::__construct($request, $data);

        switch ($request->getMessageClassName()) {
            case "HostedPage":
                $this->encript($this->data, $this->data[Constants::CONFIG_PRIVATE_KEY]);
                break;
            case "TransactionStatus":
                $this->decodeGatewayResponse($this->data);
                break;
            case "RefundPayment":
                $this->decodeGatewayResponse($this->data);
                break;
            default:
                break;
        }
    }

    public function getRequest(): AbstractRequest
    {
        return $this->request;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getEncript()
    {
        return $this->encripted;
    }

    protected function encript($data, $key)
    {
        unset($data[Constants::CONFIG_MERCHANT_ID]);
        unset($data[Constants::CONFIG_PUBLIC_KEY]);
        unset($data[Constants::CONFIG_PRIVATE_KEY]);
        $this->encripted = Cryptor::encrypt(json_encode($data), $key);
    }

    protected function decodeGatewayResponse($data): AbstractResponse
    {
        $httpResponse = $this->getData();

        $json = stripslashes($httpResponse->getBody()->getContents());
        $this->data = json_decode($json, true, 512, JSON_UNESCAPED_SLASHES);

        return $this;
    }
}
