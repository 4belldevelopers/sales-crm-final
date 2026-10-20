<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SaasFront\SaasFrontBaseController;
use App\Models\FrontCmsHeader;
use App\Models\ThemeSetting;
// use Illuminate\Foundation\Auth\RegistersUsers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends SaasFrontBaseController
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    // use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->pageTitle = 'Meecruit | Register';
        $this->pageIcon = 'icon-grid';

        $this->middleware('guest')->except('logout');
    }

    public function showRegistrationForm()
    {
        if (auth()->check()) {
            return app(\App\Actions\Auth\ShowLoginFormAction::class)->redirectIfAuthenticated() ?? redirect('/service_company/dashboard');
        }

        $this->setting = $this->global;
        $this->headerData = FrontCmsHeader::first();
        $this->frontTheme = $this->adminTheme;

        return view('auth.login', array_merge($this->data, [
            'initialMode' => 'signup',
            'setting' => $this->global,
            'global' => $this->global,
            'frontTheme' => ThemeSetting::whereNull('company_id')->first(),
            'headerData' => FrontCmsHeader::first(),
            'adminTheme' => $this->adminTheme,
        ]));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
