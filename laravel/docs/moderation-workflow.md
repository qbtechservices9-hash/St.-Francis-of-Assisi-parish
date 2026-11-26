# Moderation Workflow (Events & Gallery)

## Roles

- `super_admin`
- `content_admin`
- `ministry_admin`

## Submission flow

1. `ministry_admin` creates or edits event/gallery content.
2. Content is set to `Pending` and hidden from public pages.
3. Reviewers (`super_admin` and eligible `content_admin`) receive notification.
4. Reviewer approves or rejects.
5. Submitter receives decision notification.

## Approval statuses

- `Draft`: Not submitted yet.
- `Pending`: Waiting for review.
- `Approved`: Accepted and publishable.
- `Rejected`: Returned with note.

## Reviewer scope setting

Admin path: `Site Settings -> Moderation`

- **Allow global content admins to review all ministries**
  - `ON`: content admins without ministry assignment can review all ministry submissions.
  - `OFF`: content admins must be assigned to a ministry to review its submissions.

## Moderation screens

- `admin/events`
- `admin/gallery-items`

Both screens include:

- default role-aware moderation filters
- single-item approve/reject/submit actions
- bulk approve/reject actions for reviewers
- moderation metadata columns (submitted/reviewed info)

## Notifications

- In-app notifications: bell icon + `admin/notifications`
- Mail notifications: sent when mail is configured

## Audit log

- Path: `admin/audit-logs` (super_admin only)
- Captures create/update/delete activity for key content models.
