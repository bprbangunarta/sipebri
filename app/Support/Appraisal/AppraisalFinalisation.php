<?php

namespace App\Support\Appraisal;

use App\Models\CollateralAppraisal;

/**
 * What stands between a draft and a finalised appraisal. A finalised appraisal is history and is never changed, so it must say
 * who appraised it, as of when, and what the conclusion is; an acceptance needs the legal checks that cannot be waived.
 */
final class AppraisalFinalisation
{
    /**
     * @return list<string> the reasons it cannot be finalised yet, in words for the appraiser
     */
    public static function blockers(CollateralAppraisal $appraisal, AppraisalTemplate $template): array
    {
        $values = AppraisalValues::of($appraisal);
        $blockers = [];

        foreach (['h:effective_date' => 'Isi tanggal efektif nilai (bagian A).', 'h:appraiser_name' => 'Isi nama appraiser/petugas (bagian A).', 'h:decision' => 'Pilih status agunan di kesimpulan.'] as $key => $message) {
            if (blank($values[$key] ?? null)) {
                $blockers[] = $message;
            }
        }

        $accepts = in_array($values['h:decision'] ?? null, ['accepted', 'conditional'], true);

        return $accepts ? [...$blockers, ...self::acceptance($appraisal, $template, $values)] : $blockers;
    }

    /**
     * @param  array<string, string>  $values
     * @return list<string>
     */
    private static function acceptance(CollateralAppraisal $appraisal, AppraisalTemplate $template, array $values): array
    {
        $labels = collect(AppraisalTemplates::items($template))->pluck('label', 'key');
        $blockers = [];

        foreach ($template->hardStops() as $key) {
            if (($values[$key] ?? null) !== 'yes') {
                $blockers[] = "Pemeriksaan \"{$labels[$key]}\" harus dijawab Ya sebelum agunan dapat diterima.";
            }
        }

        if ($appraisal->market_value <= 0) {
            $blockers[] = 'Nilai pasar belum terhitung.';
        }

        if ($appraisal->liquidation_value <= 0) {
            $blockers[] = 'Nilai likuidasi belum terhitung: isi haircut.';
        }

        if (($values['h:decision'] ?? null) === 'conditional' && blank($values['h:main_conditions'] ?? null)) {
            $blockers[] = 'Isi syarat utama untuk status "Dapat diterima bersyarat".';
        }

        return $blockers;
    }
}
