<?php

namespace loong\oauth\utils;

class CreatePem
{
    public static function create($path)
    {
        $data = Rsa::create();
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        file_put_contents($path . 'rsa_private.pem', $data['privatekey']);
        file_put_contents($path . 'rsa_public.pem', $data['publickey']);
    }
}
