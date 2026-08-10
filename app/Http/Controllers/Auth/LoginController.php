<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\AttemptLoginAction;
use App\Actions\Auth\LogoutAction;
use App\Actions\Auth\ShowLoginFormAction;
use App\Http\Controllers\Controller;
use App\Models\FrontCmsHeader;
use App\Models\ThemeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/**
 * Thin auth controller — delegates to Action classes.
 * Phase 2 of V2_login plan (docs/V2_login/IMPLEMENTATION_PLAN.md §2.1).
 */
class LoginController extends Controller
{
    protected $redirectTo = '/admin/dashboard';

    public function __construct(
        private AttemptLoginAction  $attemptLogin,
        private ShowLoginFormAction $showLoginFormAction,
        private LogoutAction        $logoutAction,
    ) {
        parent::__construct();
        $this->middleware('guest')->except('logout');
        App::setLocale($this->global->locale);
    }

    public function showLoginForm()
    {
        if ($redirect = $this->showLoginFormAction->redirectIfAuthenticated()) {
            return $redirect;
        }

        return view('auth.login', [
            'setting'    => $this->global,
            'global'     => $this->global,
            'frontTheme' => ThemeSetting::whereNull('company_id')->first(),
            'headerData' => FrontCmsHeader::first(),
            'url'        => request('url'),
            'adminTheme' => $this->adminTheme,
        ]);
    }

    public function superLoginForms()
    {
        if ($redirect = $this->showLoginFormAction->redirectIfAuthenticated()) {
            return $redirect;
        }

        return view('auth.superadmin-login', [
            'setting'    => $this->global,
            'global'     => $this->global,
            'frontTheme' => ThemeSetting::whereNull('company_id')->first(),
            'headerData' => FrontCmsHeader::first(),
            'adminTheme' => $this->adminTheme,
        ]);
    }

    public function login(Request $request)
    {
        return $this->attemptLogin->execute($request);
    }

    public function logout(Request $request)
    {
        return $this->logoutAction->execute($request);
    }
}
