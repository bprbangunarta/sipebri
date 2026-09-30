<?php

use App\Enums\RoleName;

return [
    /*
    | Survey scheduling (stage 2).
    */
    // More schedules than this only raises a warning; it never blocks.
    'max_schedules' => 3,

    // Roles that survey a file, in order. The first survey is done by the first role; each survey judged
    // insufficient escalates the next one to the next role. No calculation, only a feasibility opinion.
    'surveyor_ladder' => [
        RoleName::Analyst->value,
        RoleName::AnalysisSectionHead->value,
        RoleName::AnalysisDepartmentHead->value,
        RoleName::BusinessDirector->value,
        RoleName::PresidentDirector->value,
    ],

    // The customer walks in: no field survey, the file skips the visit, handled by office staff.
    'walk_in_product' => 'KTA',
    'walk_in_roles' => [RoleName::BranchCashHead->value, RoleName::CustomerService->value, RoleName::Teller->value],

    /*
    | Survey (stage 3).
    */
    'max_survey_photos' => 5,
];
