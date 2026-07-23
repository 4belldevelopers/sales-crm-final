<?php

namespace App\Http\Requests;

use App\Job;
use App\Question;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class StoreJobApplication extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $job = Job::where('id', $this->job_id)->first();
        $requiredColumns = $job->required_columns;
        $sectionVisibility = $job->section_visibility;

        $rules = [
            'full_name' => 'required',
            'email' => 'email|required',
            'phone' => 'required',
            'job_id' => 'required|exists:jobs,id',
            // The new one-stage upload sends a final URL, not a temp path.
            'resume_url' => 'nullable|string|max:1024',
            // Tolerate legacy clients still emitting the temp-path field.
            // The controller falls back to headCheck() if only this is present.
            'resume_temp_path' => 'nullable|string|max:1024',
            // When the sectionVisibility gate does not add 'resume' for this job,
            // we still bound the multipart fallback file so it never exceeds 5 MB.
            'resume' => 'nullable|mimes:jpeg,jpg,png,doc,docx,rtf,xls,xlsx,pdf|max:5120',
        ];

        if ($requiredColumns['gender']) {
            $rules = Arr::add($rules, 'gender', 'required|in:male,female,others');
        }
        if ($requiredColumns['dob']) {
            $rules = Arr::add($rules, 'dob', 'required|date');
        }
        if ($requiredColumns['country']) {
            $rules = Arr::add($rules, 'country', 'required|integer|min:1');
            $rules = Arr::add($rules, 'state', 'required|integer|min:1');
            $rules = Arr::add($rules, 'city', 'required');
        }

        if(isset($sectionVisibility)){
            foreach ($sectionVisibility as $key => $section) {
                if ($section === 'yes') {
                    if ($key === 'profile_image') {
                        $rules = Arr::add($rules, 'photo', 'required|mimes:jpeg,jpg,png');
                    }
                    if ($key === 'resume') {
                        $rules = Arr::add($rules, 'resume', 'required|mimes:jpeg,jpg,png,doc,docx,rtf,xls,xlsx,pdf|max:5120');
                    }
                }
            }
        }


        $answers = $this->get('answer');
        if(isset($answers) && !empty($this->get('answer')))
        {
            foreach($this->get('answer') as $key => $value){

                $answer = Question::where('id', $key)->first();
                if($answer->required == 'yes')
                    $rules["answer.{$key}"] = 'required';
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'answer.*' => 'This field is required.',
            'dob.required' => 'Date of Birth field is required.',
            'country.min' => 'Please select country.',
            'state.min' => 'Please select state.',
            'city.required' => 'Please enter city.',
        ];
    }
}
