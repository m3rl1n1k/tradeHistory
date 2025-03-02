<?php

namespace App\Service;

use App\Service\Interfaces\CrypticInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CryptService implements CrypticInterface
{

    private string $key;
    private string $cipher;

    public function __construct(
        #[Autowire('%env(ENCRYPTION_KEY)%')]
        string $key,
    )
    {
        $this->key = $key;
        $this->cipher = "AES-256-CBC";
    }

    public function encrypt(string $data): string
    {
        $ivLength = openssl_cipher_iv_length($this->cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $encryptedDate = openssl_encrypt($data, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $encryptedDate);
    }

    public function decrypt(string $data): string
    {
        if (empty($data) || base64_decode($data, true) === false) {
            return $data;
        }
        $encryptedData = base64_decode($data);
        $ivLength = openssl_cipher_iv_length($this->cipher);
        $iv = substr($encryptedData, 0, $ivLength);
        $encryptedData = substr($encryptedData, $ivLength);
        return openssl_decrypt($encryptedData, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv);
    }
}