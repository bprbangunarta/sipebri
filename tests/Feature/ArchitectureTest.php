<?php

use App\Audit\Auditable;
use App\Http\Controllers\Controller;
use App\Models\AppNotification;

/*
| Project rules that used to live only in documents. When one of these fails, either fix the code or
| (if the rule itself changed) change it here and in CLAUDE.md / .ai/rules in the same commit.
*/

arch('no debugging leftovers')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'die', 'exit'])
    ->not->toBeUsed();

arch('configuration is read through config(), never env(), outside config files')
    ->expect('env')->not->toBeUsed();

arch('every business model is audited')
    ->expect('App\Models')
    ->toUseTrait(Auditable::class)
    ->ignoring([AppNotification::class]);

arch('controllers are suffixed and extend the base controller')
    ->expect('App\Http\Controllers')
    ->toHaveSuffix('Controller')
    ->toExtend(Controller::class)
    ->ignoring(Controller::class);

arch('enums live in App\Enums and are enums')
    ->expect('App\Enums')->toBeEnums();

arch('the audit trail is only written through the Audit service')
    ->expect('App\Audit\AuditLog')
    ->toOnlyBeUsedIn(['App\Audit', 'App\Http\Controllers\AuditLogController']);

arch('models do not reach into HTTP')
    ->expect('App\Models')
    ->not->toUse(['Illuminate\Http\Request', 'Illuminate\Support\Facades\Request']);
