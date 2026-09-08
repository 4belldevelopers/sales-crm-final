<?php

namespace App\Services;

use App\Helper\GeneralMethod;
use App\Models\ApplicationStatus;
use App\Models\JobApplication;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class JobApplicationsNewUploadService
{
    protected $elasticService;

    public function __construct(ElasticService $elasticService)
    {
        $this->elasticService = $elasticService;
    }

    public function candidateIndexElastic($request, $company_id, $submitted_by, $jobApplication_company_id, $company_id_roles, $app_duplicate, $app_duplicate_days)
    {

        try {

            $applicationStatus = ApplicationStatus::first();
            $jobApplication = new JobApplication;
            $jobApplication->full_name = ucwords(strtolower($request->full_name));
            $jobApplication->job_id = $request->job_id;
            $jobApplication->status_id = 8;
            $jobApplication->email = $request->email;
            $jobApplication->phone = $request->phone;
            $jobApplication->column_priority = 0;
            $jobApplication->phone_code = ($request->primary_country_code) ? @$request->primary_country_code : null;
            $jobApplication->second_phone_code = ($request->secondary_country_code) ? @$request->secondary_country_code : null;
            $total_exp = (isset($request->total_exp_year) && $request->total_exp_year !== '' || isset($request->total_exp_month) && $request->total_exp_month !== '') ? $request->total_exp_year.'.'.$request->total_exp_month : null;
            $rel_exp = (isset($request->rel_exp_year) && $request->rel_exp_year !== '' || isset($request->rel_exp_month) && $request->rel_exp_month !== '') ? $request->rel_exp_year.'.'.$request->rel_exp_month : null;
            $jobApplication->total_exp = $total_exp;
            $jobApplication->rel_exp = $rel_exp;

            $jobApplication->ctc = $request->ctc;
            $jobApplication->current_ctc_cur = $request->current_ctc_cur;
            $jobApplication->current_ctc_mode = $request->current_ctc_mode;

            $jobApplication->ectc = $request->ectc;
            $jobApplication->current_ectc_cur = $request->current_ectc_cur;
            $jobApplication->current_ectc_mode = $request->current_ectc_mode;
            $jobApplication->gender = $request->gender;
            $jobApplication->notice_comments = @$request->notice_comments;

            $currency = GeneralMethod::currency();

            $ecurrency = $currency[$request->current_ectc_cur];
            $econvertedAmount = $request->ectc;
            if ($request->current_ectc_cur > 0) {
                $econvertedAmount = GeneralMethod::convertToINR($econvertedAmount, $ecurrency);
            }

            $ectcdivided = GeneralMethod::salaryDividedByMode($request->current_ectc_mode, $econvertedAmount);
            $jobApplication->ectc_yearly = @$ectcdivided->yearly;
            $jobApplication->ectc_monthly = @$ectcdivided->monthly;
            $jobApplication->ectc_hourly = @$ectcdivided->hourly;

            $currency = $currency[$request->current_ctc_cur];
            $convertedAmount = $request->ctc;
            if ($request->current_ctc_cur > 0) {
                $convertedAmount = GeneralMethod::convertToINR($convertedAmount, $currency);
            }

            $ctcdivided = GeneralMethod::salaryDividedByMode($request->current_ctc_mode, $convertedAmount);

            $jobApplication->ctc_yearly = @$ctcdivided->yearly;
            $jobApplication->ctc_monthly = @$ctcdivided->monthly;
            $jobApplication->ctc_hourly = @$ctcdivided->hourly;

            $jobApplication->percentage_hike = $request->percentage_hike;
            $jobApplication->comment = $request->comment;
            $jobApplication->pincode = (@$request->pincode) ? $request->pincode : '';

            $jobApplication->current_designation = $request->current_designation;
            $jobApplication->current_company = $request->current_company;

            $cnt = 0;
            $skillStr = '';
            $skillArr = json_decode($request->skills, true);
            if (! empty($skillArr)) {
                foreach ($skillArr as $sval) {
                    if ($cnt == 0) {
                        $skillStr .= $sval['value'];
                    } else {
                        $skillStr .= ' , '.$sval['value'];
                    }
                    $cnt++;
                }
            }
            $jobApplication->skills = $skillStr;

            $jobApplication->master_status_id = 8;
            $jobApplication->secondrary_email = $request->secondrary_email;
            $jobApplication->secondary_mobile = $request->secondary_mobile;

            if (! empty($submitted_by)) {
                $jobApplication->submitted_by = $submitted_by;
            }

            $serving_notice_period = '';
            $serving_notice_period = $request->serving_notice_period;
            if ($serving_notice_period == 1) {
                $jobApplication->np_id = $request->np_id;
            } elseif ($serving_notice_period == 2) {
                $jobApplication->np_id = 'Immediate';
                $jobApplication->last_working_date = $request->np_id;
            } else {
                $jobApplication->np_id = $request->ddl_np_id;
            }

            if (! empty($request->qualification)) {
                $jobApplication->qualification = $request->qualification;
            }
            $jobApplication->company_id = $jobApplication_company_id;
            $jobApplication->serving_notice_period_mode = $request->serving_notice_period;
            if ($request->has('gender')) {
                $jobApplication->gender = $request->gender;
            }
            if ($request->has('dob')) {
                $jobApplication->dob = $request->dob;
            }

            $stateId = '';
            $cityID = '';
            $countryID = '';
            $location_find = '';
            $work_city = request('kt_tagify_work_location');
            $work_city_all = json_decode($work_city, true);

            if (\request('kt_tagify_work_location') != '') {
                foreach ($work_city_all as $cityVal) {
                    $parts = explode('|', $cityVal['value']);
                    // Trim to remove any leading or trailing whitespace
                    $cityName = trim($parts[0]);
                    $stateName = trim($parts[1]);
                    $countrName = trim($parts[2]);
                    $location_find = $cityName.'_'.$stateName.'_'.$countrName;
                    // dd($cityName);
                    // Debugging line
                    $city_row = DB::table('city_list')
                        ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
                        ->join('countries', 'countries.id', '=', 'state_list.country_id')
                        ->select('city_list.city_id', 'countries.id as country_id')
                        ->where('city_list.city', '!=', '')
                        ->where('city_list.city', '=', $cityName)
                        ->where('countries.country_name', '=', $countrName)
                        ->first();

                    if ($city_row) {
                        $cityID = (@$city_row) ? $city_row->city_id : '';
                        $countryID = (@$city_row) ? $city_row->country_id : '';
                        $stateId = GeneralMethod::stateId($cityID);
                    }

                }
            }

            $jobApplication->country = $countryID;
            $jobApplication->state = $stateId;

            if ($jobApplication->country == 101) {
                $jobApplication->city_id = $cityID;
            } else {
                $jobApplication->city_id = $cityID;
            }

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = time().'.'.$file->getClientOriginalExtension();
                $filePath = 'Images/'.$filename;

                Storage::disk('s3')->put($filePath, file_get_contents($file));

                $fileUrl = Storage::disk('s3')->url($filePath);
                $jobApplication->photo = $fileUrl;
                // $jobApplication->photo = Files::upload($request->photo, 'candidate-photos');
            }

            $returnCount = 0;
            $message = 'messages.createdSuccessfully';
            $company_id = $company_id_roles;

            $applicationEmail = $request->email;
            if (($app_duplicate == 1) || ($app_duplicate == 2)) {
                $returnCount = GeneralMethod::checkDuplicateCandidate($company_id, $app_duplicate, $request->job_id, $applicationEmail, $app_duplicate_days, $request->phone);
                if ($returnCount > 0 && $app_duplicate == 1) {
                    $message = 'Duplicate :: application already in this job';
                    $jobApplication->duplicate = 1;
                } elseif ($returnCount > 0 && $app_duplicate == 2) {
                    $message = 'Duplicate :: application already in system';
                    $jobApplication->duplicate = 1;
                }
            }

            $chkApplication = DB::table('job_applications')->select('job_applications.id')->where('company_id', $company_id)->where('email', $applicationEmail)->orWhere('phone', $request->phone)->count();
            if ($chkApplication > 0) {
                // $jobApplication->duplicate = 1;
            }
            // dd("k");
            $jobApplication->save();

            return true;
        } catch (Exception $e) {
            Log::error('Elastic Job Application Index queue failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
