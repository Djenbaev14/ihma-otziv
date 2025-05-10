<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $guarded=['id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function feedbackDetails()
    {
        return $this->hasMany(FeedbackDetail::class);
    }
    
}
