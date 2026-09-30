<?php

return [
    /*
    | Two-factor authentication (MFA). Turn the whole feature on or off with MFA_ENABLED in .env.
    | It is opt-in per person: each user chooses an authenticator app (TOTP) or a code sent to their email
    | on their profile. Only people who did so are challenged after a successful Codex sign-in; inactive
    | people never get that far. When the switch is off nobody is challenged and the profile hides the section.
    */
    'mfa_enabled' => (bool) env('MFA_ENABLED', true),

    // Length of the emailed / authenticator code.
    'code_length' => 6,

    // Minutes an emailed code stays valid.
    'email_code_ttl' => 10,

    // Wrong codes allowed before the code is invalidated / the sign-in attempt is dropped.
    'max_attempts' => 5,

    // Seconds before another email code may be requested.
    'email_resend_seconds' => 60,

    // Minutes the second step of a sign-in may take.
    'challenge_ttl' => 10,

    // Number of single-use recovery codes issued when an authenticator app is enabled.
    'recovery_codes' => 8,

    /*
    | Audit trail retention in years, counted from the moment an entry was written (credit files are kept
    | 5 years). `php artisan audit:prune` removes only older entries and leaves an anchor so the hash chain
    | of what remains still verifies. Nothing is removed unless that command runs.
    */
    'audit_retention_years' => (int) env('AUDIT_RETENTION_YEARS', 5),
];
