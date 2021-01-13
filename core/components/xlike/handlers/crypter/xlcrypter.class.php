<?php

class xlCrypter
{
    /**
     * @var string $method
     */
    protected $method;
    /**
     * @var string $salt
     */
    protected $salt;


    /**
     * @param string $salt
     * @param string $method
     */
    function __construct($salt, $method)
    {
        $this->salt = $salt;
        $this->method = $method;
    }


    /**
     * @param string $value
     *
     * @return false|string
     *
     * @throws Exception
     */
    public function encrypt($value)
    {
        if (!is_string($value) && !is_numeric($value)) {
            return false;
        }

        $iv_length = openssl_cipher_iv_length($this->method);
        $iv = openssl_random_pseudo_bytes($iv_length);
        $value_crypted_raw = openssl_encrypt($value, $this->method, $this->salt, $options=OPENSSL_RAW_DATA, $iv);
        $value_crypted = base64_encode($iv . $value_crypted_raw);

        return $value_crypted;
    }


    /**
     * @param string $value_crypted
     *
     * @return string
     */
    public function decrypt($value_crypted)
    {
        $value_crypted = base64_decode($value_crypted);
        $iv_length = openssl_cipher_iv_length($this->method);
        $iv = substr($value_crypted, 0, $iv_length);
        $value_crypted_raw = substr($value_crypted, $iv_length);
        $value_decrypted = openssl_decrypt($value_crypted_raw, $this->method, $this->salt, $options=OPENSSL_RAW_DATA, $iv);

        return $value_decrypted;
    }
}