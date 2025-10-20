<?php

// app/Services/FcmAccessToken.php
namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;

class FcmAccessToken
{
    public static function make(string $serviceAccountPath): string
    {
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $creds  = new ServiceAccountCredentials($scopes, $serviceAccountPath);
        $creds->fetchAuthToken(); // ensures internal state updated
        $token = $creds->getLastReceivedToken();
        return $token['access_token'] ?? '';
    }
}
