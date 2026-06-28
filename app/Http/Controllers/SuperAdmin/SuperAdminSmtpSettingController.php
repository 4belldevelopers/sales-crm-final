<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Requests\SmtpSetting\UpdateSmtpSetting;
use App\Http\Requests\UpdateProfile;
use App\Models\EmailSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class SuperAdminSmtpSettingController extends SuperAdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.mailSetting';
        $this->pageIcon = 'ti-user';
    }
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        $this->smtpSetting = EmailSetting::whereIn('id',[8,9,10,11,12])->get();
        return view('super-admin.mail-setting.index', $this->data);
    }
    /**
     * @param Request $request
     * @return array
     */
    public function update(UpdateSmtpSetting $request){

         
        // $smtp = EmailSetting::first();

        $data = $request->all();

       
        // dd(count($data['mail_host']));

        for($i = 0; $i <= count($data['mail_host']); $i++){
             if($i <= 1){
             $array =  ($data['mail_host']);
             $keys = array_keys($array);
             $keys  = $keys[$i];
           
                //  dd($keys);

            
                     $updateData =[
                        'mail_host'=>$data['mail_host'][$keys],
                        'mail_port'=>$data['mail_port'][$keys],
                        'mail_username'=>$data['mail_username'][$keys],
                        'mail_password'=>$data['mail_password'][$keys],
                        'mail_encryption'=>$data['mail_encryption'][$keys],
                        'notification_mail_host'=>$data['mail_host'][$keys],
                        'notification_mail_port'=>$data['mail_port'][$keys],
                        'notification_mail_username'=>$data['mail_username'][$keys],
                        'notification_mail_password'=>$data['mail_password'][$keys],
                        'notification_mail_encryption'=>$data['mail_encryption'][$keys],
                        'email_id'=>$data['from_mail'][$keys],
                        'status'=>$data['status'][$keys],
                        'dailyCount'=>$data['daily_used'][$keys],
                        'mail_from_email'=>$data['from_mail'][$keys],
                     ];
            DB::table('smtp_settings')->where('id','=',$keys)->update($updateData);
             }
        }
       

       
            return Reply::success(__('messages.updatedSuccessfully'));

    }
    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        $smtp = EmailSetting::first();
        $response = $smtp->verifySmtp();

        if ($response['success']) {
            Notification::route('mail', \request()->test_email)->notify(new TestEmail());
            return Reply::success('Test mail sent successfully');
        }
        return Reply::error($response['message']);
    }

}
