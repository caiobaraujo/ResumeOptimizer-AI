<?php

namespace App\Contracts;

interface AiServiceInterface
{
    /**
     * Envia um prompt para a IA e retorna a resposta em texto.
     */
    public function generate(string $prompt): string;
}