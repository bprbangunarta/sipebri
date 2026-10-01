<?php

namespace App\Support\CreditAnalysis;

use App\Models\LoanApplication;

/**
 * The worksheets a credit analysis can follow. Every analysis records the template and its version, so a file keeps the
 * worksheet it was made with when a template changes. For now every product follows the general worksheet (the one used
 * before this system); a product with a worksheet of its own gets its template added here and mapped in `for()`.
 */
final class AnalysisTemplates
{
    public const GENERAL = 'general';

    public const CURRENT_VERSION = 1;

    /**
     * The sections of a template, in the order they are filled in: key, label, icon (see components/nav-icons) and the
     * route segment of its form.
     *
     * @return list<array{key: string, label: string, icon: string}>
     */
    public static function sections(string $template = self::GENERAL): array
    {
        return [
            ['key' => 'business', 'label' => 'Analisa Usaha', 'icon' => 'store'],
            ['key' => 'finance', 'label' => 'Analisa Keuangan', 'icon' => 'banknote'],
            ['key' => 'ownership', 'label' => 'Analisa Kepemilikan', 'icon' => 'key-round'],
            ['key' => 'collateral', 'label' => 'Analisa Agunan', 'icon' => 'landmark'],
            ['key' => 'five-c', 'label' => 'Analisa 5C', 'icon' => 'shield-check'],
            ['key' => 'qualitative', 'label' => 'Analisa Kualitatif', 'icon' => 'messages'],
            ['key' => 'memorandum', 'label' => 'Memorandum', 'icon' => 'file-text'],
            ['key' => 'administration', 'label' => 'Administrasi', 'icon' => 'clipboard-list'],
        ];
    }

    /** The template a file follows. */
    public static function for(LoanApplication $loan): string
    {
        return self::GENERAL;
    }
}
