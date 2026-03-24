<?php

namespace App\Http\Controllers;

use App\Models\GeneratedResume;
use Inertia\Inertia;

class GeneratedResumeController extends Controller
{
    public function show($id)
    {
        // Busca o currículo gerado no banco, e traz junto os dados da vaga e do currículo original (Eager Loading)
        $generatedResume = GeneratedResume::with(['jobVacancy', 'originalResume'])
            ->findOrFail($id);

        // Bloqueia o acesso se o currículo não for do usuário logado (Segurança / Autorização)
        if ($generatedResume->user_id !== auth()->id()) {
            abort(403, 'Acesso negado.');
        }

        // Manda os dados para a tela Vue
        return Inertia::render('GeneratedResumes/Show', [
            'generatedResume' => $generatedResume
        ]);
    }
}