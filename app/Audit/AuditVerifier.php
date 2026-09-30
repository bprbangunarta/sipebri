<?php

namespace App\Audit;

use Illuminate\Support\Facades\DB;

/**
 * Walks the whole trail in order and checks every link of the hash chain. A changed, removed or inserted
 * row (even edited straight in the database) shows up as the first id where the chain breaks.
 */
class AuditVerifier
{
    /**
     * @return array{ok: bool, checked: int, broken_at: int|null, reason: string|null}
     */
    public function verify(): array
    {
        $previous = null;
        $checked = 0;
        $first = true;

        foreach (DB::table('audit_logs')->orderBy('id')->cursor() as $row) {
            $columns = (array) $row;

            // After pruning, the oldest remaining entry links to the recorded anchor instead of to nothing.
            if ($first && $columns['previous_hash'] !== null) {
                $previous = DB::table('audit_anchors')->where('last_hash', $columns['previous_hash'])->value('last_hash');
            }
            $first = false;

            if ($columns['previous_hash'] !== $previous) {
                return $this->broken($checked, (int) $columns['id'], 'The link to the previous entry does not match (an entry before this one was removed or changed).');
            }

            if (! hash_equals(Audit::hash($columns, $previous), (string) $columns['hash'])) {
                return $this->broken($checked, (int) $columns['id'], 'The content of this entry was changed after it was written.');
            }

            $previous = (string) $columns['hash'];
            $checked++;
        }

        return ['ok' => true, 'checked' => $checked, 'broken_at' => null, 'reason' => null];
    }

    /**
     * @return array{ok: bool, checked: int, broken_at: int|null, reason: string|null}
     */
    private function broken(int $checked, int $id, string $reason): array
    {
        return ['ok' => false, 'checked' => $checked, 'broken_at' => $id, 'reason' => $reason];
    }
}
