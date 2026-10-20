<?php

namespace App\Support\Appraisal;

use App\Models\Collateral;
use App\Models\CollateralAppraisal;
use InvalidArgumentException;

/**
 * The forms an appraisal can follow. An appraisal records the template and the version it was made with, so it keeps its form
 * when a form changes; a form with a new version keeps the older one here as long as appraisals use it.
 */
final class AppraisalTemplates
{
    /**
     * @return list<AppraisalTemplate> every form and version, newest of each last
     */
    private static function all(): array
    {
        return [new LandBuildingTemplate, new GeneralTemplate];
    }

    /** The form for a collateral that gets a new appraisal. */
    public static function for(Collateral $collateral): AppraisalTemplate
    {
        return in_array($collateral->collateral_type_code, LandBuildingTemplate::TYPES, true) ? new LandBuildingTemplate : new GeneralTemplate;
    }

    /** The form an existing appraisal was made with. */
    public static function of(CollateralAppraisal $appraisal): AppraisalTemplate
    {
        foreach (self::all() as $template) {
            if ($template->key() === $appraisal->template && $template->version() === $appraisal->template_version) {
                return $template;
            }
        }

        throw new InvalidArgumentException("Unknown appraisal form {$appraisal->template} v{$appraisal->template_version}.");
    }

    /**
     * Every item of a template with the section and block layout it sits in.
     *
     * @return list<array<string, mixed>> items with `section` and `layout`
     */
    public static function items(AppraisalTemplate $template): array
    {
        $items = [];

        foreach ($template->sections() as $section) {
            foreach ($section['blocks'] as $block) {
                foreach (Fields::itemsOf($section['key'], $block) as $item) {
                    $items[] = $item + ['section' => $section['key'], 'layout' => $block['layout']];
                }
            }
        }

        return $items;
    }
}
