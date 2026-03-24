<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobVacancy extends Model
{
    protected $fillable =[
        'user_id', 'url', 'company', 'role_title', 'description', 'company_culture', 'required_skills'
    ];

    protected function casts(): array
    {
        return[
            'company_culture' => 'array',
            'required_skills' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}