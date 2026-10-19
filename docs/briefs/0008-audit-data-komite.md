# Brief template

For whoever starts a feature or any change that touches behavior, a contract, the schema, or security. Copy this file to `docs/briefs/NNNN-short-title.md` (next number, kebab-case), fill it in, show it to the owner in chat, and change nothing else until the owner approves. This file is the only permanent file in the folder; any other file here is unfinished work.

How the brief is used is written once in `AGENTS.md`, section 3. The checklist below is the single list of what must hold before the work is called done.

---

# Audit and UI standardisation of Data Komite

Status: approved
Approved: 2026-10-20

## Goal

Prove that the functions of Data Komite are correct and safe, and bring every page, button, and dialog of it to the UI rules (`.ai/rules/js.md`, `ui.md`). Behaviour does not change except where a functional finding is fixed.

## Acceptance criteria

- [x] The whole surface is listed in the report with a status per item: the four tabs (Jenjang, Aturan khusus, Anggota, Pengecualian), the path detail page, the dialogs (change levels, add and change a path, add and change a tier, delete confirmations, member NIK, release member, authority check), and every button; the 17 routes; `CommitteeController`, `CommitteeMemberController`, `App\Support\{Committee,CommitteeLevels,CommitteeMembers}`, `CommitteePath`, `CommitteeTier`, `CommitteeSeeder`, the NIK columns of users; who uses committees (approval flow, loan applications, conflict of interest); `pages/committees/*.tsx` and `components/committee-tabs.tsx`; existing tests.
- [x] Function: the nine production questions (`.ai/rules/quality.md`) are answered per action; hostile and boundary input is tried (negative or overlapping ranges, a tier without a role, a role deleted while used, moving a tier at either end, a duplicate path); concurrent actions are tried; the audit record of every outcome is proven by a test; paths and tiers that a running approval holds are protected.
- [x] UI: every button and dialog on these pages is checked one by one against the rules: shared components, `h-8`, footer order (cancel left, confirm right), button words, one icon style within a toolbar, confirmation of what cannot be undone, focus and Escape, loading, empty and error states, sort on every data column of a sortable table, one row-action pattern, the same header pattern on every tab (the main action of that tab at the right, "Cek wewenang" on every tab).
- [x] The tab bar stays a routed navigation (one page per tab) and is checked to look and behave like the kit `Tabs`; if it differs, it is fixed, and the reason it is not the kit component is written down.
- [x] Checked in a browser at 1280 px and at 375 px with the console clear; the antislop Delivery Gate is answered as PASS/FAIL with evidence.
- [x] Every functional finding has a test that fails before the fix and passes after; every UI fix has a test or a machine check where one can exist.
- [x] A finding that changes policy or the way people work is explained to the owner first.
- [x] Documents and code are compared in both directions and fixed; `scripts/verify.sh` is green.

## Out of scope

Changes to the business rules of committees (levels, limits, mechanisms), the use of committees in the credit flow itself, close family of an applicant (an open question in `memory/PRD.md`).

## Assumptions and open questions

Owner's decisions of 2026-10-20: one header pattern for every tab; "Cek wewenang" is a fixed button on every tab; the owner has no complaint about the dialogs' header, body, and footer where they already follow the standard, so dialogs are changed only where they differ from it. The tab bar is routed navigation and is kept as such unless it differs from the kit look.

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
- [x] Project rule: a schema change is a new migration, never an edited one, and the database is not reset (`.ai/rules/database.md`) N/A: no schema change
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
