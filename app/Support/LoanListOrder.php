<?php

namespace App\Support;

use App\Models\LoanApplication;
use App\Models\LoanSchedule;
use App\Models\Office;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * The sort keys shared by the lists of loan application files (applications, scheduling) and what each one orders by:
 * a column of the file, or the name of a related record read through a subquery so the list keeps one row per file.
 * Each controller keeps its own whitelist of the keys its table shows.
 */
class LoanListOrder
{
    private const COLUMNS = ['application_code', 'application_date', 'full_name', 'nik', 'requested_amount', 'requested_tenor', 'status', 'survey_date'];

    /**
     * @param  Builder<LoanApplication>  $query
     * @param  'asc'|'desc'  $direction
     * @return Builder<LoanApplication>
     */
    public static function apply(Builder $query, string $key, string $direction): Builder
    {
        if (in_array($key, self::COLUMNS, true)) {
            return $query->orderBy('loan_applications.'.$key, $direction);
        }

        $related = match ($key) {
            'product' => Product::query()->select('alias')->whereColumn('products.id', 'loan_applications.product_id'),
            'office' => Office::query()->select('alias')->whereColumn('offices.id', 'loan_applications.office_id'),
            'supervisor' => User::query()->select('name')->whereColumn('users.id', 'loan_applications.supervisor_id'),
            'surveyor' => User::query()->select('name')->whereColumn('users.id', 'loan_applications.surveyor_id'),
            // Only schedules and reschedules count, the same number the list shows.
            'schedule_count' => LoanSchedule::query()->selectRaw('count(*)')->whereColumn('loan_schedules.loan_application_id', 'loan_applications.id')
                ->whereIn('action', [LoanSchedule::ACTION_SCHEDULE, LoanSchedule::ACTION_RESCHEDULE]),
            default => null,
        };

        return $related === null ? $query : $query->orderBy($related, $direction);
    }
}
