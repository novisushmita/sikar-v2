<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Log;

class FcmService
{
    private string $projectId = 'sikar-a9a0d';

    private function getAccessToken(): string
    {
        $credentialsPath = storage_path('app/firebase-credentials.json');
        $credentials = new ServiceAccountCredentials(
            'https://www.googleapis.com/auth/firebase.messaging',
            json_decode(file_get_contents($credentialsPath), true)
        );
        $token = $credentials->fetchAuthToken();
        return $token['access_token'];
    }

    public function kirim(string $token, string $judul, string $isi): void
    {
        $accessToken = $this->getAccessToken();

        $payload = json_encode([
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $judul,
                    'body'  => $isi
                ]
            ]
        ]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send",
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode !== 200) {
            Log::error('FCM kirim gagal: ' . $response);
        }
    }

    public function kirimKeBanyak(array $tokens, string $judul, string $isi): void
    {
        foreach ($tokens as $token) {
            $this->kirim($token, $judul, $isi);
        }
    }
}