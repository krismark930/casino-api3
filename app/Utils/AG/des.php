<?php

namespace App\Utils\AG;

class DES
{

    protected $key = '';

    public function __construct($key)
    {
        $this->key = $key;
    }

    function encrypt($input)
    {

        $iv_size = openssl_cipher_iv_length('DES-ECB');
        $iv = '';
        if ($iv_size > 0) {
            $iv = openssl_random_pseudo_bytes($iv_size);
        }

        $encrypted = openssl_encrypt($input, "DES-ECB", $this->key, OPENSSL_RAW_DATA, $iv);

        // $encrypted = openssl_encrypt($input, "DES-ECB", $this->key, OPENSSL_ZERO_PADDING, $iv);
        

        if ($encrypted == false) {
            $error = openssl_error_string();
            return $error;
            $t = date("Y-m-d H:i:s");
            $tmpfile = $_SERVER['DOCUMENT_ROOT'] . "/tmp/ssl_" . date("Ymd") . ".txt";
            $f = fopen($tmpfile, 'a');
            fwrite($f, $t . "\r\nSSL_ERROR\r\n$error\r\n\r\n");
            fclose($f);
        }

        $encrypted = base64_encode($encrypted);

        return $encrypted;
    }

    function decrypt($str)
    {

        $iv = '';

        $encrypted = base64_decode($str);

        if (!$encrypted) {
            error_log("Invalid base64 string: $encrypted");
            return false;
        }

        $decrypted = openssl_decrypt($encrypted, 'des-ecb', $this->key, OPENSSL_RAW_DATA, $iv);

        if ($decrypted === false) {
            $error = openssl_error_string();
            error_log("Decryption error: $error");
            return false;
        }

        return $decrypted;
    }
}
