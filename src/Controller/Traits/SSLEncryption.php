<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\Traits;

trait SSLEncryption
{
    /**
     * 
     */
    public function SSLCrypt(mixed $data): string|false
    {
        return base64_encode(
            openssl_encrypt(
                data: json_encode($data),
                cipher_algo: 'AES-128-CBC',
                passphrase: pack('a16', env('OPENSSL_SECRET', 'no_secret_at_all')),
                options: 0,
                iv: pack('a16', env('OPENSSL_SECRET', 'no_secret_at_all'))
            )
        );
    }

    /**
     * 
     */
    public function SSLDecrypt(string $data): mixed
    {
        return json_decode(
            openssl_decrypt(
                data: base64_decode($data),
                cipher_algo: 'AES-128-CBC',
                passphrase: pack('a16', env('OPENSSL_SECRET', 'no_secret_at_all')),
                options: 0,
                iv: pack('a16', env('OPENSSL_SECRET', 'no_secret_at_all'))
            )
        );
    }
}
