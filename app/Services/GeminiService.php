<?php

namespace App\Services;

use App\Contracts\AiServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService implements AiServiceInterface
{
    public function generate(string $prompt): string
    {
        $apiKey = env('GEMINI_API_KEY');
        
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'contents' => [
                    [
                        'parts' => [['text' => $prompt]]
                    ]
                ]
            ]);

            // Se o Google recusar a requisição, paramos tudo e mostramos a "fofoca" exata na tela
            if (!$response->successful()) {
                dd('ERRO DO GOOGLE:', $response->json(), 'URL USADA:', $url);
            }

            // Se deu tudo certo, extraímos o texto
            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Erro ao extrair texto da resposta.';

        } catch (\Exception $e) {
            // Se a sua internet cair ou o Docker não alcançar o Google
            dd('ERRO DE CONEXÃO LOCAL:', $e->getMessage());
        }
    }
}