<?php

namespace App\Support;

use App\Models\CommitteeTier;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Who sits on the credit committee. Membership is not a list that has to be kept in step with anything: a committee member is
 * simply an active person whose Codex role decides in at least one tier of a committee path. What is added on top is the
 * person's national ID (KTP), so an applicant can be recognised as a member.
 */
class CommitteeMembers
{
    /**
     * Every role that decides in some tier, committee or individual. Used to recognise an applicant who could decide.
     *
     * @return list<string>
     */
    public static function roles(): array
    {
        return array_values(CommitteeTier::query()->distinct()->pluck('role')->all());
    }

    /**
     * Roles that sit in a committee proper: they appear in a tier that is not an individual authority.
     *
     * @return list<string>
     */
    public static function committeeRoles(): array
    {
        return array_values(CommitteeTier::query()->where('is_individual', false)->distinct()->pluck('role')->all());
    }

    /**
     * The people of the committees only (the analyst staff, who decide alone, are left out).
     *
     * @return Builder<User>
     */
    public static function committee(): Builder
    {
        $roles = self::committeeRoles();

        return $roles === [] ? User::query()->whereRaw('1 = 0') : User::query()->role($roles);
    }

    /**
     * @return Builder<User>
     */
    public static function query(): Builder
    {
        $roles = self::roles();

        return $roles === [] ? User::query()->whereRaw('1 = 0') : User::query()->role($roles);
    }

    public static function isMember(User $user): bool
    {
        return $user->hasAnyRole(self::roles());
    }

    /** The committee member with this national ID, if the applicant is one. */
    public static function findByNik(string $nik): ?User
    {
        $user = User::findByNik($nik);

        return $user !== null && self::isMember($user) ? $user : null;
    }

    /**
     * @return list<array{value: int, label: string}>
     */
    public static function options(): array
    {
        $options = self::query()->with('roles:id,name')->orderBy('name')->get()
            ->map(fn (User $u): array => ['value' => $u->id, 'label' => $u->name.' — '.$u->getRoleNames()->first()]);

        return array_values($options->all());
    }

    /** 3201••••••••0001 style: enough to recognise a number, not enough to use it. */
    public static function mask(?string $nik): ?string
    {
        return $nik === null ? null : str_repeat('•', max(0, strlen($nik) - 4)).substr($nik, -4);
    }
}
