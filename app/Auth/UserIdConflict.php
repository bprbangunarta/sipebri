<?php

namespace App\Auth;

use RuntimeException;

/** The person Codex names is already stored here under another id and has records that cannot move with it. */
class UserIdConflict extends RuntimeException {}
