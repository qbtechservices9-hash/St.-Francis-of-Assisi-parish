<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Global Content Admin Reviewer
    |--------------------------------------------------------------------------
    |
    | When true, content admins with no assigned ministries can review
    | submissions from any ministry. When false, they only receive ministry-
    | specific submissions if assigned to that ministry.
    |
    */
    'allow_global_content_admin_reviewer' => env('APPROVAL_GLOBAL_CONTENT_ADMIN_REVIEWER', true),
];
