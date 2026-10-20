# Sample collaterals and filled-in appraisals for testing and as examples

Status: approved
Approved: 2026-10-21

## Goal

The owner and the analysts can open collaterals shaped like the real ones in the legacy collateral screens (two kinds of land and building, a car, a motorcycle, and two supporting documents) and read appraisals filled in through every section of the new forms, as examples of how a form is filled and as data to test the screens with. Nothing here is connected to the credit analysis or to core banking.

## Acceptance criteria

Each line can be answered "met" or "not met" with evidence (a test, a command, a screen checked in a browser).

- [ ] **A seeder for non-production.** `CollateralExampleSeeder` is called by `DatabaseSeeder` only outside production (like `UserSeeder`), is idempotent (running it again adds nothing and changes nothing an owner edited), writes nothing to the audit trail (like every seeder), and uses the real domain classes (`AppraisalStarter`, `AppraisalSaver`, the finalisation) so every figure in a sample is the one the system works out.
- [ ] **Six collaterals in the shape of the six legacy screens** (the screenshots the owner gave): a land and building with a certificate and mortgage right (type 05, binding 01) whose description says customary papers; a land and building with a certificate and no binding yet (type 06, binding 06) held by two owners; a car (type 10, binding 03); a motorcycle (type 10, binding 03); two supporting documents (type 14, binding 99) with no value. They keep the types, bindings, location code, the amounts (mortgage value, market value, tax value, internal appraisal), the conditions, the insurance, and the method of the screens.
- [ ] **The data of the screens is used as it is.** The owner confirmed that the screenshots come from the dummy core banking, so the names, addresses, document numbers, accounts, and collateral ids are kept as the screens show them (a value the screen cuts off, like the end of a plate number, is completed with an invented one and said so). The facts of a vehicle that the screens do not state (brand, year) are read from the chassis number and said so in the report.
- [ ] **One credit account holds several collaterals.** The screens show one account with two and with three collaterals, but `collaterals.credit_account` is unique, so the second one cannot be saved. The unique index becomes a plain index by a new migration, the unique rule of the form is removed, and a test proves that two collaterals can carry the same account while `cbs_id` stays unique.
- [ ] **Four finalised appraisals filled through every section of their form**, each with a written assessment in the conclusion, the figures worked out, and the photo places left empty (no invented photos): land with a mortgage right (an assessment that finds the customary-papers description against a certificate-with-mortgage type and ends in "Tidak dapat diterima" through the hard stop), land without a binding (accepted with conditions: bind it, consent of both owners), the car (accepted with conditions), the motorcycle (accepted). 
- [ ] **Marked as samples.** Every appraisal of a sample says in its summary that it is a sample filled in to show how the form is used and not an appraisal of a real object; the appraiser is "PENILAI CONTOH".
- [ ] **The two supporting documents (type 14) get no appraisal.** The general form cannot accept a collateral whose market value is zero, and such a document (an employee decree, a social-security card) has none; the finding is reported to the owner as a decision to take (whether such collaterals are appraised at all, or accepted without a value), not worked around in the seeder.
- [ ] **Tests.** A test seeds twice and proves: the six collaterals exist once; the four appraisals are final with known market values, liquidation values, and decisions; no collateral of type 14 has an appraisal; `DatabaseSeeder` does not call the sample seeder in production; and every answer of a sample passes the rules of its item (the same rules a request goes through).
- [ ] **Docs.** `CLAUDE.md` (how to run the seeders), and `memory/PRD.md` (the finding about zero-value collaterals as an open question) are brought in line.

## Out of scope

Connecting an appraisal to the credit analysis or mapping it to core banking. Photos. Changing the forms or the acceptance rule for zero-value collaterals (reported, not decided). The real data of the screenshots: nothing of it is stored.

## Assumptions and open questions

- The ceilings used for loan-to-value in the samples are invented (about 60 to 70% of the liquidation value) because the screenshots hold no loan.
- Where the screens show an internal appraisal that I would not reproduce (for example 129.8 million against a market value of 437.5 million for the first land), the sample shows my own assessment from comparables and the form's method, so the sample teaches the method and is not a copy of the legacy figure; the legacy figure is kept in the collateral's own value columns.
- The sample appraisals use the dates of the screens (October 2026) as effective dates.
- The owner said the screens can also serve as the reference for the request that creates a collateral in core banking. This brief only checks `toCbsPayload()` against the screens and reports what has no place in it; the mapping itself is not changed here.

## Reading done for this brief

Read in this conversation before writing it: the six screenshots, `database/seeders/DatabaseSeeder.php`, `.ai/rules/database.md` and the other rule files (read earlier in this conversation), `CLAUDE.md`, `memory/PRD.md`, and all the appraisal code of briefs 0016 and 0018. Not yet read, and read before the first edit: `database/seeders/UserSeeder.php` and `tests/Feature/UserSeederTest.php` (the pattern to follow), and `tailwindcss-development` is not needed.

## Checklist

Every line is ticked `[x]`; a line that does not apply is ticked and ends with `N/A: reason`. Nothing is "done" while a `[ ]` remains. The rule behind each line is in the file it names; do not copy the rule here. A rule that the project adds (`AGENTS.md`, section 6) adds its own line here, under the group it belongs to.

Reading and scope

- [ ] The PRD and the rule files matched to the paths touched are read (`AGENTS.md` section 1, `.ai/rules/index.md`)
- [ ] The skills of section 5 that apply are named and used
- [ ] The person who meets this change and the moment they are most likely to be confused are named, and handled: the owner and the analysts who open the sample appraisals to learn how a form is filled, and who must never mistake a sample for a real appraisal (principle at the top of `AGENTS.md`)
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
