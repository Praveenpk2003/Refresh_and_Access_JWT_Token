<?php

class JWT
{
    private static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode($data)
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public static function generate(array $payload)
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];

        $headerEncoded  = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));

        $signature = hash_hmac(
            'sha256',
            "$headerEncoded.$payloadEncoded",
            env('JWT_SECRET'),
            true
        );

        return $headerEncoded . '.' . $payloadEncoded . '.' . self::base64UrlEncode($signature);
    }

    public static function validate($token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        [$header, $payload, $signature] = $parts;

        $expected = hash_hmac(
            'sha256',
            "$header.$payload",
            env('JWT_SECRET'),
            true
        );

        if (!hash_equals(self::base64UrlDecode($signature), $expected)) {
            return false;
        }

        $payload = json_decode(self::base64UrlDecode($payload), true);

        if (!$payload || !isset($payload['exp']) || $payload['exp'] < time()) {
            return false;
        }

        return $payload;
    }
}
