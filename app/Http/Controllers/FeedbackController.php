<?php

namespace App\Http\Controllers;

use App\Http\Resources\BranchResource;
use App\Http\Resources\QuestionResource;
use App\Models\Branch;
use App\Models\Feedback;
use App\Models\FeedbackDetail;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    public function branch(Request $request){
        $query=Branch::where('status',true);
        if($request->has('slug')){
            $slug=$request->slug;
            $query=$query->where('slug','=',$slug);
        }
        $branch=$query->get();
        
        return $this->responsePagination($branch, BranchResource::collection($branch));
    }
    public function questions(){
        $questions=Question::with('questionOptions')->get();
        return $this->responsePagination($questions, QuestionResource::collection($questions));
    }

    public function store(Request $request){
        $branch = Branch::findOrFail($request->branch_id);
        $rules = [
            'branch_id' => 'required|exists:branches,id',
            'feedback' => 'required|array',
            'feedback.*.question_id' => 'required|exists:questions,id',
        ];
        
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            $token = env('TOKEN_BOT');
            $chatId = env('CHAT_ID');
            $feedback=Feedback::create([
                'branch_id'=>$request->branch_id,
                'comment'=>$request->comment
            ]);
            foreach ($request->feedback as $key => $feed) {
                FeedbackDetail::create([
                    'feedback_id'=>$feedback->id,
                    'branch_id'=>$request->branch_id,
                    'question_id'=>$feed['question_id'],
                    'question_option_id'=>$feed['question_option_id'],
                ]);
            }
            
            // Custom message format
            $message = "<b>Новый отзыв</b>\n\n";
            $message .= "<b>Филиал: </b>" . $branch->name."\n";
            foreach ($feedback->feedbackDetails as $key => $detail) {
                $question=$detail->question->question;
                $text=$detail->QuestionOption->option;
                $message .= "<b>$question: </b>" . $text."\n";
            }
            $message .= "<b>Пожелания: </b>" . $feedback->comment;

            Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => "<blockquote> $message </blockquote>",
                'parse_mode' => 'HTML',
            ]);
            return response()->json(['message' => 'Feedback success'],200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Feedback error'],500);
        }
    }
}
