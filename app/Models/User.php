<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Audit\Audit;
use App\Audit\Auditable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id Same as the Codex user id (mass assignable on purpose).
 * @property string $name
 * @property string|null $username Codex username.
 * @property int|null $office_id
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at Soft delete = inactive at Codex; such users cannot sign in.
 * @property string|null $mfa_method "totp" or "email"
 * @property string|null $mfa_secret
 * @property list<string>|null $mfa_recovery_codes Hashes of the unused recovery codes.
 * @property Carbon|null $mfa_confirmed_at
 * @property string|null $nik National ID (KTP), encrypted; never shown in full outside the form that edits it.
 * @property string|null $nik_hash Keyed hash of the NIK, to look people up by NIK.
 * @property string|null $nik_source Where the NIK came from: codex or manual.
 * @property-read Office|null $office
 */
#[Fillable(['id', 'username', 'name', 'email', 'password', 'office_id'])]
#[Hidden(['password', 'remember_token', 'mfa_secret', 'mfa_recovery_codes', 'nik', 'nik_hash'])]
class User extends Authenticatable
{
    use Auditable;

    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * Secrets and session noise stay out of the trail; two-factor changes are recorded explicitly.
     *
     * @return list<string>
     */
    public function auditExcept(): array
    {
        return ['password', 'remember_token', 'mfa_secret', 'mfa_recovery_codes', 'mfa_method', 'mfa_confirmed_at', 'nik', 'nik_hash'];
    }

    /** Keyed hash used to find a person by NIK without decrypting everyone (the app key is the secret). */
    public static function nikHash(string $nik): string
    {
        return hash_hmac('sha256', $nik, (string) config('app.key'));
    }

    /** The active person with this NIK, if any. */
    public static function findByNik(string $nik): ?self
    {
        return preg_match('/^\d{16}$/', $nik) === 1 ? self::query()->where('nik_hash', self::nikHash($nik))->first() : null;
    }

    /**
     * Set or clear the NIK. The value itself is never written to the audit trail (it is excluded there); the fact that it
     * changed, and where it came from, is.
     */
    public function setNik(?string $nik, string $source): void
    {
        $changed = $nik === null ? $this->nik !== null : $this->nik !== $nik;

        $this->forceFill([
            'nik' => $nik,
            'nik_hash' => $nik === null ? null : self::nikHash($nik),
            'nik_source' => $nik === null ? null : $source,
        ])->save();

        if ($changed) {
            Audit::record('users.nik_changed', 'users', 'nik_changed', $this, context: ['source' => $source, 'cleared' => $nik === null]);
        }
    }

    public function auditLabel(): string
    {
        return trim(($this->username ?? '').' '.($this->name ?? ''));
    }

    /**
     * @return BelongsTo<Office, $this>
     */
    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'mfa_secret' => 'encrypted',
            'mfa_recovery_codes' => 'encrypted:array',
            'mfa_confirmed_at' => 'datetime',
            'nik' => 'encrypted',
        ];
    }
}
