<?php

namespace App\Utils\LYPAY;

class DES {

    protected $key = '';

    public function __construct($key) {
        $this->key = $key;
    }

    function encrypt($input) {

        $iv = '';

        $encrypted = openssl_encrypt($input, "des-ecb", $this->key, OPENSSL_RAW_DATA, $iv);

        $encrypted = base64_encode($encrypted);

        return $encrypted;
    }

    function decrypt($str) {

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

?>
