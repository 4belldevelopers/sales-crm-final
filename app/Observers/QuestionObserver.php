<?php

namespace App\Observers;

use App\Models\Question;

class QuestionObserver
{
    public function saving(Question $question)
    {
        if (company()) {
            $question->company_id = company()->id;
        }
    }
}
