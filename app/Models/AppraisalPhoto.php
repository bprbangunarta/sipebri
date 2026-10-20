<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * A photo of an appraisal, kept under one item of the template's photo list.
 *
 * @property int $id
 * @property int $collateral_appraisal_id
 * @property string $item
 * @property string $path
 * @property string $created_by
 * @property Carbon|null $created_at
 */
#[Fillable(['collateral_appraisal_id', 'item', 'path', 'created_by'])]
class AppraisalPhoto extends Model
{
    use Auditable;

    public const UPDATED_AT = null;

    protected $table = 'collateral_appraisal_photos';

    /**
     * @return BelongsTo<CollateralAppraisal, $this>
     */
    public function appraisal(): BelongsTo
    {
        return $this->belongsTo(CollateralAppraisal::class, 'collateral_appraisal_id');
    }

    public function auditModule(): string
    {
        return 'collateral_appraisals';
    }

    public function auditLabel(): string
    {
        return "Foto penilaian #{$this->collateral_appraisal_id}: {$this->item}";
    }

    /** The disk the attachments live on (config `filesystems.attachments`). */
    public static function disk(): FilesystemAdapter
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk((string) config('filesystems.attachments'));

        return $disk;
    }

    /** A private disk (s3) gets a short-lived signed link: the photos show where a person's property is. */
    public function url(): string
    {
        $disk = self::disk();

        return $disk->providesTemporaryUrls() ? $disk->temporaryUrl($this->path, now()->addMinutes(30)) : $disk->url($this->path);
    }
}
