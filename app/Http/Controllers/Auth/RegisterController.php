<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\SaasFront\SaasFrontBaseController;
use App\Models\FrontCmsHeader;
use App\Models\ThemeSetting;
use App\Models\User;
use App\Services\RegistrationActivationService;
use App\Services\RegistrationContextResolver;
use App\Services\TeamInvitationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends SaasFrontBaseController
{
    protected $redirectTo = '/home';

    public function __construct()
    {
        parent::__construct();

        $this->pageTitle = 'Meecruit | Register';
        $this->pageIcon = 'icon-grid';

        $this->middleware('guest')->except(['logout', 'activateTeamMember']);
    }

    public function showRegistrationForm(Request $request)
    {
        $envelopeCookie = null;

        // 1. Process raw secret token if provided in query string (Direct Zero-Latency Resolution)
        if ($request->has('token')) {
            $rawSecret = (string) $request->query('token');
            try {
                $envelopeData = app(TeamInvitationService::class)->resolveTokenAndCreateEnvelope($rawSecret, $request);
                $envelopeCookie = $envelopeData['cookie'] ?? null;
            } catch (\Throwable $e) {
                // If token is invalid or expired, continue to fallback
            }
        }

        // 2. Resolve Dynamic Registration Context (SELF, AGENCY, or TEAM)
        $contextResolver = app(RegistrationContextResolver::class);
        $registrationContext = $contextResolver->resolve($request);

        if (auth()->check() && $registrationContext['context'] !== RegistrationContextResolver::CONTEXT_TEAM) {
            return app(\App\Actions\Auth\ShowLoginFormAction::class)->redirectIfAuthenticated() ?? redirect('/service_company/dashboard');
        }

        $this->setting = $this->global;
        $this->headerData = FrontCmsHeader::first();
        $this->frontTheme = $this->adminTheme;

        $viewResponse = response()->view('auth.login', array_merge($this->data, [
            'initialMode' => ($registrationContext['context'] === RegistrationContextResolver::CONTEXT_TEAM) ? 'team_invite' : 'signup',
            'registrationContext' => $registrationContext,
            'setting' => $this->global,
            'global' => $this->global,
            'frontTheme' => ThemeSetting::whereNull('company_id')->first(),
            'headerData' => FrontCmsHeader::first(),
            'adminTheme' => $this->adminTheme,
        ]));

        if ($envelopeCookie) {
            $viewResponse->withCookie($envelopeCookie);
        }

        return $viewResponse;
    }

    /**
     * Activates an invited team member account (Boundary 3).
     */
    public function activateTeamMember(Request $request): JsonResponse
    {
        return app(RegistrationActivationService::class)->activate($request);
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
     * @return \App\Models\User
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
