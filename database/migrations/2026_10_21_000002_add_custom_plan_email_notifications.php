<?php

use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration adding the two Custom Subscription Plan email notification templates
 * to MySQL (email_notifications, mail_permissions) and indexing them in Elasticsearch
 * (ELASTIC_EMAIL_NOTIFICATIONS, ELASTIC_EMAIL_PERMISSIONS).
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('email_notifications')) {
            return;
        }

        $templates = [
            [
                'purpose' => 'Custom Subscription Plan Enquiry (Admin Notification)',
                'subject' => 'New Custom Subscription Plan Enquiry – JERO.works',
                'from_email_id' => 'noreply@jero.live',
                'mailType' => 2, // Sales Notification
                'status' => 1,
                'roles' => ['Administrator', 'Default', 'Sales'],
                'body_description' => '<p>Dear Admin Team,</p>

<p>A new enquiry for a <strong>Custom Subscription Plan</strong> has been submitted through the JERO.works platform.</p>

<p>The customer is interested in exploring a subscription package tailored to their business requirements.</p>

<p><strong>Enquiry Details</strong></p>
<ul>
    <li><strong>Name:</strong> {{customer_name}}</li>
    <li><strong>Email:</strong> {{customer_email}}</li>
    <li><strong>Company:</strong> {{company_name}}</li>
    <li><strong>Enquiry Type:</strong> Custom Subscription Plan</li>
    <li><strong>Submitted On:</strong> {{submitted_at}}</li>
</ul>

<p>Please contact the customer to understand their requirements and recommend the most suitable plan, pricing, and coin package.</p>

<p>We recommend following up promptly to provide a seamless customer experience.</p>

<p>Best regards,<br>
<strong>JERO.works System Notification</strong><br>
JERO.works Technology</p>',
            ],
            [
                'purpose' => 'Custom Subscription Plan Confirmation (Customer)',
                'subject' => "We've Received Your Custom Plan Enquiry | JERO.works",
                'from_email_id' => 'noreply@jero.live',
                'mailType' => 2, // Sales Notification
                'status' => 1,
                'roles' => ['Default', 'Administrator'],
                'body_description' => '<p>Dear {{customer_name}},</p>

<p>Thank you for your interest in a <strong>Custom Subscription Plan with JERO.works</strong>.</p>

<p>We\'ve received your enquiry, and our team will review your business requirements to help you find a plan that best fits your needs.</p>

<p>Whether you\'re looking for customized coin packages, volume discounts, or dedicated support, we\'re here to help you find the right solution.</p>

<p><strong>What happens next?</strong></p>
<ul>
    <li>Our team will review your enquiry.</li>
    <li>A representative will contact you to understand your requirements.</li>
    <li>We\'ll discuss suitable package options, pricing, and available benefits.</li>
</ul>

<p>Thank you for choosing JERO.works. We look forward to supporting your business and helping you get the most out of our platform.</p>

<p>Best regards,<br>
<strong>JERO.works Sales Team</strong><br>
JERO.works Technology</p>',
            ],
        ];

        $elasticService = null;
        try {
            $elasticService = app(ElasticService::class);
        } catch (\Throwable $e) {
            // Elastic service not available in isolated test environments
        }

        foreach ($templates as $tmpl) {
            $existing = DB::table('email_notifications')->where('purpose', $tmpl['purpose'])->first();
            $now = Carbon::now();

            if ($existing) {
                $id = $existing->id;
                DB::table('email_notifications')->where('id', $id)->update([
                    'subject' => $tmpl['subject'],
                    'from_email_id' => $tmpl['from_email_id'],
                    'body_description' => $tmpl['body_description'],
                    'status' => $tmpl['status'],
                    'mailType' => $tmpl['mailType'],
                    'updated_at' => $now,
                ]);
            } else {
                $id = DB::table('email_notifications')->insertGetId([
                    'purpose' => $tmpl['purpose'],
                    'subject' => $tmpl['subject'],
                    'from_email_id' => $tmpl['from_email_id'],
                    'body_description' => $tmpl['body_description'],
                    'status' => $tmpl['status'],
                    'mailType' => $tmpl['mailType'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // Sync to Elasticsearch
            if ($elasticService) {
                try {
                    $elasticService->getClient()->update([
                        'index' => defined('ELASTIC_EMAIL_NOTIFICATIONS') ? ELASTIC_EMAIL_NOTIFICATIONS : config('constants.ELASTIC_EMAIL_NOTIFICATIONS', 'local_email_notifications'),
                        'id' => (string) $id,
                        'retry_on_conflict' => 5,
                        'body' => [
                            'doc' => [
                                'id' => (int) $id,
                                'purpose' => $tmpl['purpose'],
                                'subject' => $tmpl['subject'],
                                'from_email_id' => $tmpl['from_email_id'],
                                'body_description' => $tmpl['body_description'],
                                'status' => (int) $tmpl['status'],
                                'mailType' => (int) $tmpl['mailType'],
                                'created_at' => $now->toIso8601String(),
                                'updated_at' => $now->toIso8601String(),
                            ],
                            'doc_as_upsert' => true,
                            'detect_noop' => false,
                        ],
                    ]);
                } catch (\Throwable $e) {
                    // Ignore Elastic error in CLI migration if offline
                }
            }

            // Permissions
            if (Schema::hasTable('mail_permissions')) {
                DB::table('mail_permissions')->where('notification_id', $id)->delete();
                foreach ($tmpl['roles'] as $roleName) {
                    $rowId = DB::table('mail_permissions')->insertGetId([
                        'role_name' => $roleName,
                        'notification_id' => $id,
                    ]);

                    if ($elasticService) {
                        try {
                            $elasticService->getClient()->update([
                                'index' => defined('ELASTIC_EMAIL_PERMISSIONS') ? ELASTIC_EMAIL_PERMISSIONS : config('constants.ELASTIC_EMAIL_PERMISSIONS', 'local_email_permissions'),
                                'id' => (string) $rowId,
                                'retry_on_conflict' => 5,
                                'body' => [
                                    'doc' => [
                                        'id' => (int) $rowId,
                                        'role_name' => $roleName,
                                        'notification_id' => (int) $id,
                                    ],
                                    'doc_as_upsert' => true,
                                    'detect_noop' => false,
                                ],
                            ]);
                        } catch (\Throwable $e) {
                            // Ignore Elastic error
                        }
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('email_notifications')) {
            return;
        }

        $purposes = [
            'Custom Subscription Plan Enquiry (Admin Notification)',
            'Custom Subscription Plan Confirmation (Customer)',
        ];

        $ids = DB::table('email_notifications')->whereIn('purpose', $purposes)->pluck('id')->toArray();

        DB::table('email_notifications')->whereIn('id', $ids)->delete();
        if (Schema::hasTable('mail_permissions')) {
            DB::table('mail_permissions')->whereIn('notification_id', $ids)->delete();
        }

        try {
            $elasticService = app(ElasticService::class);
            foreach ($ids as $id) {
                $elasticService->getClient()->delete([
                    'index' => defined('ELASTIC_EMAIL_NOTIFICATIONS') ? ELASTIC_EMAIL_NOTIFICATIONS : config('constants.ELASTIC_EMAIL_NOTIFICATIONS', 'local_email_notifications'),
                    'id' => (string) $id,
                ]);
            }
        } catch (\Throwable $e) {
            // Ignore
        }
    }
};
