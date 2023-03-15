<?php

namespace App\Service;

use DateTimeImmutable;

class JWTService
{
    // On génère le JWT

    /**
     * Generating the JWT
     * @param array<string, int|string> $header
     * @param array<string, string> $payload
     * @param string $secret
     * @param int $validity
     * @return string
     */
    public function generate(
        array $header,
        array $payload,
        string $secret,
        int $validity = 10800
    ): string {
        if ($validity > 0) {
            $now = new DateTimeImmutable();
            $exp = $now->getTimestamp() + $validity;

            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $exp;
        }

        // On encode en base64 le Header et le Payload
        $encodedHeader = (string)json_encode($header);
        $base64Header = base64_encode($encodedHeader);
        $encodedPayload = (string)json_encode($payload);
        $base64Payload = base64_encode($encodedPayload);

        // on "nettoie" les valeurs encodées (suppression des +, / et =)
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);

        // On génère la signature
        $secret = base64_encode($secret);

        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);

        $base64Signature = base64_encode($signature);

        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64Signature);

        // on génère le token
        $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;

        return $jwt;
    }

    // On vérifie la validité du JWT (correctement formé)

    public function isValid(string $token): bool
    {
        return preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $token
        ) === 1;
    }

    // On récupère le Payload
    /**
     * @param string $token
     * @return array<string, string>
     */
    public function getPayload(string $token): array
    {
        // On démantèle le token
        $array = explode('.', $token);

        // On décode le Payload
        $payload = json_decode(base64_decode($array[1]), true);

        return $payload;
    }

    // On récupère le Header
    /**
     * @param string $token
     * @return array<string, string>
     */
    public function getHeader(string $token): array
    {
        // On démantèle le token
        $array = explode('.', $token);

        // On décode le Header
        $header = json_decode(base64_decode($array[0]), true);

        return $header;
    }

    // On vérifie si le token est expiré
    /**
     * @param string $token
     * @return bool
     */
    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);

        $now = new DateTimeImmutable();

        return $payload['exp'] < $now->getTimestamp();
    }

    // On vérifie la signature du token
    /**
     * @param string $token
     * @param string $secret
     * @return bool
     */
    public function check(string $token, string $secret)
    {
        // On récupère le Header et le Payload
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        // On génère un nouveau token
        $verifToken = $this->generate($header, $payload, $secret, 0);

        return $token === $verifToken;
    }
}
