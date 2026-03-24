<?php

namespace App\Http\Controllers;

use App\Contracts\AiServiceInterface;
use App\Models\GeneratedResume;
use App\Models\JobVacancy;
use App\Models\Resume;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class ResumeController extends Controller
{
    public function create()
    {
        return Inertia::render('Resumes/Create');
    }

    /**
     * Injetamos a interface AiServiceInterface diretamente no método (Dependency Injection).
     * O Laravel é inteligente o suficiente para entregar a classe GeminiService aqui.
     */
    public function store(Request $request, AiServiceInterface $aiService)
    {
        $validated = $request->validate([
            'resume_content' => 'required|string|min:50',
            'job_url' => 'nullable|url',
            'job_description' => 'required|string|min:20',
        ]);

        $resume = Resume::create([
            'user_id' => $request->user()->id,
            'title' => 'Currículo Base - ' . now()->format('d/m/Y'),
            'original_content' => $validated['resume_content'],
        ]);

        $jobVacancy = JobVacancy::create([
            'user_id' => $request->user()->id,
            'url' => $validated['job_url'],
            'description' => $validated['job_description'],
        ]);

        // 1. Criamos a instrução (Prompt Engineering) para o Gemini
        $prompt = "Você é um especialista Sênior em Recrutamento, Seleção e sistemas ATS (Applicant Tracking System).
        Abaixo, fornecerei a DESCRIÇÃO DE UMA VAGA e o CURRÍCULO ORIGINAL de um candidato.
        
        DESCRIÇÃO DA VAGA:
        {$validated['job_description']}
        
        CURRÍCULO ORIGINAL:
        {$validated['resume_content']}
        
        SUA TAREFA:
        Otimize o currículo do candidato para esta vaga específica, adicionando palavras-chave relevantes da vaga, mas sem inventar mentiras sobre habilidades técnicas.
        Retorne APENAS um objeto JSON válido, sem formatação markdown (sem ```json), estritamente com esta estrutura:
        {
            \"optimized_content\": \"Texto completo do novo currículo otimizado.\",
            \"ats_score\": 85, // Um número inteiro de 0 a 100 indicando a aderência
            \"feedback\": {
                \"fortes\": [\"ponto forte 1\", \"ponto forte 2\"],
                \"fracos\":[\"ponto a melhorar 1\", \"ponto a melhorar 2\"]
            },
            \"has_seniority_gap\": false // true se o candidato for Junior e a vaga Pleno/Senior, etc.
        }";

        // 2. Chamamos a IA
        $aiResponse = $aiService->generate($prompt);


        // 3. Limpamos a resposta caso a IA retorne markdown "```json ... ```" e convertemos para Array PHP
        $cleanJson = trim(str_replace(['```json', '```'], '', $aiResponse));
        $aiData = json_decode($cleanJson, true);

        // 4. Se a IA falhar em retornar o JSON correto, criamos um fallback (plano B de segurança)
        if (!$aiData || !isset($aiData['optimized_content'])) {
            Log::error('Falha ao decodificar JSON da IA: ' . $aiResponse);
            return redirect()->back()->withErrors(['error' => 'A IA retornou um formato inválido. Tente novamente.']);
        }

        // 5. Salvamos o resultado no banco
        $generatedResume = GeneratedResume::create([
            'user_id' => $request->user()->id,
            'resume_id' => $resume->id,
            'job_vacancy_id' => $jobVacancy->id,
            'optimized_content' => $aiData['optimized_content'],
            'ats_score' => $aiData['ats_score'] ?? 0,
            'feedback' => $aiData['feedback'] ??[],
            'has_seniority_gap' => $aiData['has_seniority_gap'] ?? false,
        ]);

        // 6. Redirecionamos para a tela de visualização do resultado
        return redirect()->route('generated-resumes.show', $generatedResume->id);
    }
}