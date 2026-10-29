<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\UpdateSuperAdminProfileRequest;
use App\Models\SmsSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

/**
 * SuperAdminProfileController — Root Administrator Profile & Security Hub.
 *
 * Intent: Manages the master identity, contact details, authentication credentials,
 * security password rotation with current-password verification, avatar photo uploads
 * with automated orphan file purging, and phone verification states for the platform's
 * root Super Administrator.
 *
 * Tenancy: Master Super Admin singleton scope (unrestricted system clearance).
 */
class SuperAdminProfileController extends SuperAdminBaseController
{
    /**
     * SuperAdminProfileController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.myProfile';
        $this->pageIcon = 'ti-user';
        $this->smsSettings = SmsSetting::first();
    }

    /**
     * Display the redesigned Super Admin Profile Hub.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $this->calling_codes = $this->getCallingCodes();
        $this->user = auth()->user();
        $this->smsSettings = $this->smsSettings ?? SmsSetting::first();

        return view('super-admin.profile.index', $this->data);
    }

    /**
     * Defensive alias for resource show route.
     *
     * @param int|string|null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($id = null)
    {
        return redirect()->route('superadmin.profile.index');
    }

    /**
     * Defensive alias for resource edit route.
     *
     * @param int|string|null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit($id = null)
    {
        return redirect()->route('superadmin.profile.index');
    }

    /**
     * Update root Super Admin profile credentials and contact channels.
     *
     * @param UpdateSuperAdminProfileRequest $request
     * @return array
     */
    public function update(UpdateSuperAdminProfileRequest $request)
    {
        /** @var User $user */
        $user = User::findOrFail(auth()->id());

        // ── 1. Password Verification & Rotation ──────────────────────────────────
        if ($request->filled('password')) {
            if (! $request->filled('current_password') || ! Hash::check($request->current_password, $user->password)) {
                return Reply::error(__('The provided current password does not match our records.'), 'current_password');
            }
            $user->password = Hash::make($request->password);
        }

        // ── 2. Core Identity Attributes ──────────────────────────────────────────
        $user->name = $request->name;
        $user->email = $request->email;

        // ── 3. Direct Mobile & Calling Code (Handle state transition) ─────────────
        if ($request->has('mobile')) {
            $newMobile = $request->filled('mobile') ? trim((string) $request->mobile) : null;
            $newCallingCode = $request->filled('calling_code') ? trim((string) $request->calling_code) : null;

            if ($user->mobile !== $newMobile || $user->calling_code !== $newCallingCode) {
                $user->mobile_verified = 0;
            }

            $user->mobile = $newMobile;
            $user->calling_code = $newCallingCode;
        }

        // ── 4. Communications & Social Links ─────────────────────────────────────
        if ($request->has('whatsapp_number')) {
            $user->whatsapp_number = $request->filled('whatsapp_number') ? trim((string) $request->whatsapp_number) : null;
        }

        if ($request->has('linkedin')) {
            $user->linkedin = $request->filled('linkedin') ? trim((string) $request->linkedin) : null;
        }

        // ── 5. Profile Picture (Avatar) Lifecycle Management ──────────────────────
        if ($request->boolean('remove_image')) {
            if ($user->image) {
                $this->removeOldAvatar($user->image);
                $user->image = null;
            }
        } elseif ($request->hasFile('image')) {
            if ($user->image) {
                $this->removeOldAvatar($user->image);
            }
            $user->image = Files::upload($request->image, 'profile');
        }

        $user->save();

        // ── 6. Multi-Tenant Cache Invalidation ───────────────────────────────────
        Cache::forget('user_' . $user->id);
        Cache::forget('superadmin_user_' . $user->id);

        // ── 7. Structured JSON Response for Client-Side DOM Sync ─────────────────
        return Reply::successWithData(__('menu.myProfile') . ' ' . __('messages.updatedSuccessfully'), [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'calling_code' => $user->calling_code,
                'mobile' => $user->mobile,
                'mobile_verified' => (int) $user->mobile_verified,
                'whatsapp_number' => $user->whatsapp_number,
                'linkedin' => $user->linkedin,
                'image' => $user->image,
                'profile_image_url' => $user->profile_image_url,
            ],
            'action' => 'none',
        ]);
    }

    /**
     * Safely purge old avatar from disk and storage.
     *
     * @param string|null $image
     * @return void
     */
    protected function removeOldAvatar(?string $image): void
    {
        if (! $image) {
            return;
        }

        try {
            Files::deleteFile($image, 'profile');
            $localPath = public_path('user-uploads/profile/' . $image);
            if (File::exists($localPath)) {
                File::delete($localPath);
            }
        } catch (\Throwable $e) {
            // Gracefully ignore missing file exceptions
        }
    }
}
