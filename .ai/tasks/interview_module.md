# Task Memory — Interview Module

## Purpose
Schedule and track interviews (`InterviewSchedule`) with attendees, Zoom links,
comments, and automated reminders.

## Workflows
- Schedule interview → create `InterviewSchedule` (+ `InterviewScheduleEmployee` per
  attendee) → create Zoom meeting → index ES → queue confirmation mail.
- Reschedule/cancel; add schedule comments/meeting comments.
- Automated reminders via scheduled commands → queued reminder mail.
See [DATA_FLOW.md](../../docs/DATA_FLOW.md) §4.

## Business rules (verify before changing)
- Interview is tenant-scoped and tied to a `JobApplication`/candidate.
- Zoom meeting creation uses JWT (`ZoomJWT` trait) — handle token/expiry errors.
- Changes must re-index `ats_interview_tracker_elastic` (Observer).
- Reminder timing logic lives in the reminder commands/service — confirm before edits.

## Common files
- Controllers: `ServiceCompany/InterviewScheduleController.php` 🔴
  (⚠️ ignore `OLD_InterviewScheduleController`, `OLD_2_InterviewScheduleController`)
- Services: `ZoomService.php`, `InterviewRemainderMail.php`, `CalendarService.php`
- Models: `InterviewSchedule`, `InterviewScheduleEmployee`, `MeetingSchedule`,
  `MeetingComment`, `ScheduleComments`, `OpenworkspaceInterviewSchedule`
- Jobs/Commands: `QueueInterviewRemainderMailService`, `InterviewReminderCommand`,
  `SendInterviewReminder`, `SendInterviewReminders` (⚠️ confirm which is the live one)
- Observers: `InterviewScheduleObserver`, `InterviewScheduleEmployeeObserver`,
  `InterviewScheduleCommentObserver`
- Traits: `ZoomJWT`

## Gotchas
- Three reminder commands exist with overlapping names — verify the one wired in
  `routes/console.php` before changing reminders.
- Notifications may also broadcast over Reverb (realtime). **[INFERRED]**
