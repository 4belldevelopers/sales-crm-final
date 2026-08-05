<?php

namespace App\Services\Apptracker;

use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

/**
 * TicketService — Phase C of the Step-16 Apptracker controller slice.
 *
 * Owns ticket display and tenant-scoped comment operations (~230 LoC extracted
 * from ServiceCompanyAppTrackerController: ticketshow, storetComment, updateStatus).
 *
 * All queries include company_id; raw DB:: calls are NOT auto-scoped by Eloquent.
 */
class TicketService
{
    /**
     * Load a ticket with relations, enforcing tenant ownership.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException  404 on wrong tenant.
     */
    public function show(int $ticketId, int $companyId): Ticket
    {
        return Ticket::where('id', $ticketId)
            ->where('company_id', $companyId)
            ->with(['comments.user', 'ticketstatus', 'user'])
            ->firstOrFail();
    }

    /**
     * Store a comment on a ticket, enforcing tenant ownership.
     *
     * $payload keys: user_id (int), content (string), status_id (int|null)
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException  403 if cross-tenant.
     */
    public function addComment(int $ticketId, int $companyId, array $payload): Comment
    {
        abort_if(
            !Ticket::where('id', $ticketId)->where('company_id', $companyId)->exists(),
            403
        );

        $comment = Comment::create([
            'ticket_id' => $ticketId,
            'user_id'   => $payload['user_id'],
            'content'   => $payload['content'],
        ]);

        if (!empty($payload['status_id'])) {
            Ticket::where('id', $ticketId)->update(['status_id' => $payload['status_id']]);
        }

        return $comment;
    }

    /**
     * Update the status of a ticket, enforcing tenant ownership.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException  404 on wrong tenant.
     */
    public function updateStatus(int $ticketId, int $companyId, int $statusId): Ticket
    {
        $ticket = Ticket::where('id', $ticketId)
            ->where('company_id', $companyId)
            ->firstOrFail();

        $ticket->update(['status_id' => $statusId]);

        return $ticket;
    }
}
