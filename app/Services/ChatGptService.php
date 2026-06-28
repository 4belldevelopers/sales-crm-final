<?php

namespace App\Services;

class ChatGptService
{
    public function parseResume($resumeText)
    {
        $apiKey = env('CHATGPT_API_KEY');
        $url = 'https://api.openai.com/v1/chat/completions';

        // Prepare the cURL request
        $ch = curl_init($url);
        $postData = json_encode([
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a resume parser.'],
                ['role' => 'user', 'content' => $resumeText],
            ],
        ]);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        // Execute the cURL request and fetch the response
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}

