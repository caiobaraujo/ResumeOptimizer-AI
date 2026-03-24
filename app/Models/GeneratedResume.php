<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedResume extends Model
{
    protected $fillable =[
        'user_id', 'resume_id', 'job_vacancy_id', 'optimized_content', 'ats_score', 'feedback', 'has_seniority_gap'
    ];

    protected function casts(): array
    {
        return[
            'feedback' => 'array',
            'has_seniority_gap' => 'boolean',
        ];
    }

    public function jobVacancy()
    {
        return $this->belongsTo(JobVacancy::class);
    }

    public function originalResume()
    {
        return $this->belongsTo(Resume::class, 'resume_id');
    }
}