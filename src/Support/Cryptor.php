<?php

namespace Omnipay\Ticketasavisa\Support;

use Omnipay\Common\Http\Exception;

class Cryptor
{

    const password_shuffled = "";

    public static function encrypt($plaintext, $key)
    {
        $result = '';
        for ($i = 0; $i < strlen($plaintext); $i++) {
            $char = substr($plaintext, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result .= $char;
        }

        return base64_encode($result);
    }

    public static function desEncrypt($encrypted)
    {
        try {
            $password = '3sc3RLrpd17';
            $method = 'aes-256-cbc';

            $password = substr(hash('sha256', $password, true), 0, 32);

            $decrypted = openssl_decrypt(
                base64_decode($encrypted),
                $method,
                $password,
                OPENSSL_RAW_DATA,
                (new Cryptor())->getIV()
            );
        } catch (Exception $error) {

            print_r($error);
        }

        return $decrypted;
    }

    public function getIV()
    {

        return chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
    }
}
