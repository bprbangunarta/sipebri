<?php

namespace App\Console\Commands;

use App\Audit\AuditVerifier;
use Illuminate\Console\Command;

class AuditVerify extends Command
{
    protected $signature = 'audit:verify';

    protected $description = 'Check the audit trail hash chain for tampering';

    public function handle(AuditVerifier $verifier): int
    {
        $result = $verifier->verify();

        if ($result['ok']) {
            $this->info("Audit trail intact: {$result['checked']} entries verified.");

            return self::SUCCESS;
        }

        $this->error("Audit trail BROKEN at entry #{$result['broken_at']} after {$result['checked']} valid entries: {$result['reason']}");

        return self::FAILURE;
    }
}
