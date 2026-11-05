<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="referrer" content="no-referrer">
    <title>Partnership Invitation | JERO</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1.5rem;
        }
        .partner-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            max-width: 620px;
            width: 100%;
            overflow: hidden;
        }
        .partner-header {
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
            padding: 1.75rem 2rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }
        .partner-body {
            padding: 2rem;
        }
        .company-avatar {
            width: 56px;
            height: 56px;
            border-radius: 10px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.35rem;
            color: #009ef7;
        }
        .badge-active {
            background-color: #e8fff3;
            color: #50cd89;
            font-weight: 600;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.8rem;
        }
        .badge-warning {
            background-color: #fff8dd;
            color: #f1416c;
            font-weight: 600;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.8rem;
        }
        .badge-expired {
            background-color: #f1f5f9;
            color: #64748b;
            font-weight: 600;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.8rem;
        }
        .info-subcard {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }
        .btn-primary-blue {
            background-color: #009ef7;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-primary-blue:hover {
            background-color: #0086d4;
            color: #ffffff;
        }
        .btn-secondary-light {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
        }
        .btn-secondary-light:hover {
            background-color: #e2e8f0;
        }
    </style>
</head>
<body>

<div class="partner-card">
    @if(!$invitation)
        <div class="partner-body text-center py-10">
            <div class="mb-4 text-danger">
                <i class="ki-outline ki-cross-circle fs-3x"></i>
            </div>
            <h3 class="fw-bold text-gray-800 mb-2">Invalid or Expired Link</h3>
            <p class="text-muted fs-6 mb-6">This partnership invitation link is no longer valid or has already expired.</p>
            <a href="{{ url('/') }}" class="btn btn-secondary-light">Return to JERO Home</a>
        </div>
    @elseif($isAccepted)
        <div class="partner-header">
            <div class="company-avatar">
                {{ substr($invitation->sender_company_name ?? 'C', 0, 1) }}
            </div>
            <div>
                <h4 class="fw-bold mb-1 text-gray-900">{{ $invitation->sender_company_name }}</h4>
                <span class="badge-active"><i class="ki-outline ki-check-circle me-1"></i> Connected Partner</span>
            </div>
        </div>
        <div class="partner-body text-center py-8">
            <h4 class="fw-bold text-gray-800 mb-2">Partnership Already Active</h4>
            <p class="text-muted fs-6 mb-6">Your agency is already connected as an active staffing partner with {{ $invitation->sender_company_name }}.</p>
            <a href="{{ url('/service/consultancy') }}" class="btn btn-primary-blue">Open Partner Portal</a>
        </div>
    @elseif($isExpired || $isCancelled)
        <div class="partner-header">
            <div class="company-avatar">
                {{ substr($invitation->sender_company_name ?? 'C', 0, 1) }}
            </div>
            <div>
                <h4 class="fw-bold mb-1 text-gray-900">{{ $invitation->sender_company_name }}</h4>
                <span class="badge-expired"><i class="ki-outline ki-time me-1"></i> Invitation Inactive</span>
            </div>
        </div>
        <div class="partner-body text-center py-8">
            <h4 class="fw-bold text-gray-800 mb-2">{{ $isCancelled ? 'Invitation Cancelled' : 'Invitation Expired' }}</h4>
            <p class="text-muted fs-6 mb-6">
                {{ $isCancelled ? 'This invitation was cancelled by the requesting employer.' : 'This partnership invitation has expired. Please contact the employer for a new link.' }}
            </p>
            <a href="{{ url('/') }}" class="btn btn-secondary-light">Return to JERO Home</a>
        </div>
    @elseif($isDeclined)
        <div class="partner-header">
            <div class="company-avatar">
                {{ substr($invitation->sender_company_name ?? 'C', 0, 1) }}
            </div>
            <div>
                <h4 class="fw-bold mb-1 text-gray-900">{{ $invitation->sender_company_name }}</h4>
                <span class="badge-warning"><i class="ki-outline ki-cross-circle me-1"></i> Invitation Declined</span>
            </div>
        </div>
        <div class="partner-body text-center py-8">
            <h4 class="fw-bold text-gray-800 mb-2">Invitation Declined</h4>
            <p class="text-muted fs-6 mb-6">This partnership request was declined. If you would like to connect in the future, a 7-day cooling-off period applies.</p>
            <a href="{{ url('/') }}" class="btn btn-secondary-light">Return to JERO Home</a>
        </div>
    @else
        {{-- PENDING INVITATION: VALID LANDING --}}
        <div class="partner-header">
            <div class="company-avatar">
                {{ substr($invitation->sender_company_name ?? 'C', 0, 1) }}
            </div>
            <div class="flex-grow-1">
                <h4 class="fw-bold mb-1 text-gray-900">{{ $invitation->sender_company_name }}</h4>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted fs-7">Requests to partner with your Agency</span>
                    <span class="badge-active"><i class="ki-outline ki-shield-tick fs-7 me-1"></i> Verified Employer</span>
                </div>
            </div>
        </div>

        <div class="partner-body">
            <div class="info-subcard">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted fs-7 fw-semibold">INVITED RECIPIENT</span>
                    <span class="text-gray-900 fw-bold fs-7">{{ $invitation->to_email }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted fs-7 fw-semibold">OFFER VALID UNTIL</span>
                    <span class="text-danger fw-semibold fs-7">
                        {{ $invitation->expires_at ? \Carbon\Carbon::parse($invitation->expires_at)->format('d M Y, h:i A') : '7 Days' }}
                    </span>
                </div>
            </div>

            <div class="mb-6">
                <h5 class="fw-bold text-gray-900 mb-2">What happens when you accept?</h5>
                <ul class="text-muted fs-7 ps-4 mb-0" style="line-height: 1.7;">
                    <li>Establish a direct B2B partnership connection with {{ $invitation->sender_company_name }}.</li>
                    <li>Submit vetted candidates directly to the employer's requisitions and job pipelines.</li>
                    <li>Zero subscription fees: Partnership collaboration is built directly into JERO.</li>
                </ul>
            </div>

            @if(auth()->check())
                <div class="d-flex align-items-center justify-content-between p-3 bg-light-primary rounded mb-6 border border-primary border-dashed">
                    <div class="d-flex align-items-center gap-3">
                        <i class="ki-outline ki-user fs-2 text-primary"></i>
                        <div>
                            <div class="fs-7 fw-bold text-gray-900">{{ auth()->user()->name }}</div>
                            <div class="fs-8 text-muted">Signed in as {{ auth()->user()->email }} ({{ $authCompany->company_name ?? 'Agency' }})</div>
                        </div>
                    </div>
                    <span class="badge badge-light-success fs-8">Authenticated</span>
                </div>

                <div class="d-flex gap-3">
                    <button type="button" id="btn-accept" class="btn-primary-blue flex-grow-1" onclick="handleAccept()">
                        <i class="ki-outline ki-check-circle me-1"></i> Accept Partnership
                    </button>
                    <button type="button" id="btn-decline" class="btn-secondary-light" onclick="handleDecline()">
                        Decline
                    </button>
                </div>
            @else
                <div class="alert alert-dismissible bg-light-warning border border-warning border-dashed d-flex flex-column p-4 mb-6">
                    <div class="d-flex align-items-center mb-1">
                        <i class="ki-outline ki-information-5 fs-2 text-warning me-2"></i>
                        <span class="fw-bold text-gray-900 fs-7">Authentication Required</span>
                    </div>
                    <span class="text-muted fs-8">To bind this partnership to your organization, please sign in or register with your agency account.</span>
                </div>

                <div class="d-flex flex-column gap-2">
                    <a href="{{ url('/login?return_to=' . urlencode(request()->fullUrl())) }}" class="btn-primary-blue text-center">
                        <i class="ki-outline ki-entrance-left me-1"></i> Sign In to Accept
                    </a>
                    <a href="{{ url('/register?return_to=' . urlencode(request()->fullUrl())) }}" class="btn-secondary-light text-center">
                        Register New Agency Account
                    </a>
                </div>
            @endif

            <div id="action-feedback" class="mt-4" style="display: none;"></div>
        </div>
    @endif
</div>

<script>
    const RAW_TOKEN = @json($rawToken);
    const CSRF_TOKEN = '{{ csrf_token() }}';

    function handleAccept() {
        const btn = document.getElementById('btn-accept');
        const feedback = document.getElementById('action-feedback');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Establishing Partnership...';
        feedback.style.display = 'none';

        fetch('{{ url("/partner-invite/accept") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'X-Idempotency-Key': 'idemp_ui_' + RAW_TOKEN.substring(0, 16) + '_' + Date.now()
            },
            body: JSON.stringify({ raw_token: RAW_TOKEN })
        })
        .then(res => res.json().then(data => ({ status: res.status, data })))
        .then(({ status, data }) => {
            if (status === 200 && data.status === 'success') {
                feedback.className = 'alert alert-success mt-4';
                feedback.innerHTML = '<i class="ki-outline ki-check-circle me-1"></i> ' + data.message + ' Redirecting to portal...';
                feedback.style.display = 'block';
                setTimeout(() => {
                    window.location.href = data.redirect || '{{ url("/service/consultancy") }}';
                }, 1500);
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="ki-outline ki-check-circle me-1"></i> Accept Partnership';
                feedback.className = 'alert alert-danger mt-4';
                feedback.innerHTML = '<i class="ki-outline ki-cross-circle me-1"></i> ' + (data.message || 'Acceptance failed. Please try again.');
                feedback.style.display = 'block';
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ki-outline ki-check-circle me-1"></i> Accept Partnership';
            feedback.className = 'alert alert-danger mt-4';
            feedback.innerHTML = '<i class="ki-outline ki-cross-circle me-1"></i> Network error occurred. Please try again.';
            feedback.style.display = 'block';
        });
    }

    function handleDecline() {
        if (!confirm('Are you sure you want to decline this partnership invitation? A 7-day cooling-off period will apply.')) {
            return;
        }

        const btn = document.getElementById('btn-decline');
        const feedback = document.getElementById('action-feedback');
        btn.disabled = true;
        feedback.style.display = 'none';

        fetch('{{ url("/partner-invite/decline") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({ raw_token: RAW_TOKEN, reason: 'Declined by agency representative' })
        })
        .then(res => res.json())
        .then(data => {
            feedback.className = 'alert alert-warning mt-4';
            feedback.innerHTML = '<i class="ki-outline ki-information-5 me-1"></i> ' + (data.message || 'Invitation declined.');
            feedback.style.display = 'block';
            setTimeout(() => { window.location.reload(); }, 1200);
        })
        .catch(err => {
            btn.disabled = false;
            feedback.className = 'alert alert-danger mt-4';
            feedback.innerHTML = 'Error declining invitation.';
            feedback.style.display = 'block';
        });
    }
</script>

</body>
</html>
