<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScraperService
{
    public function extractTextFromUrl(string $url): ?string
    {
        // Pegamos a URL do microserviço Python no arquivo .env
        $scraperApiUrl = env('PYTHON_SCRAPER_URL', 'http://localhost:5001/scrape');

        try {
            // O Laravel faz um POST para o Flask
            $response = Http::timeout(15)->post($scraperApiUrl,[
                'url' => $url
            ]);

            // Se o Python retornar sucesso, devolvemos o texto limpo
            if ($response->successful() && $response->json('success')) {
                $text = $response->json('text');
                Log::info("Scraping realizado com sucesso na URL: {$url}");
                return $text;
            }

            // Se o site bloqueou o robô, registramos no Log (Requisito seu)
            Log::warning("Scraping falhou para a URL: {$url}. Motivo: " . $response->json('error'));
            return null;

        } catch (\Exception $e) {
            Log::error("Erro fatal ao tentar comunicar com o Microserviço Python: " . $e->getMessage());
            return null;
        }
    }
}