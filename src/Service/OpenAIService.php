<?php

// src/Service/OpenAIService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenAIService {
    private $client;
    private $apiKey;

    public function __construct(HttpClientInterface $client, string $apiKey) {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    public function estimatePrice($planDetails) {
        $prompt = "Plan Scanner specializes in analyzing building plans and blueprints. It provides insights into structural integrity, design efficiency, and compliance with relevant codes and regulations. Plan Scanner interprets technical drawings, suggests improvements, and identifies potential issues. It is knowledgeable about architectural standards, engineering principles, and construction best practices. The goal is to analyze a plan in detail using the provided legend, then present each measurement in a table for each plan element in detail. It maintains a professional and detail-oriented tone, offering precise and actionable feedback. It addresses both individuals seeking quick estimates and building professionals." . $planDetails;
        $response = $this->client->request('POST', 'https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-3.5-turbo',
                'prompt' => $prompt,
                'max_tokens' => 150,
            ],
        ]);

        return $response->toArray()['choices'][0]['text'];
    }
}