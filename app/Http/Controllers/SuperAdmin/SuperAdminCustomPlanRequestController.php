<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Jobs\ProcessCustomPlanRequestOutboxEventJob;
use App\Models\CustomPlanRequest;
use App\Models\CustomPlanRequestLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Yajra\DataTables\Facades\DataTables;

/**
 * SuperAdminCustomPlanRequestController
 *
 * Dedicated administration module for managing incoming enterprise custom subscription
 * and custom top-up plan enquiries. Provides summary analytics, DataTables server-side listing,
 * detailed request inspection, validated status workflows, Optimistic Concurrency Control (OCC),
 * transactional outbox event logging, and durable idempotency guarantees.
 *
 * Author: Antigravity AI
 * Date: October 21, 2026
 */
class SuperAdminCustomPlanRequestController extends SuperAdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Custom Plan Requests';
        $this->pageIcon = 'icon-layers';
    }

    /**
     * Display the Custom Plan Requests management dashboard.
     */
    public function index(Request $request)
    {
        $this->summaryCounts = $this->getSummaryCounts();
        $this->allowedStatuses = CustomPlanRequest::ALLOWED_STATUSES;

        return view('super-admin.custom-plan-requests.index', $this->data);
    }

    /**
     * Server-side DataTables JSON endpoint.
     */
    public function data(Request $request)
    {
        $query = CustomPlanRequest::query()
            ->select('custom_plan_requests.*');

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('custom_plan_requests.status', $request->status);
        }

        // Date range filter
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $start = Carbon::parse($request->startDate)->startOfDay();
            $end = Carbon::parse($request->endDate)->endOfDay();
            $query->whereBetween('custom_plan_requests.created_at', [$start, $end]);
        }

        $query->orderBy('custom_plan_requests.id', 'DESC');

        return DataTables::of($query)
            ->setRowId(function ($row) {
                return 'cpr-row-'.$row->id;
            })
            ->setRowAttr([
                'data-id' => function ($row) {
                    return $row->id;
                },
                'data-reference' => function ($row) {
                    return $row->request_reference;
                },
                'data-version' => function ($row) {
                    return (int) ($row->aggregate_version ?? 1);
                },
            ])
            ->editColumn('request_reference', function ($row) {
                return '<span class="badge badge-light-primary fw-bolder fs-7">'.e($row->request_reference).'</span>';
            })
            ->editColumn('customer_name', function ($row) {
                $mobile = $row->customer_mobile ? '<br><small class="text-muted"><i class="fa fa-phone me-1"></i>'.e($row->customer_mobile).'</small>' : '';

                return '<div class="d-flex flex-column"><span class="fw-bolder text-gray-800">'.e($row->customer_name).'</span>'.$mobile.'</div>';
            })
            ->editColumn('customer_email', function ($row) {
                return '<a href="mailto:'.e($row->customer_email).'" class="text-primary text-hover-primary">'.e($row->customer_email).'</a>';
            })
            ->editColumn('company_name', function ($row) {
                $compName = e($row->company_name ?? 'N/A');
                $compId = $row->company_id ? '<span class="text-muted fs-8">(ID: '.$row->company_id.')</span>' : '';

                return '<div class="d-flex flex-column"><span>'.$compName.'</span>'.$compId.'</div>';
            })
            ->editColumn('request_type', function ($row) {
                $pkgTitle = $row->package_title;
                if (empty($pkgTitle) || strcasecmp(trim($pkgTitle), 'free') === 0 || strcasecmp(trim($pkgTitle), 'custom package') === 0) {
                    $pkgTitle = ucfirst($row->package_type ?? 'operational').' Custom Package';
                }
                $pkg = $pkgTitle ? '<br><small class="text-muted">'.e($pkgTitle).'</small>' : '';

                return '<div><span class="badge badge-light-info fs-8">'.e($row->request_type).'</span>'.$pkg.'</div>';
            })
            ->editColumn('status', function ($row) {
                return $this->formatStatusBadge($row->status);
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->format('d M Y, h:i A') : 'N/A';
            })
            ->editColumn('updated_at', function ($row) {
                return $row->updated_at ? $row->updated_at->format('d M Y, h:i A') : 'N/A';
            })
            ->addColumn('action', function ($row) {
                $viewBtn = '<button type="button" class="btn btn-sm btn-icon btn-light-primary btn-view-request me-1" data-id="'.$row->id.'" title="View Details">
                                <i class="fa fa-eye"></i>
                            </button>';

                $statusBtn = '<button type="button" class="btn btn-sm btn-icon btn-light-success btn-update-status" data-id="'.$row->id.'" data-status="'.e($row->status).'" data-reference="'.e($row->request_reference).'" data-version="'.(int) ($row->aggregate_version ?? 1).'" title="Change Status">
                                <i class="fa fa-edit"></i>
                              </button>';

                return '<div class="d-flex justify-content-center">'.$viewBtn.$statusBtn.'</div>';
            })
            ->rawColumns(['request_reference', 'customer_name', 'customer_email', 'company_name', 'request_type', 'status', 'action'])
            ->make(true);
    }

    /**
     * Inspect complete details and transition log for a specific request.
     */
    public function show($id)
    {
        $request = CustomPlanRequest::with([
            'company',
            'user',
            'assignedUser',
            'updatedByUser',
            'logs.updatedByUser',
        ])->findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $request->id,
                    'request_reference' => $request->request_reference,
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_mobile' => $request->customer_mobile ?? 'N/A',
                    'company_name' => $request->company_name ?? 'N/A',
                    'company_id' => $request->company_id,
                    'location' => $request->location ?? 'N/A',
                    'request_type' => $request->request_type,
                    'package_type' => ucfirst($request->package_type ?? 'operational'),
                    'package_title' => $request->package_title ?? 'Custom Tier',
                    'status' => $request->status,
                    'entity_version' => (int) ($request->aggregate_version ?? 1),
                    'status_badge' => $this->formatStatusBadge($request->status),
                    'admin_notes' => $request->admin_notes ?? '',
                    'created_at' => $request->created_at ? $request->created_at->format('d M Y, h:i A') : 'N/A',
                    'updated_at' => $request->updated_at ? $request->updated_at->format('d M Y, h:i A') : 'N/A',
                    'last_status_updated_at' => $request->last_status_updated_at ? $request->last_status_updated_at->format('d M Y, h:i A') : 'N/A',
                    'updated_by_name' => $request->updatedByUser->name ?? 'System',
                    'logs' => $request->logs->map(function ($log) {
                        return [
                            'previous_status' => $log->previous_status ?? 'Created',
                            'new_status' => $log->new_status,
                            'notes' => $log->notes ?? '',
                            'updated_by' => $log->updatedByUser->name ?? 'System',
                            'created_at' => $log->created_at ? $log->created_at->format('d M Y, h:i A') : '',
                        ];
                    }),
                ],
            ]);
        }

        $this->cpr = $request;

        return view('super-admin.custom-plan-requests.show', $this->data);
    }

    /**
     * Update the status and optional admin notes for a request with ACID transaction,
     * Optimistic Concurrency Control (OCC), Durable Idempotency, and Transactional Outbox.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:'.implode(',', CustomPlanRequest::ALLOWED_STATUSES),
            'admin_notes' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
            'current_version' => 'nullable|integer',
            'idempotency_key' => 'nullable|string|max:64',
        ]);

        $newStatus = $request->status;
        $notes = $request->admin_notes ?? $request->notes;
        $idempotencyKey = $request->header('X-Idempotency-Key') ?: $request->input('idempotency_key');

        // Canonical Request Hash for Payload Defense
        $requestPayload = [
            'request_id' => (int) $id,
            'status' => $newStatus,
            'admin_notes' => trim($notes ?? ''),
        ];
        $requestHash = hash('sha256', json_encode($requestPayload));

        // Durable Idempotency Guard (sys_idempotency_keys check)
        if ($idempotencyKey) {
            $existingIdempotency = DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existingIdempotency) {
                // Reject key reuse with conflicting payload
                if ($existingIdempotency->request_hash !== $requestHash) {
                    return response()->json([
                        'status' => 'fail',
                        'message' => 'Idempotency key reuse with modified payload detected.',
                        'error_code' => 'IDEMPOTENCY_KEY_REUSE',
                    ], 409);
                }

                // If already completed, replay authoritative response without re-executing
                if ($existingIdempotency->status === 'COMPLETED' && $existingIdempotency->response_body) {
                    $cachedData = json_decode($existingIdempotency->response_body, true);

                    return response()->json($cachedData, $existingIdempotency->response_status ?? 200)
                        ->header('X-Cache-Lookup', 'HIT-IDEMPOTENT')
                        ->header('X-Idempotency-Key', $idempotencyKey);
                }

                // If currently processing, reject concurrent duplicate request
                if ($existingIdempotency->status === 'PROCESSING') {
                    return response()->json([
                        'status' => 'fail',
                        'message' => 'A concurrent operation is already in progress for this request. Please wait.',
                        'error_code' => 'CONCURRENT_OPERATION_IN_PROGRESS',
                    ], 409);
                }
            }
        }

        $outboxId = null;
        $responsePayload = null;

        try {
            $responsePayload = DB::transaction(function () use ($request, $id, $newStatus, $notes, $idempotencyKey, $requestHash, &$outboxId) {
                // 1. Claim Idempotency Key in PROCESSING state within transaction
                if ($idempotencyKey) {
                    DB::table('sys_idempotency_keys')->insertOrIgnore([
                        'tenant_id' => 0,
                        'idempotency_key' => $idempotencyKey,
                        'request_hash' => $requestHash,
                        'status' => 'PROCESSING',
                        'expires_at' => now()->addMinutes(30),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // 2. Fetch with row lock for update
                $cpr = CustomPlanRequest::lockForUpdate()->findOrFail($id);

                // 3. Optimistic Concurrency Control (OCC) Check
                if ($request->filled('current_version')) {
                    $expectedVersion = (int) $request->current_version;
                    if ((int) ($cpr->aggregate_version ?? 1) !== $expectedVersion) {
                        throw new HttpException(409, 'Conflict: Record version has been modified by another administrator. Please refresh the page.');
                    }
                }

                $previousStatus = $cpr->status;
                $newVersion = ((int) ($cpr->aggregate_version ?? 1)) + 1;

                // 4. Update authoritative model
                $cpr->status = $newStatus;
                if (! empty($notes)) {
                    $cpr->admin_notes = $notes;
                }
                $cpr->updated_by = auth()->id();
                $cpr->aggregate_version = $newVersion;
                $cpr->last_status_updated_at = now();
                $cpr->save();

                // 5. Record audit log entry
                CustomPlanRequestLog::create([
                    'custom_plan_request_id' => $cpr->id,
                    'previous_status' => $previousStatus,
                    'new_status' => $newStatus,
                    'notes' => $notes,
                    'updated_by' => auth()->id(),
                    'created_at' => now(),
                ]);

                // 6. Invalidate cached counts & recompute
                Cache::forget('custom_plan_requests_counts');
                $counts = $this->getSummaryCounts();

                $deltaPayload = [
                    'id' => (int) $cpr->id,
                    'reference' => $cpr->request_reference,
                    'previous_status' => $previousStatus,
                    'new_status' => $newStatus,
                    'entity_version' => $newVersion,
                    'status_badge' => $this->formatStatusBadge($newStatus),
                    'counts' => $counts,
                    'last_updated' => now()->format('d M Y, h:i A'),
                    'admin_notes' => $cpr->admin_notes ?? '',
                    'updated_by' => auth()->user()->name ?? 'Super Admin',
                    'company_id' => $cpr->company_id,
                ];

                // 7. Atomic Transactional Outbox write
                $outboxId = DB::table('sys_outbox_events')->insertGetId([
                    'tenant_id' => $cpr->company_id ?? 0,
                    'idempotency_key' => $idempotencyKey,
                    'aggregate_type' => 'CustomPlanRequest',
                    'aggregate_id' => $cpr->id,
                    'aggregate_version' => $newVersion,
                    'event_type' => 'CustomPlanRequestStatusUpdated',
                    'payload' => json_encode($deltaPayload),
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => now(),
                    'created_at' => now(),
                ]);

                $result = [
                    'status' => 'success',
                    'message' => 'Status updated successfully.',
                    'id' => (int) $cpr->id,
                    'reference' => $cpr->request_reference,
                    'previous_status' => $previousStatus,
                    'new_status' => $newStatus,
                    'entity_version' => $newVersion,
                    'status_badge' => $this->formatStatusBadge($newStatus),
                    'counts' => $counts,
                    'last_updated' => now()->format('d M Y, h:i A'),
                    'admin_notes' => $cpr->admin_notes ?? '',
                    'updated_by' => auth()->user()->name ?? 'Super Admin',
                    'outbox_id' => $outboxId,
                ];

                // 8. Mark Idempotency Key as COMPLETED
                if ($idempotencyKey) {
                    DB::table('sys_idempotency_keys')
                        ->where('tenant_id', 0)
                        ->where('idempotency_key', $idempotencyKey)
                        ->update([
                            'status' => 'COMPLETED',
                            'response_status' => 200,
                            'response_body' => json_encode($result),
                            'completed_at' => now(),
                            'updated_at' => now(),
                        ]);
                }

                return $result;
            });
        } catch (HttpException $e) {
            if ($idempotencyKey) {
                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $idempotencyKey)
                    ->delete();
            }

            return response()->json([
                'status' => 'fail',
                'message' => $e->getMessage(),
                'error_code' => 'CONCURRENCY_CONFLICT',
            ], $e->getStatusCode());
        }

        // 9. Dispatch outbox processing job asynchronously after commit
        if ($outboxId) {
            try {
                ProcessCustomPlanRequestOutboxEventJob::dispatch((int) $outboxId)->afterCommit();
            } catch (\Throwable $dispatchEx) {
                // Job queue failure will be safely retried by ProcessCustomPlanRequestOutboxCommand
            }
        }

        return response()->json($responsePayload, 200)
            ->header('X-Idempotency-Key', $idempotencyKey ?? 'NONE');
    }

    /**
     * Real-time delta synchronization endpoint.
     * Computes fresh summary counts using single-pass aggregate query with Redis caching,
     * tracks the most recently modified request using the idx_cpr_updated_at index,
     * and delivers status change deltas for seamless cross-tab / multi-admin synchronization.
     */
    public function sync(Request $request)
    {
        $counts = $this->getSummaryCounts();
        $latest = CustomPlanRequest::orderBy('updated_at', 'DESC')
            ->select('id', 'request_reference', 'status', 'aggregate_version', 'updated_at')
            ->first();

        return response()->json([
            'status' => 'success',
            'counts' => $counts,
            'latest' => $latest ? [
                'id' => (int) $latest->id,
                'reference' => $latest->request_reference,
                'status' => $latest->status,
                'entity_version' => (int) ($latest->aggregate_version ?? 1),
                'status_badge' => $this->formatStatusBadge($latest->status),
                'updated_at' => $latest->updated_at ? $latest->updated_at->format('d M Y, h:i A') : '',
                'timestamp' => $latest->updated_at ? $latest->updated_at->timestamp : 0,
            ] : null,
            'total' => $counts['total'] ?? 0,
            'server_time' => now()->timestamp,
        ]);
    }

    /**
     * Compute summary counts for the dashboard cards in a single-pass aggregate query.
     * Cached in Redis for 15 seconds; instantly invalidated upon status mutations.
     */
    protected function getSummaryCounts(): array
    {
        return Cache::remember('custom_plan_requests_counts', 15, function () {
            $row = DB::table('custom_plan_requests')
                ->selectRaw("
                    COUNT(id) as total,
                    SUM(CASE WHEN status = 'New' THEN 1 ELSE 0 END) as `new`,
                    SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status = 'Contacted' THEN 1 ELSE 0 END) as contacted,
                    SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved,
                    SUM(CASE WHEN status = 'Closed' THEN 1 ELSE 0 END) as closed
                ")->first();

            return [
                'total' => (int) ($row->total ?? 0),
                'new' => (int) ($row->new ?? 0),
                'in_progress' => (int) ($row->in_progress ?? 0),
                'contacted' => (int) ($row->contacted ?? 0),
                'resolved' => (int) ($row->resolved ?? 0),
                'closed' => (int) ($row->closed ?? 0),
            ];
        });
    }

    /**
     * Helper to render HTML status badge.
     */
    protected function formatStatusBadge(string $status): string
    {
        switch ($status) {
            case CustomPlanRequest::STATUS_NEW:
                return '<span class="badge badge-light-info fw-bolder fs-8 px-3 py-2">New</span>';
            case CustomPlanRequest::STATUS_IN_PROGRESS:
                return '<span class="badge badge-light-warning fw-bolder fs-8 px-3 py-2">In Progress</span>';
            case CustomPlanRequest::STATUS_CONTACTED:
                return '<span class="badge badge-light-primary fw-bolder fs-8 px-3 py-2">Contacted</span>';
            case CustomPlanRequest::STATUS_RESOLVED:
                return '<span class="badge badge-light-success fw-bolder fs-8 px-3 py-2">Resolved</span>';
            case CustomPlanRequest::STATUS_CLOSED:
                return '<span class="badge badge-light-dark fw-bolder fs-8 px-3 py-2">Closed</span>';
            default:
                return '<span class="badge badge-light-secondary fs-8">'.e($status).'</span>';
        }
    }
}
