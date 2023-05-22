<?php

namespace App\Utils\KY;

class DES
{
    protected $cipher;// = MCRYPT_RIJNDAEL_128;
    protected $mode;// = MCRYPT_MODE_ECB;
    protected $pad_method = NULL;
    protected $secret_key = '';
    protected $iv = '';


    public function set_cipher($cipher)
    {
        $this->cipher = $cipher;
    }

    public function set_mode($mode)
    {
        $this->mode = $mode;
    }

    public function set_iv($iv)
    {
        $this->iv = $iv;
    }

    public function set_key($key)
    {
        $this->secret_key = $key;
    }

    public function require_pkcs5()
    {
        $this->pad_method = 'pkcs5';
    }

    protected function pad_or_unpad($str, $ext)
    {
        if ( is_null($this->pad_method) )
        {
            return $str;
        }
        else
        {
            $func_name = __CLASS__ . '::' . $this->pad_method . '_' . $ext . 'pad';
            if ( is_callable($func_name) )
            {
                $size = mcrypt_get_block_size('MCRYPT_3DES', 'ecb');
                return call_user_func($func_name, $str, $size);
            }
        }
        return $str;
    }

    protected function pad($str)
    {
        return $this->pad_or_unpad($str, '');
    }

    protected function unpad($str)
    {
        return $this->pad_or_unpad($str, 'un');
    }

    public function encrypt($str)
    {
        $iv_size = openssl_cipher_iv_length('AES-128-ECB');
        $iv = '';
        if ($iv_size > 0) {
            $iv = openssl_random_pseudo_bytes($iv_size);
        }

        $encrypted = openssl_encrypt($str, "AES-128-ECB", $this->secret_key, OPENSSL_RAW_DATA, $iv);

        $encrypted = base64_encode($encrypted);

        return $encrypted;
    }

    public function decrypt($str) {

        $iv_size = openssl_cipher_iv_length('AES-128-ECB');
        $iv = '';
        if ($iv_size > 0) {
            $iv = openssl_random_pseudo_bytes($iv_size);
        }

        $encrypted = base64_decode($str);

        if (!$encrypted) {
            error_log("Invalid base64 string: $encrypted");
            return false;
        }

        $decrypted = openssl_decrypt($encrypted, 'AES-128-ECB', $this->secret_key, OPENSSL_RAW_DATA, $iv);

        if ($decrypted === false) {
            $error = openssl_error_string();
            error_log("Decryption error: $error");
            return false;
        }

        return $decrypted;
    }

    public static function hex2bin($hexdata) {
        $bindata = '';
        $length = strlen($hexdata);
        for ($i=0; $i < $length; $i += 2)
        {
            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
        }
        return $bindata;
    }

    public static function pkcs5_pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    public static function pkcs5_unpad($text)
    {
        $pad_length = ord($text[strlen($text) - 1]);

        if ($pad_length > strlen($text)) {
            return false;
        }

        return substr($text, 0, -1 * $pad_length);
    }

    public function getMillisecond() {
        list($t1, $t2) = explode(' ', microtime());
        return $t2 .  ceil( ($t1 * 1000) );
    }

    public function getOrderId($agent){
        list($usec, $sec) = explode(" ", microtime());
        $msec=round($usec*1000);
        return $agent.date("YmdHis").$msec;
    }
}
?>
