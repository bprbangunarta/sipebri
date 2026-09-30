<?php

namespace App\Enums;

/**
 * Roles the code itself refers to (committee tiers, surveyor ladder, gates). The names are exactly what
 * the Codex sign-in API reports, so they double as the key that links a Codex user to a local role.
 * Every other Codex role only needs to exist (see RoleSeeder::STANDARD_ROLES).
 */
enum RoleName: string
{
    case SuperAdmin = 'Super Admin';
    case Guest = 'Guest';
    case CreditOfficer = 'AO Kredit';
    case AnalysisSectionHead = 'Kepala Seksi Analis';
    case AnalysisDepartmentHead = 'Kepala Bagian Analis';
    case Analyst = 'Staff Analis & Appraisal';
    case BusinessDirector = 'Direktur Bisnis';
    case PresidentDirector = 'Direktur Utama';
    case BranchCashHead = 'Kepala Kantor Kas';
    case CustomerService = 'Customer Service';
    case Teller = 'Teller';
}
