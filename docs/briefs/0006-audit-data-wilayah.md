# Brief template

For whoever starts a feature or any change that touches behavior, a contract, the schema, or security. Copy this file to `docs/briefs/NNNN-short-title.md` (next number, kebab-case), fill it in, show it to the owner in chat, and change nothing else until the owner approves. This file is the only permanent file in the folder; any other file here is unfinished work.

How the brief is used is written once in `AGENTS.md`, section 3. The checklist below is the single list of what must hold before the work is called done.

---

# Audit of Data Wilayah (regions)

Status: approved
Approved: 2026-10-20

## Goal

Prove that Data Wilayah is correct and safe on data of real size (about 82,000 villages) and in its relation to collaterals, and fix what the attack finds, each finding with a test that failed first.

## Acceptance criteria

- [x] The whole surface is listed in the report with a status per item: screen and routes `/references/regions` (list, add, change, delete, sort, search, paging); `ReferenceController`, `ReferenceResource`, `pages/references/index.tsx` (shared; the findings common to all tables were fixed in the audit of Data Kantor); `Region` model, the `regions` entry in `config/references.php`, the migration and its indexes, `CreditReferenceSeeder::regions()` and `database/data/regions.csv.gz`; consumers (`collaterals.region_code` and `region_label`, the collateral picker built from `regions`, the loan application collateral rules); menu and Super Admin gate; existing tests.
- [x] The nine production questions (`.ai/rules/quality.md`) are answered per action; who may act is checked before validation and every input limit is explicit.
- [x] Hostile input is tried and handled: postal code, very long names, Unicode, markup, `%` and `_`, wild `sort` and `per_page`, unknown ids, double submit.
- [x] The guarantees are attacked on purpose: delete or change a region whose code a collateral uses (a deleted collateral too), delete the last village of a code, two writes at the same moment. Every outcome is one Indonesian sentence, never a server error. A finding that changes policy is explained to the owner first.
- [x] Speed is measured with data of the real size (list, search, sort, the collateral picker) and the numbers are in the report; a slow path is fixed or reported with its number.
- [x] The audit record is proven by a test for every outcome.
- [x] Screen: loading, empty, error, and refused-delete states; checked in a browser at 1280 px and 375 px with the console clear; the antislop Delivery Gate is answered as PASS/FAIL with evidence.
- [x] Every finding has a test that fails before the fix and passes after.
- [x] Documents and code are compared in both directions and fixed; `scripts/verify.sh` is green.

## Out of scope

Excel import for regions, refreshing the 82,000 rows from an outside source, a full audit of the collateral form.

## Assumptions and open questions

Starting suspicions, none proven yet: (1) a region whose code a collateral uses can be deleted, because `usage` is empty and the link is a code without a foreign key; (2) many villages share one code, so changing or deleting one village can leave a collateral pointing at a code that no row has; (3) the collateral picker groups `code, regency` with `distinct`, which doubles an entry when one code is written two ways; (4) search over five unindexed columns and the total count may be slow at 82,000 rows; (5) nothing stops a duplicate regency + district + village. Findings 1 and 2 are policy and go to the owner before any change.

## Checklist

Every line is ticked `[x]`; a line that does not apply is ticked and ends with `N/A: reason`. Nothing is "done" while a `[ ]` remains. The rule behind each line is in the file it names; do not copy the rule here. A rule that the project adds (`AGENTS.md`, section 6) adds its own line here, under the group it belongs to.

Reading and scope

- [x] The PRD and the rule files matched to the paths touched are read (`AGENTS.md` section 1, `.ai/rules/index.md`)
- [x] The skills of section 5 that apply are named and used
- [x] The person who meets this change and the moment they are most likely to be confused are named, and handled (principle at the top of `AGENTS.md`)
- [x] Out of scope is respected; findings outside the task are reported, not fixed on the side

Design

- [x] The impact of a changed function, interface, field, or contract is searched (callers, screens, documents, tests) before the change
- [x] Who may do this is decided before the input is validated, and every input limit is explicit (`.ai/rules/quality.md`, questions 1 and 2)
- [x] Every outcome has one defined response, and a failure leaves nothing half done (`.ai/rules/quality.md`, questions 3 to 5)
- [x] Concurrency, repetition, and cost are handled (`.ai/rules/quality.md`, questions 4 and 6)
- [x] The code is readable, upgradeable, extensible, and scalable as `.ai/rules/quality.md`, "Design quality", asks: small units, one place to add a case, nothing kept in a process, nothing a person would not have written
- [x] Nothing leaks, and what must be on record is on record (`.ai/rules/quality.md`, questions 7 and 8)
- [x] Project rule: the change is on the audit record, a new model uses `Auditable`, and a new relation has a foreign key with a deliberate delete rule (`.ai/rules/audit-log.md`, `.ai/rules/general.md`, "Integritas relasi")
- [x] Project rule: a schema change is a new migration, never an edited one, and the database is not reset (`.ai/rules/database.md`) N/A: no schema change in this brief (the sort-index migration of brief 0004 was run on the real data and works)
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
