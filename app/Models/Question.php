<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $guarded=['id'];
    public function questionOptions()
    {
        return $this->hasMany(QuestionOption::class);
    }
    
}
