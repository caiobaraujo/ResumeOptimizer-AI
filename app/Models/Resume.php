<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    // Mass Assignment Protection
    protected $fillable =[
        'user_id', 'title', 'original_content', 'parsed_data'
    ];

    // Data Mutators (Casts)
    protected function casts(): array
    {
        return [
            'parsed_data' => 'array',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}