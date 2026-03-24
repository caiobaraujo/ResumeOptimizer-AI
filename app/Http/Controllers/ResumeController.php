<?php

namespace App\Http\Controllers;

use App\Contracts\AiServiceInterface;
use App\Models\GeneratedResume;
use App\Models\JobVacancy;
use App\Models\Resume;
use App\Services\ScraperService; // IMPORTANTE: Importar o novo serviço
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class ResumeController extends Controller
{
    public function create()
    {
        return Inertia::render('Resumes/Create');
    }

    // Injetamos o ScraperService aqui junto com a IA
    public function store(Request $request, AiServiceInterface $aiService, ScraperService $scraperService)
    {
        // 1. Validação
        $validated = $request->validate([
            'resume_content' => 'required|string|min:50',
            'job_url' => 'nullable|url',
            // A descrição da vaga agora é opcional, pois o usuário pode mandar só a URL
            'job_description' => 'nullable|string', 
        ]);

        // 2. Se não tem URL E não tem descrição, não podemos continuar
        if (empty($validated['job_url']) && empty($validated['job_description'])) {
            return redirect()->back()->withErrors(['error' => 'Você precisa informar o link da vaga OU colar a descrição dela.']);
        }

        // 3. LÓGICA DO WEB SCRAPING
        $jobText = $validated['job_description'] ?? '';

        if (!empty($validated['job_url'])) {
            $scrapedText = $scraperService->extractTextFromUrl($validated['job_url']); // null
            if ($scrapedText) {
                // Se o robô conseguiu ler o site, juntamos o texto do site com o texto (opcional) que o usuário digitou
                $jobText = $scrapedText . "\n\n" . $jobText;
            } else {
                // Se falhou (ex: bloqueio anti-robô), avisamos o usuário para colar o texto manual
                return redirect()->back()->withErrors(['error' => 'Não conseguimos extrair os dados deste link automaticamente. Por favor, copie e cole o texto da vaga no campo de descrição.']);
            }
        }

        // 4. Salvamos no banco (Mantido igual)
        $resume = Resume::create([
            'user_id' => $request->user()->id,
            'title' => 'Currículo Base - ' . now()->format('d/m/Y'),
            'original_content' => $validated['resume_content'],
        ]);

        $jobVacancy = JobVacancy::create([
            'user_id' => $request->user()->id,
            'url' => $validated['job_url'],
            'description' => $jobText, // Salvamos o texto final (Pode ser o do site ou o digitado)
        ]);

        // 5. Prompt para a IA (Mantido igual, mas usando o $jobText)
        $prompt = "Você é um especialista Sênior em Recrutamento e sistemas ATS.
        DESCRIÇÃO DA VAGA:
        {$jobText}
        
        CURRÍCULO ORIGINAL:
        {$validated['resume_content']}
        
        SUA TAREFA:
        Otimize o currículo do candidato para esta vaga, adicionando palavras-chave relevantes.
        Retorne APENAS um objeto JSON válido (sem formatar em markdown):
        {
            \"optimized_content\": \"Texto do novo currículo\",
            \"ats_score\": 85,
            \"feedback\": {\"fortes\": [], \"fracos\":[]},
            \"has_seniority_gap\": false
        }";

        // 6. Chamamos a IA (Mantido igual)
        $aiResponse = $aiService->generate($prompt);
        $cleanJson = trim(str_replace(['```json', '```'], '', $aiResponse));
        $aiData = json_decode($cleanJson, true);

        if (!$aiData || !isset($aiData['optimized_content'])) {
            return redirect()->back()->withErrors(['error' => 'A IA retornou um formato inválido. Tente novamente.']);
        }

        // 7. Salvamos o Resultado (Mantido igual)
        $generatedResume = GeneratedResume::create([
            'user_id' => $request->user()->id,
            'resume_id' => $resume->id,
            'job_vacancy_id' => $jobVacancy->id,
            'optimized_content' => $aiData['optimized_content'],
            'ats_score' => $aiData['ats_score'] ?? 0,
            'feedback' => $aiData['feedback'] ??[],
            'has_seniority_gap' => $aiData['has_seniority_gap'] ?? false,
        ]);

        return redirect()->route('generated-resumes.show', $generatedResume->id);
    }
}