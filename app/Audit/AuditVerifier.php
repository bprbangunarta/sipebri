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
        $lastId = null;
        // The last entry that an earlier verification found intact: it must still be there, unchanged.
        $head = DB::table('audit_verified_heads')->orderByDesc('id')->first();
        $headSeen = false;

        foreach (DB::table('audit_logs')->orderBy('id')->cursor() as $row) {
            $columns = (array) $row;

            // After pruning, the oldest remaining entry links to the recorded anchor instead of to nothing.
            if ($first && $columns['previous_hash'] !== null) {
                $previous = DB::table('audit_anchors')->where('last_hash', $columns['previous_hash'])->value('last_hash');
            }
            $first = false;

            if ($columns['previous_hash'] !== $previous) {
                return $this->broken($checked, (int) $columns['id'], 'Tautan ke entri sebelumnya tidak cocok (ada entri sebelumnya yang dihapus atau diubah).');
            }

            if (! hash_equals(Audit::hash($columns, $previous), (string) $columns['hash'])) {
                return $this->broken($checked, (int) $columns['id'], 'Isi entri ini diubah setelah ditulis.');
            }

            if ($head !== null && (int) $columns['id'] === (int) $head->entry_id) {
                $headSeen = true;

                if ($columns['hash'] !== $head->hash) {
                    return $this->broken($checked, (int) $columns['id'], 'Entri ini sudah pernah diperiksa dan sekarang isinya lain.');
                }
            }

            $previous = (string) $columns['hash'];
            $lastId = (int) $columns['id'];
            $checked++;
        }

        // Entries removed at the end leave a chain that is still linked, so the only proof is the entry that was checked last time.
        if ($head !== null && ! $headSeen && (int) DB::table('audit_anchors')->max('pruned_through_id') < (int) $head->entry_id) {
            return $this->broken($checked, (int) $head->entry_id, "Entri terbaru yang sudah pernah diperiksa (#{$head->entry_id}) tidak ada lagi: entri terbaru dihapus.");
        }

        if ($lastId !== null && ($head === null || (int) $head->entry_id !== $lastId)) {
            DB::table('audit_verified_heads')->insert(['entry_id' => $lastId, 'hash' => $previous]);
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
