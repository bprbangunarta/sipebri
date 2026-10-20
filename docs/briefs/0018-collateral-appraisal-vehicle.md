# Collateral appraisal: the vehicle form (motorcycle and car)

Status: approved
Approved: 2026-10-21

## Goal

The people who appraise a motorcycle or a car can record everything the management's "Form Penilaian Agunan Kendaraan Roda 2 dan Roda 4" asks for (sections A to R), on the foundation of brief 0016: each appraisal is dated and kept as history, so the fall in value of the same vehicle from one year to the next is visible on its list of appraisals, and every figure the form works out is worked out by the system. Nothing is connected to the credit analysis or to core banking in this brief.

## Acceptance criteria

Each line can be answered "met" or "not met" with evidence (a test, a command, a screen checked in a browser).

- [x] **A vehicle template.** `AppraisalTemplates` gets a `vehicle` template (version 1) for the collateral types 10, 11, and 12 (vehicle or vessel with fiduciary, notarial, or other binding). The appraisal records its variant, motorcycle or car, taken from the collateral when the appraisal starts (new column `collateral_appraisals.variant`, so an old appraisal keeps its form if the collateral is edited later). The template shows section F (car) or G (motorcycle) according to the variant and never both; progress, validation, and the blockers only count the sections that apply.
- [x] **Fixed facts stay on the collateral** (new nullable columns by a new migration, shown on the collateral form for the vehicle types only): variant (motorcycle or car), brand, type/variant, model, year of manufacture, year of registration, colour, plate number, chassis number (VIN), engine number, engine capacity (cc), fuel, transmission, drive, fleet/unit number, BPKB number and date, name and address on the BPKB, STNK number and name. What changes by the day (STNK validity, odometer, number of keys, use, parking place, accessories) is an answer of the appraisal.
- [x] **One record per vehicle.** A chassis number or an engine number already held by another collateral that is not deleted is refused with a sentence that names the existing collateral, so the history of a vehicle is never split in two (a repeat loan appraises the same collateral again).
- [x] **Every item of the form is covered**, A to R, in the form's order: A identity (including place of inspection; no kind of inspection, the vehicle form has none); B vehicle identity (the observed part); C documents and ownership (the document table and the fourteen yes/no checks, each with evidence); D history and use (fifteen items); E general physical condition (eighteen components rated good, fair, poor, each with a note or repair cost); F car (thirteen components); G motorcycle (twelve components); H function test and diagnostics (ten items); I three market comparables; J adjustments per comparable (ten factors); K repair-cost estimate (seven lines with basis) and haircut (seven factors, each with its percentage or basis); L reconciliation and value; M loan-to-value and stress; N twelve risk factors; O fiduciary, insurance, and safeguards (twelve items); P twelve places for photos; Q conclusion; R independence statements.
- [x] **Numbers are calculated by the system, not typed**, in one pure class with its own tests with values worked out by hand: the indicated value of each comparable after its adjustments, the suggested market value before repair (their average) beside the value the appraiser selects, the total repair cost, the market value (before repair minus the repair cost), the total haircut (the sum of its factors, never above 100%), the liquidation value, loan-to-value and coverage, and the stress scenarios (10, 20, and 30% and the cost of collecting and selling). The total haircut is kept on the appraisal as `haircut_percent`.
- [x] **Hard stop.** An appraisal cannot be finalised as accepted or accepted with conditions while a check that the form says cannot be waived is not answered yes: the original BPKB and STNK were seen, names are consistent, chassis number and engine number match the documents, no sign that the numbers were altered, no dispute, blockade, or seizure, and the vehicle can be bound by fiduciary. The other checks (not pledged elsewhere, not stolen, no hidden financing, and the rest) are recorded and shown; the owner can move one into the hard stops by editing one list.
- [x] **History shows the fall in value.** The list of appraisals of a vehicle already shows market and liquidation value per appraisal; the list also shows the change from the previous finalised appraisal (rupiah and percent) so management reads the yearly fall at a glance. The same applies to every template.
- [x] **Screens.** The appraisal page renders the vehicle sections with the shared blocks of brief 0016 (fields, checks, ratings, grids, photos), the collateral form shows the vehicle facts for the vehicle types, all text is Indonesian, and the screens are checked in a browser at 1280, 768, and 375 px including empty, loading, and error states and the dialogs.
- [x] **Who and what is recorded.** The permissions stay `collaterals.view` and `collaterals.manage`; every appraisal, section, photo, and finalisation is on the audit record; the vehicle facts on the collateral are audited like every collateral change.
- [x] **Tests.** Each decision and each refusal has a test written first and seen to fail: the template for types 10, 11, 12, the variant snapshot and the section that applies, each calculation, the hard stops, the duplicate chassis and engine number (including a deleted collateral and the collateral itself on edit), the change from the previous appraisal, and the cross-checks of `AppraisalCalculationTest` and `CollateralAppraisalTest` extended to the vehicle form.
- [x] **Docs.** `CLAUDE.md` (the appraisal row), `memory/PRD.md`, and the rule files that mention collateral data are brought in line with the code.

## Out of scope

Connecting an appraisal to the credit analysis or mapping it to core banking; replacing the duplicated vehicle fields of the analysis sheet (`analysis_collaterals`) with the facts of the collateral (done together with the connection to the analysis); vessels and other kinds without a form from the management; reminders for an expiring STNK or an overdue re-appraisal; signatures and printing; who fills which section and in which step of the loan process.

## Assumptions and open questions

Defaults chosen for small things; the owner corrects them when approving.

- The collateral types 10, 11, and 12 all use the vehicle form, although their names also say vessel. A vessel has no form yet; until it has one it is appraised with the vehicle form's general parts, or the owner may ask for the types to be split.
- Hard stops follow the form's own sentence ("BPKB/STNK palsu, nomor rangka atau mesin tidak cocok/diubah, kepemilikan tidak jelas, sengketa/blokir/sita, tidak dapat diikat"); the checks on theft, other pledges, and hidden financing are recorded but do not block.
- The market value before repair is selected by the appraiser from the comparables, with the average shown as a suggestion, as in the land form of brief 0016. The adjustments are percentages per factor, summed.
- The status of a stress scenario is "M" when the ceiling is at most the value that is left, as in brief 0016.
- A duplicate chassis or engine number is refused, not only warned about, because a repeated loan on the same vehicle is a new appraisal of the same collateral.

## Reading done for this brief

Read in this conversation before writing it: the management's vehicle form in `docs/` in full, `CLAUDE.md`, `AGENTS.md`, `memory/PRD.md`, `.ai/rules/index.md` and the rule files it lists, and all the code of brief 0016 (the templates, the calculators, the saver, the payload, the controllers, the screens), which this brief extends. Not yet read, and read before the first edit: `tailwindcss-development`, and the skills of section 5 as the work needs them.

## Checklist

Every line is ticked `[x]`; a line that does not apply is ticked and ends with `N/A: reason`. Nothing is "done" while a `[ ]` remains. The rule behind each line is in the file it names; do not copy the rule here. A rule that the project adds (`AGENTS.md`, section 6) adds its own line here, under the group it belongs to.

Reading and scope

- [x] The PRD and the rule files matched to the paths touched are read (`AGENTS.md` section 1, `.ai/rules/index.md`)
- [x] The skills of section 5 that apply are named and used
- [x] The person who meets this change and the moment they are most likely to be confused are named, and handled: the analyst who appraises a car or motorcycle and must see the form that fits it, and the second appraisal of the same vehicle a year later (principle at the top of `AGENTS.md`)
- [x] Out of scope is respected; findings outside the task are reported, not fixed on the side

Design

- [x] The impact of a changed function, interface, field, or contract is searched (callers, screens, documents, tests) before the change
- [x] Who may do this is decided before the input is validated, and every input limit is explicit (`.ai/rules/quality.md`, questions 1 and 2)
- [x] Every outcome has one defined response, and a failure leaves nothing half done (`.ai/rules/quality.md`, questions 3 to 5)
- [x] Concurrency, repetition, and cost are handled (`.ai/rules/quality.md`, questions 4 and 6)
- [x] The code is readable, upgradeable, extensible, and scalable as `.ai/rules/quality.md`, "Design quality", asks: small units, one place to add a case, nothing kept in a process, nothing a person would not have written
- [x] Nothing leaks, and what must be on record is on record (`.ai/rules/quality.md`, questions 7 and 8)
- [x] Project rule: the change is on the audit record, a new model uses `Auditable`, and a new relation has a foreign key with a deliberate delete rule (`.ai/rules/audit-log.md`, `.ai/rules/general.md`, "Integritas relasi")
- [x] Project rule: a schema change is a new migration, never an edited one, and the database is not reset (`.ai/rules/database.md`)
- [x] Project rule: code, comments, tests, and URLs are English; screen text is Indonesian (`.ai/rules/general.md`)

Screen (N/A without a screen)

- [x] States (loading, empty, error), words, and confirmation of irreversible actions follow the UI rules (`.ai/rules/ui.md`, and the project's `.ai/rules/js.md`: shared components, one value per column, server-side sort keys, inputs that enforce the server rule)
- [x] Checked in a browser at 1280 px and at 375 px, with the console clear
- [x] The antislop Delivery Gate is answered as PASS/FAIL lines with evidence in the report

Tests and documents

- [x] Each risk and each refusal has a test (`.ai/rules/quality.md`, question 9; `.ai/rules/testing.md`), and the cross-check tests are green
- [x] Documents and code are compared in both directions, stale text is fixed, and `memory/PRD.md` is updated (a decision that outlives this change is one line there)
- [x] The notes an agent keeps for itself (its own memory, if it has one) are updated with what this change decided or taught, without waiting to be asked

Gates and close

- [x] `scripts/verify.sh` is green and the build runs
- [x] Every acceptance criterion above is verified with evidence in the report
- [x] The owner is told what changed, what was not done, and why; this brief is deleted in the closing commit
