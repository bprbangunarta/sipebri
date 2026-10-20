# Collateral appraisal with history: the foundation and the land and building form

Status: approved
Approved: 2026-10-20

## Goal

On `/collaterals` the people who appraise a collateral can record everything the management's "Form Penilaian Agunan Tanah dan Bangunan" asks for (sections A to Q; R, the independence statement, is recorded as answers without signatures), so that every piece of data is covered and can be checked. Each appraisal is its own record with an effective date, so the same collateral can be appraised again for a new loan, a top-up, or a review and the earlier appraisals stay as history. The result is what the credit analysis will later read; this brief does not connect it.

## Acceptance criteria

Each line can be answered "met" or "not met" with evidence (a test, a command, a screen checked in a browser).

- [ ] **History.** A collateral has many appraisals. A finalised appraisal is never changed or deleted; a new appraisal for the same collateral starts as a draft (with an option to copy the answers of the previous one for a review) and has its own effective date and validity date. The list of appraisals of a collateral shows them newest first with status, purpose, effective date, market value, liquidation value, and conclusion.
- [ ] **Templates by kind.** The sections, items, and rating scales of an appraisal come from one registry (`App\Support\Appraisal\AppraisalTemplates`, in the spirit of `AnalysisTemplates`), chosen by the collateral type and recorded with a template key and version on every appraisal. This brief ships two templates: `land_building` (collateral types 05, 06, 07, 08) and `general` (every other type, so every collateral can be appraised before its own form exists: identity, documents and legal checks, value, loan-to-value, risk, conclusion). Adding the vehicle form later is a registry entry plus its calculations, not a rewrite.
- [ ] **Fixed facts stay on the collateral** (new nullable columns by a new migration): kind of right (SHM, SHGB, SHP, other), holder name, issue date, expiry date, land survey document and its date, land area per certificate, land office, parcel number (NIB), origin of acquisition, tax object number (NOP), full object address, postal code, province, route hint; and for the building: permit number and date (PBG/IMB), operating certificate number and date (SLF), permitted building area, permitted use. Existing columns keep their meaning and their CBS mapping is not touched.
- [ ] **Everything observed at the appraisal date is in the appraisal** and covers every item of the land and building form: A identity of the appraisal (purpose, kind of inspection, appraiser, reviewer, effective date, report date, contact, relation to the debtor; application, debtor, product, and ceiling are read from the linked loan file, or typed when the appraisal is not tied to a file); B object (current use, highest and best use, occupant, distance from the office); C land documents' verification results; D building documents' results; E fourteen legal yes/no checks each with evidence; F land physical data; G building physical data for the main and other buildings; H zoning, environment, marketability; I three market comparables; J adjustments per comparable; K building cost approach with physical, functional, and economic depreciation; L reconciliation; M loan-to-value and stress scenarios; N twelve risk factors rated low, medium, high with mitigation; O binding, insurance, and conditions; P photo documentation; Q conclusion; R independence statements.
- [ ] **Numbers are calculated by the system, not typed**, in one pure class with its own tests: comparable indicative value after adjustments, selected land value per m², building present value after depreciation (total depreciation is the sum of the three, never above 100%), indicative market value after deductions, liquidation value after haircut, loan-to-value and coverage against the ceiling, and the stress scenarios. Money is whole rupiah; percentages keep at most two decimals; a screen mirror of a formula only displays and never replaces the server.
- [ ] **Hard stop.** An appraisal whose hard-stop legal checks are answered "no" (original documents not seen, identity does not match, dispute, blockade or seizure, cannot be encumbered with a mortgage right, access without legal basis) cannot be finalised as "accepted" or "accepted with conditions"; the screen says which check blocks it and the server refuses it.
- [ ] **Photos** belong to the appraisal and to one of the ten items of the form's photo list. Upload accepts images only (jpeg, png, webp), at most 5 MB each and 5 per item, is stored on the attachments disk (`config('filesystems.attachments')`), is served through the same signed-link mechanism as survey photos on S3, and can be removed only while the appraisal is a draft. Device position is never read from an uploaded photo (`ArchitectureTest`).
- [ ] **Screens.** `/collaterals` gets a way into the appraisals of each collateral; the appraisal page has one tab per section of the form, saves a section at a time (like the analysis sheet), shows what is still empty or blocking, and shows the history. All text is Indonesian, all URLs and code English, built from the shared components, and checked in a browser at 1280, 768, and 375 px including empty, loading, and error states and the dialogs.
- [ ] **Who and what is recorded.** Reading and writing follow the existing `collaterals.view` and `collaterals.manage` permissions (who fills which section is decided later); every appraisal, section, photo, and finalisation is on the audit record with who and when; opening an appraisal is recorded like opening a collateral.
- [ ] **Tests.** Each decision and each refusal has a test written first and seen to fail: history is never overwritten, finalised appraisals refuse changes, hard stop, every calculation with known values, template selection by type, photo limits and cleanup, permission refusals, audit entries, and the machine checks of `ArchitectureTest`.
- [ ] **Docs.** `CLAUDE.md` (architecture table, the Agunan row), `memory/PRD.md` (decisions and next steps), and the rule files that mention collateral data are brought in line with the code.

## Out of scope

The vehicle (roda 2 and roda 4) form, which follows as the next brief on the same foundation. Changing the CBS payload or the CBS value columns (`njop_value`, `appraisal_value`, and the others keep their meaning; mapping an appraisal's result to CBS comes last). Connecting the appraisal to the analysis sheet's "Analisa Agunan" section. Who fills which section and in which step of the loan process. Signatures and printing. Capturing photos from the survey screen. Importing old appraisals.

## Assumptions and open questions

Defaults chosen for small things; the owner corrects them when approving.

- The stress status "M" (meets) and "TM" (does not meet) is decided by this initial rule: loan-to-value at or below 100% of the stressed value (that is, the stressed value still covers the ceiling). The management's threshold is not in the forms or the code; changing it is one constant.
- The adjustments of a comparable are entered as a percentage per factor (plus or minus); total adjustment is their sum.
- The "selected" values in the reconciliation table (land, building, facilities, deductions) are entered by the appraiser; the system shows the market-approach and cost-approach suggestions beside them and calculates everything after them.
- "Dapat diterima", "Dapat diterima bersyarat", "Tidak dapat diterima" is the status of a finalised appraisal. No new loan status is added (`LoanStatus` stays as it is).
- Appraisals keep their own values; the collateral's existing value columns are not written by an appraisal in this brief.
- The collateral type to template mapping is 05, 06, 07, 08 to `land_building` and the rest to `general`; type 07 (adat) is covered by the kind of right "other".
- The `.docx` forms in `docs/` stay where they are and are not edited.

## Reading done for this brief

Read in this conversation before writing it: `CLAUDE.md`, `AGENTS.md`, `memory/PRD.md`, `.ai/rules/index.md` and every rule file it lists, the antislop skills and the Boost skills `laravel-best-practices`, `testing-best-practices`, `inertia-react-development`, `wayfinder-development` (read for brief 0015), both `.docx` forms in `docs/` in full, `Collateral`, `CollateralController`, the labels of `collaterals/form.tsx`, `AnalysisCollateral` and its migration, `AnalysisTemplates`, the old SIPEBRI's `AgunanController`, `data_jaminan`, and the column list of `send_jaminan` (read only). Not yet read, and read before the first edit: `collaterals/index.tsx`, the file's attach dialog, the analysis collateral controller and section, `ItemSync`, `Auditable`, `PhotoExif`, the survey photo storage code, and `tailwindcss-development`.

## Checklist

Every line is ticked `[x]`; a line that does not apply is ticked and ends with `N/A: reason`. Nothing is "done" while a `[ ]` remains. The rule behind each line is in the file it names; do not copy the rule here. A rule that the project adds (`AGENTS.md`, section 6) adds its own line here, under the group it belongs to.

Reading and scope

- [ ] The PRD and the rule files matched to the paths touched are read (`AGENTS.md` section 1, `.ai/rules/index.md`)
- [ ] The skills of section 5 that apply are named and used
- [ ] The person who meets this change and the moment they are most likely to be confused are named, and handled: the analyst who appraises a collateral for the second time and must see what was valued before, what is copied, and which value is current (principle at the top of `AGENTS.md`)
- [ ] Out of scope is respected; findings outside the task are reported, not fixed on the side

Design

- [ ] The impact of a changed function, interface, field, or contract is searched (callers, screens, documents, tests) before the change
- [ ] Who may do this is decided before the input is validated, and every input limit is explicit (`.ai/rules/quality.md`, questions 1 and 2)
- [ ] Every outcome has one defined response, and a failure leaves nothing half done (`.ai/rules/quality.md`, questions 3 to 5)
- [ ] Concurrency, repetition, and cost are handled (`.ai/rules/quality.md`, questions 4 and 6)
- [ ] The code is readable, upgradeable, extensible, and scalable as `.ai/rules/quality.md`, "Design quality", asks: small units, one place to add a case, nothing kept in a process, nothing a person would not have written
- [ ] Nothing leaks, and what must be on record is on record (`.ai/rules/quality.md`, questions 7 and 8)
- [ ] Project rule: the change is on the audit record, a new model uses `Auditable`, and a new relation has a foreign key with a deliberate delete rule (`.ai/rules/audit-log.md`, `.ai/rules/general.md`, "Integritas relasi")
- [ ] Project rule: a schema change is a new migration, never an edited one, and the database is not reset (`.ai/rules/database.md`)
- [ ] Project rule: code, comments, tests, and URLs are English; screen text is Indonesian (`.ai/rules/general.md`)

Screen (N/A without a screen)

- [ ] States (loading, empty, error), words, and confirmation of irreversible actions follow the UI rules (`.ai/rules/ui.md`, and the project's `.ai/rules/js.md`: shared components, one value per column, server-side sort keys, inputs that enforce the server rule)
- [ ] Checked in a browser at 1280 px and at 375 px, with the console clear
- [ ] The antislop Delivery Gate is answered as PASS/FAIL lines with evidence in the report

Tests and documents

- [ ] Each risk and each refusal has a test (`.ai/rules/quality.md`, question 9; `.ai/rules/testing.md`), and the cross-check tests are green
- [ ] Documents and code are compared in both directions, stale text is fixed, and `memory/PRD.md` is updated (a decision that outlives this change is one line there)
- [ ] The notes an agent keeps for itself (its own memory, if it has one) are updated with what this change decided or taught, without waiting to be asked

Gates and close

- [ ] `scripts/verify.sh` is green and the build runs
- [ ] Every acceptance criterion above is verified with evidence in the report
- [ ] The owner is told what changed, what was not done, and why; this brief is deleted in the closing commit
