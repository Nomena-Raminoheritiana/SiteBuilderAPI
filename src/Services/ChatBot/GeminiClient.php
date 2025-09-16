<?php

namespace App\Services\ChatBot;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeminiClient
{
    public function __construct(
        private HttpClientInterface $client, 
        private string $apiKey
    ) {}

    public function generateContent(string $message, array|string $subject = null): string
    {
        try {
            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $message]
                        ]
                    ]
                ]
            ];

            // Ajouter system_instruction si fourni
            if ($subject) {
                $payload['system_instruction'] = [
                    'parts' => [
                        ['text' => is_array($subject) ? json_encode($subject, JSON_UNESCAPED_UNICODE) : $subject]
                    ]
                ];
            }

            $response = $this->client->request(
                'POST',
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent',
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'x-goog-api-key' => $this->apiKey,
                    ],
                    'json' => $payload,
                ]
            );

            $data = $response->toArray(false);

            return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No reply available';

        } catch (\Throwable $e) {
            return 'No reply available';
        }
    }
}
