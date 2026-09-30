<?php

namespace App\Audit;

use Illuminate\Database\Eloquent\Model;

/**
 * Records created / updated / deleted / restored for a model, with the values before and after.
 *
 * The using model provides `auditModule()` (e.g. "loan_applications") and `auditLabel()`; it may list attributes
 * to leave out in `auditExcept()`. Secret attributes are redacted centrally (see Audit::REDACTED).
 *
 * @mixin Model
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(fn (self $model) => $model->writeAudit('created', [], $model->auditAttributes($model->getAttributes())));

        static::updated(function (self $model): void {
            $changed = array_keys($model->auditAttributes($model->getChanges()));
            $old = array_intersect_key($model->auditAttributes($model->getRawOriginal()), array_flip($changed));
            $new = array_intersect_key($model->auditAttributes($model->getAttributes()), array_flip($changed));

            if ($changed !== []) {
                $model->writeAudit('updated', $old, $new);
            }
        });

        static::deleted(fn (self $model) => $model->writeAudit('deleted', $model->auditAttributes($model->getAttributes()), []));

        if (method_exists(static::class, 'restored')) {
            static::restored(fn (self $model) => $model->writeAudit('restored', [], $model->auditAttributes($model->getAttributes())));
        }
    }

    /** Module the entries belong to; the table name unless the model says otherwise. */
    public function auditModule(): string
    {
        return $this->getTable();
    }

    /** A human-readable name of the record, kept on the entry so it still reads well after the record is gone. */
    public function auditLabel(): string
    {
        // Raw columns only: a model may have a method of the same name (e.g. CommitteePath::title()).
        foreach (['code', 'name', 'title', 'label', 'username'] as $column) {
            if (filled($this->attributes[$column] ?? null)) {
                return (string) $this->attributes[$column];
            }
        }

        return class_basename($this).' #'.$this->getKey();
    }

    /**
     * @return list<string>
     */
    public function auditExcept(): array
    {
        return [];
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private function auditAttributes(array $values): array
    {
        return array_diff_key($values, array_flip(['created_at', 'updated_at', 'deleted_at', ...$this->auditExcept()]));
    }

    /**
     * @param  array<string, mixed>  $old
     * @param  array<string, mixed>  $new
     */
    private function writeAudit(string $action, array $old, array $new): void
    {
        Audit::record($this->auditModule().'.'.$action, $this->auditModule(), $action, $this, $old, $new);
    }
}
