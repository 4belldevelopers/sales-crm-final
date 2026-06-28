<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Company;
use Carbon\Carbon;
use App\Services\MailService;
use App\Helper\GeneralMethod;

class NotifyFeaturedEnd extends Command
{
    protected $mailService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:featured-end';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notifications to companies whose featured status has expired';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MailService $mailService)
    {
        parent::__construct();
        $this->mailService = $mailService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        
        $today = Carbon::today();
        $companies = Company::all();
                            // dd(Carbon::now()->addDay()->format('Y-m-d'));
            $basecompany = GeneralMethod::baseCompany();

            $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
            $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
            $attachments = array();
            $subject = "Notification sent to";
            $cc = [];
            $data = [
                'message' => "Notification sent to"
            ];
        foreach ($companies as $company) {
            $to_Mail_Id = $company->company_email;
            //  $result = $this->mailService->sendEmail($to_Mail_Id, $subject,'email.common.index', (object) $data, $attachments, $fromAddress, $senderName,$cc);
        }

        // return 0;
    }
}
