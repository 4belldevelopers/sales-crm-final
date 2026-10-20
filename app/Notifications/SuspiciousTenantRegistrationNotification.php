<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

/**
 * SuspiciousTenantRegistrationNotification
 *
 * Passive alarm raised to SuperAdmin when a newly registered tenant exhibits
 * risk indicators (e.g., disposable email, suspicious domain, bot patterns).
 * Per God-Level Specification: The tenant is NEVER restricted or blocked;
 * SuperAdmin is notified for manual review and 1-click verification.
 */
class SuspiciousTenantRegistrationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $uniq;

    public int $companyId;

    public string $companyName;

    public string $email;

    public string $phone;

    public int $companyType;

    public int $riskScore;

    public array $riskReasons;

    /**
     * Create a new notification instance.
     */
    public function __construct(int $companyId, string $companyName, string $email, string $phone, int $companyType, int $riskScore, array $riskReasons)
    {
        $this->uniq = (string) Str::uuid();
        $this->companyId = $companyId;
        $this->companyName = $companyName;
        $this->email = $email;
        $this->phone = $phone;
        $this->companyType = $companyType;
        $this->riskScore = $riskScore;
        $this->riskReasons = $riskReasons;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the payload representation of the notification.
     */
    public function dataPayload(): array
    {
        $typeNames = [
            1 => 'Staffing Company',
            2 => 'Service Company',
            3 => 'Product Company',
            4 => 'Consulting / Freelancer',
        ];

        return [
            'uniq' => $this->uniq,
            'title' => 'Suspicious Tenant Registration Detected',
            'message' => "Tenant \"{$this->companyName}\" ({$this->email}) registered with Risk Score {$this->riskScore}/100. Reasons: ".implode('; ', $this->riskReasons),
            'company_id' => $this->companyId,
            'company_name' => $this->companyName,
            'email' => $this->email,
            'phone' => $this->phone,
            'company_type' => $typeNames[$this->companyType] ?? 'Standard',
            'risk_score' => $this->riskScore,
            'risk_reasons' => $this->riskReasons,
            'requires_manual_verification' => true,
            'url' => route('superadmin.company.edit', ['company' => $this->companyId], false) ?: '/super-admin/company',
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return $this->dataPayload();
    }
}
