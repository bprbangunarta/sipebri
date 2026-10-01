<?php

namespace App\Enums;

/**
 * Lifecycle of a loan file. No status may be added without agreeing the business flow first:
 * draft → submitted → scheduling → survey → analysis → committee → approved | rejected → disbursed
 * (a file may also be cancelled).
 */
enum LoanStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Scheduling = 'scheduling';
    case Survey = 'survey';
    case Analysis = 'analysis';
    case Committee = 'committee';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
    case Disbursed = 'disbursed';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draf',
            self::Submitted => 'Diajukan',
            self::Scheduling => 'Dijadwalkan',
            self::Survey => 'Disurvei',
            self::Analysis => 'Dalam analisa',
            self::Committee => 'Di komite',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
            self::Cancelled => 'Dibatalkan',
            self::Disbursed => 'Dicairkan',
        };
    }

    /**
     * @return 'neutral'|'info'|'success'|'warning'|'danger'
     */
    public function tone(): string
    {
        return match ($this) {
            self::Draft, self::Cancelled => 'neutral',
            self::Submitted, self::Scheduling, self::Survey, self::Analysis => 'info',
            self::Committee => 'warning',
            self::Approved, self::Disbursed => 'success',
            self::Rejected => 'danger',
        };
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(fn (self $s): array => ['value' => $s->value, 'label' => $s->label()], self::cases());
    }
}
