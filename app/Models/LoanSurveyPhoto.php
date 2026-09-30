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
 * Survey location photo with the coordinates captured when it was taken.
 *
 * @property int $id
 * @property int $loan_application_id
 * @property int|null $loan_survey_id
 * @property string $path
 * @property string $latitude
 * @property string $longitude
 * @property string $source
 * @property string $created_by
 * @property Carbon|null $created_at
 */
#[Fillable(['loan_application_id', 'loan_survey_id', 'path', 'latitude', 'longitude', 'source', 'created_by'])]
class LoanSurveyPhoto extends Model
{
    use Auditable;

    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return ['latitude' => 'decimal:7', 'longitude' => 'decimal:7'];
    }

    /**
     * @return BelongsTo<LoanApplication, $this>
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    /** The disk the attachments live on (config `filesystems.attachments`). */
    public static function disk(): FilesystemAdapter
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk((string) config('filesystems.attachments'));

        return $disk;
    }

    /** A private disk (s3) gets a short-lived signed link: the photos show where a customer's business or home is. */
    public function url(): string
    {
        $disk = self::disk();

        return $disk->providesTemporaryUrls() ? $disk->temporaryUrl($this->path, now()->addMinutes(30)) : $disk->url($this->path);
    }
}
