<?php

namespace Omnipay\Ticketasavisa\Message;

use Omnipay\Ticketasavisa\Exception\InvalidResponseData;
use Omnipay\Ticketasavisa\Support\Cryptor;
use Omnipay\Ticketasavisa\Support\ParametersInterface;
use Omnipay\Ticketasavisa\Constants;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
implements ParametersInterface
{

    protected $data = [];
    protected $encripted;
    protected $commonHeaders = [
        'Accept'       => 'application/json',
        'Content-Type' => 'application/json',
    ];
    protected $PWTServices = [
        "Purchase"          => [
            "request"  => "HostedPage",
            "response" => "HostedPageResponse",
        ],
        "TransactionStatus" => [
            "request"  => "Transactions",
            "response" => "TransactionStatusResponse",
        ],
        "RefundPayment"     => [
            "request"  => "refund",
            "response" => "RefundPaymentResponse",
        ],
    ];

    public function sendData($data)
    {

        switch ($this->getMessageClassName()) {

            case "HostedPage":
                return $this->response = new HostedPageResponse($this, $data);

            case "TransactionStatus":

                $this->addCommonHeaders($data);

                $uri = $this->getEndpoint();

                $httpResponse = $this->httpClient->request(
                    "GET",
                    $uri,
                    $this->commonHeaders,
                    null
                );

                return $this->response = new TransactionStatusResponse($this, $httpResponse);

            case "RefundPayment":
                $this->addCommonHeaders($data);
                unset($data[Constants::CONFIG_MERCHANT_ID]);
                unset($data[Constants::CONFIG_PUBLIC_KEY]);
                unset($data[Constants::CONFIG_PRIVATE_KEY]);

                $requestBody = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                $uri = $this->getEndpointRefaund();

                $httpResponse = $this->httpClient->request(
                    "GET",
                    $uri,
                    $this->commonHeaders,
                    null //$requestBody
                );

                return $this->response = new RefundPaymentResponse($this, $httpResponse);

                break;

            default:
                throw new InvalidResponseData($this->getMessageClassName());
        }
    }

    protected function addCommonHeaders($data): AbstractRequest
    {
        $this->commonHeaders['merchant_id'] = $this->getMerchantId();
        $this->commonHeaders['merchant_key_id'] = $this->getPublicKey();
        $this->commonHeaders['merchant_secret_key'] = $this->getPrivateKey();
        $this->encript($data, $this->getPrivateKey());

        return $this;
    }

    protected function getEndpoint()
    {
        return ($this->getTestMode() ? Constants::PLATFORM_TA_UAT : Constants::PLATFORM_TA_PROD)
            . '/fetchTransaction?data=' . $this->getEncript();
    }

    protected function getEndpointRefaund()
    {
        return ($this->getTestMode() ? Constants::PLATFORM_TA_UAT : Constants::PLATFORM_TA_PROD)
            . '/refaundTransaction?data=' . $this->getEncript();
    }


    public function getMessageClassName()
    {
        $className = explode("\\", get_called_class());

        return array_pop($className);
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

    protected function createQueryParamProtect($data)
    {
        return json_encode($data);
    }

    public function getTransactionId()
    {
        //  print_r($this->getParameter(Constants::CONFIG_TRANSACTION_IDENTIFIER));
        return $this->getParameter(Constants::CONFIG_TRANSACTION_IDENTIFIER);
    }

    public function setTransactionId($value)
    {
        //  print_r($value);
        return $this->setParameter(Constants::CONFIG_TRANSACTION_IDENTIFIER, $value);
    }

    public function setOrderIdentifier($value)
    {
        return $this->setParameter(Constants::GATEWAY_ORDER_IDENTIFIER, $value);
    }

    public function getOrderIdentifier()
    {
        return $this->getParameter(Constants::GATEWAY_ORDER_IDENTIFIER);
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

    public function getDate()
    {
        return date("D, d M Y G:i:s");
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

    // Function used to generate the digest for the given payload
    public function getGenerateDigest($requestPayload)
    {
        $utf8EncodedString = utf8_encode($requestPayload);
        $digestEncode = hash("sha256", $utf8EncodedString, true);
        return base64_encode($digestEncode);
    }


    // Function to generate the HTTP Signature
    // param: resourcePath - denotes the resource being accessed
    // param: httpMethod - denotes the HTTP verb
    // param: currentDate - stores the current timestamp
    public function getSignature($resourcePath, $httpMethod, $body)
    {

        $digest = "";

        if ($httpMethod == "get") {
            $signatureString = "host: " . $this->getEndpoint() . "\ndate: " . $this->getDate() . "\n(request-target): " . $httpMethod . " " . $resourcePath . "\nv-c-merchant-id: " . $this->getMerchantId();
            $headerString = "host date (request-target) v-c-merchant-id";
        } else if ($httpMethod == "post") {
            //Get digest data        
            $digest = $this->getGenerateDigest($body);

            $signatureString = "host: " . $this->getEndpoint() . "\ndate: " . $this->getDate() . "\n(request-target): " . $httpMethod . " " . $resourcePath . "\ndigest: SHA-256=" . $digest . "\nv-c-merchant-id: " . $this->getMerchantId();
            $headerString = "host date (request-target) digest v-c-merchant-id";
        }

        $signatureByteString = utf8_encode($signatureString);
        $decodeKey = base64_decode($this->getPrivateKey());
        $signature = base64_encode(hash_hmac("sha256", $signatureByteString, $decodeKey, true));
        $signatureHeader = array(
            'keyid="' . $this->getPublicKey() . '"',
            'algorithm="HmacSHA256"',
            'headers="' . $headerString . '"',
            'signature="' . $signature . '"',
        );

        $signatureToken = implode(", ", $signatureHeader);

        return $signatureToken;
    }
}
