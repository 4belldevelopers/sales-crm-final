<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\PackageFeature;
use App\Models\PackageDetail;
use App\Http\Requests\SuperAdmin\StorePackage;
use App\Helper\Reply;
use Illuminate\Support\Facades\DB;

class PackageController extends SuperAdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.packages';
        $this->pageIcon = 'icon-settings';
    }
    public function index()
    {
        $this->packages = Package::all();
        $this->paymentSetting = PaymentSetting::first();
        $this->packageData = Package::all();
            // dd($this->packageData);
        return view('super-admin.packages.index', $this->data);
    }
    public function package_feature(Request $request)
    {
        $perPage = (int) $request->input('per_page', $request->input('size', $request->input('limit', 10)));
        $perPage = min(max($perPage, 1), 100);
        $this->packageFeature = PackageFeature::paginate($perPage)->appends($request->query());
        return view('super-admin.packages.package_feature', $this->data);
    }
    public function add_edit_package_feature(Request $request,$id){
        // dd($request->all());

        if ($request->method() === 'PUT') {
            // dd($request->all() , $request->token_id);
            if(!empty($request->token_id)){
                // dd("dkj");
                $PackageFeature = PackageFeature::findOrFail($request->token_id);
                $message = 'Updated successfully';
            }else{
                
                $PackageFeature = new PackageFeature();
                $field_name  = preg_replace('/[^a-zA-Z0-9]/', '', $request->name);
                $PackageFeature->field_name = $field_name;
                if(PackageFeature::where('field_name','=', $field_name)->exists()){
                    return response()->json(['message' => 'Already exists','url'=> route('superadmin.package.package_feature'),'status'=>'faild']);
                }
                $message = 'Created successfully';
            }
            // dd($request->status_pc);
             $PackageFeature->title = $request->name;
             $PackageFeature->features_type = $request->cvaccess_status;
             $PackageFeature->status = ($request->status_pc) ? '1' : '0';
             
             $PackageFeature->save();

             return response()->json(['message' => $message,'url'=> route('superadmin.package.package_feature'),'status'=>'success']);
             
            //  return view('super-admin.packages.add_edit_package_feature', $this->data);

        }

        if($id > 0){
            $this->Packagefeature = PackageFeature::findOrfail($id);
            $this->update= true;
        }else{
            $this->Packagefeature = array(); 
            $this->update= false;
        }
        
        
        return view('super-admin.packages.add_edit_package_feature', $this->data);

    }
    public function create()
    {
        $this->package_features_operations = PackageFeature::where('features_type', '=', '1')->get();
        $this->package_features_salse = PackageFeature::where('features_type', '=', '2')->get();
        $this->paymentSetting = PaymentSetting::first();
        return view('super-admin.packages.create', $this->data);
    }
    public function store(StorePackage $request)
    {
        // dd($request->all());
        DB::beginTransaction();

        try {
        $package = new Package();
        $package->name = $request->name;
        $package->description = $request->description;
        $package->monthly_price = $request->monthly_price; //unuse fields
        $package->annual_price = $request->annual_price; //unuse fields
        $package->stripe_monthly_plan_id = $request->stripe_monthly_plan_id; //unuse fields
        $package->stripe_annual_plan_id = $request->stripe_annual_plan_id; //unuse fields
        $package->razorpay_annual_plan_id = $request->razorpay_annual_plan_id; //unuse fields
        $package->razorpay_monthly_plan_id = $request->razorpay_monthly_plan_id; //unuse fields

        if ($request->discount != '') {
            $package->discount = $request->discount;
        }
        if ($request->sub_total != '') {
            $package->sub_total = $request->sub_total;
        }
        if ($request->amount_total_paid != '') {
            $package->amount_total_paid = $request->amount_total_paid;
        }


        //unuse fields
        if ($request->no_of_job_openings != '') {
            $package->no_of_job_openings = $request->no_of_job_openings;
        } else {
            $package->no_of_job_openings = null;
        }

        //unuse fields
        if ($request->no_of_candidate_access != '') {
            $package->no_of_candidate_access = $request->no_of_candidate_access;
        } else {
            $package->no_of_candidate_access = null;
        }

        //unuse fields
        if ($request->career_website) {
            $package->career_website = 1;
        } else {
            $package->career_website = 0;
        }

        //unuse fields
        if ($request->multiple_roles) {
            $package->multiple_roles = 1;
        } else {
            $package->multiple_roles = 0;
        }


        if ($request->recommended) {
            $package->recommended = 1;
        } else {
            $package->recommended = 0;
        }

        if ($request->is_trail) {
            $package->is_trail = 1;
        } else {
            $package->is_trial = 0;
        }

        if ($request->status) {
            $package->status = 1;
        } else {
            $package->status = 0;
        }
        if ($request->custome_pck) {
            $package->custome_pck = 1;
        } else {
            $package->custome_pck = 0;
        }

        $package->save();
        if (count($request->id) > 0) {

            for ($i = 0; $i < count($request->id); $i++) {
                $packagedetail = new PackageDetail();
                $packagedetail->package_id = $package->id;
                $packagedetail->package_feature_id = $request->id[$i];
                $packagedetail->quantity = ($request->quantity[$i]) ? $request->quantity[$i] : 0;
                $packagedetail->price = ($request->price[$i]) ? $request->price[$i] : 0;
                $packagedetail->sub_total = ($request->finalprice[$i]) ? $request->finalprice[$i] : 0;
                $packagedetail->status = $request->status[$i];
                $packagedetail->save();
            }
        }
        DB::commit();
             return Reply::redirect(route('superadmin.packages.index'), __('messages.createdSuccessfully'));
        } catch (\Exception $e) {
            // If an error occurs, rollback the transaction
            DB::rollback();
        }


        
    }
    public function edit($id)
    {
        $this->package_features_operations = PackageFeature::where('features_type', '=', '1')->get();
        $this->package_features_salse = PackageFeature::where('features_type', '=', '2')->get();
        // dd( $this->data);
        $this->package = Package::findOrfail($id);
        // dd($this->package);
        $this->paymentSetting = PaymentSetting::first();
        return view('super-admin.packages.edit', $this->data);
    }
    public function update(StorePackage $request, $id)
    {
        
        // dd($request->all());
        DB::beginTransaction();

        try {
            $package = Package::findOrFail($id);
            $package->name = $request->name;
            $package->description = $request->description;
            $package->monthly_price = 0;//$request->monthly_price; unuse fields
            $package->annual_price = $request->annual_price; //unuse fields
            $package->stripe_monthly_plan_id = $request->stripe_monthly_plan_id; //unuse fields
            $package->stripe_annual_plan_id = $request->stripe_annual_plan_id; //unuse fields
            $package->razorpay_annual_plan_id = $request->razorpay_annual_plan_id; //unuse fields
            $package->razorpay_monthly_plan_id = $request->razorpay_monthly_plan_id; //unuse fields

            if ($request->discount != '') {
                $package->discount = $request->discount;
            }
            if ($request->sub_total != '') {
                $package->sub_total = $request->sub_total;
            }
            if ($request->amount_total_paid != '') {
                $package->amount_total_paid = $request->amount_total_paid;
            }


            //unuse fields
            if ($request->no_of_job_openings != '') {
                $package->no_of_job_openings = $request->no_of_job_openings;
            } else {
                $package->no_of_job_openings = null;
            }

            //unuse fields
            if ($request->no_of_candidate_access != '') {
                $package->no_of_candidate_access = $request->no_of_candidate_access;
            } else {
                $package->no_of_candidate_access = null;
            }

            if ($request->trial_duration != '') {
                $package->trial_duration = $request->trial_duration;
            }

            //unuse fields
            if ($request->career_website) {
                $package->career_website = 1;
            } else {
                $package->career_website = 0;
            }

            //unuse fields
            if ($request->multiple_roles) {
                $package->multiple_roles = 1;
            } else {
                $package->multiple_roles = 0;
            }
            
            
            if ($request->recommended) {
                $package->recommended = 1;
            } else {
                $package->recommended = 0;
            }

            // dd($request->is_trail);
            if ($request->is_trail) {
                $package->is_trial = 1;
            } else {
                $package->is_trial = 0;
            }

            if ($request->status_pc) {
                $package->status = 1;
            } else {
                $package->status = 0;
            }
            if ($request->custome_pck) {
                $package->custome_pck = 1;
            } else {
                $package->custome_pck = 0;
            }

            $package->save();

            PackageDetail::where('package_id', $package->id)->delete();
            if (count($request->id) > 0) {

                for ($i = 0; $i < count($request->id); $i++) {
                    $packagedetail = new PackageDetail();
                    $packagedetail->package_id = $package->id;
                    $packagedetail->package_feature_id = $request->id[$i];
                    $packagedetail->quantity = ($request->quantity[$i]) ? $request->quantity[$i] : 0;
                    $packagedetail->price = ($request->price[$i]) ? $request->price[$i] : 0;
                    $packagedetail->sub_total = ($request->finalprice[$i]) ? $request->finalprice[$i] : 0;
                    $packagedetail->status = $request->status[$i];
                    $packagedetail->save();
                }
            }


            DB::commit();
            return Reply::redirect(route('superadmin.packages.index'), __('messages.updatedSuccessfully'));
        } catch (\Exception $e) {
            // If an error occurs, rollback the transaction
            DB::rollback();
        }



       
    }
    public function destroy($id)
    {
        Package::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }


}
