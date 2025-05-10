<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackDetail extends Model
{
    use HasFactory;

    protected $guarded=['id'];
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
    public function QuestionOption()
    {
        return $this->belongsTo(QuestionOption::class);
    }
}
