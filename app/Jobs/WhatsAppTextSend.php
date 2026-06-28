<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Services\ElasticService;
use Carbon\Carbon;
use App\Models\JobApplication;

class WhatsAppTextSend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone;
    protected $message;
    protected $instance_name;
    protected $token;
    protected $baseUrl;
    protected $company_id;
    protected $type;
    


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($phone, $message,$instance_name,$token,$baseUrl,$company_id,$type)
    {
        $this->phone = $phone;
        $this->message = $message;
        $this->instance_name = $instance_name;
        $this->token = $token;
        $this->baseUrl = $baseUrl;
        $this->company_id = $company_id;
        $this->type = $type;

        if (empty($this->uuid)) {
            $this->uuid = (string) Str::uuid();
        }

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        $response = Http::withHeaders([
            'apikey' =>  $this->token
        ])->post(
            $this->baseUrl."/message/sendText/". $this->instance_name,
        [
            "number" => $this->phone,
            "text"   => $this->message
        ]);
        $result = $response->json();
        if ($response->successful()) {
            // ✅ SUCCESS
            
            $created_at = now();
            $inserted = DB::table('whatsapp_count_companies')->insertGetId([
                'phone'        => $this->phone,
                'messageBody'  => $this->message,
                'messageType'  => $this->type,
                'company_id'   => $this->company_id,
                'instance_name'   => $this->instance_name,
                'created_at'   => $created_at,
                'updated_at'   => $created_at
            ]);
            $elasticService = app(ElasticService::class);
            $response = $elasticService->getClient()->update([
                'index' => ELASTIC_WHATSAPP_COUNT,
                'id'    => $inserted ,
                'retry_on_conflict' => 5,
                'body'  => [
                    'doc' => [ 
                        'id' =>  $inserted, 
                        'phone' => $this->phone,
                        'messageBody' => $this->message,
                        'messageType' => $this->type,
                        'company_id' => $this->company_id,
                        'created_at' => ($created_at) ? Carbon::parse($created_at)->toIso8601String() : null,
                        'updated_at' => ($created_at) ? Carbon::parse($created_at)->toIso8601String() : null
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false
                ],
            ]);

        }

        return $result;

        
    }
}
