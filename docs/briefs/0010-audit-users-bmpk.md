# Brief template

For whoever starts a feature or any change that touches behavior, a contract, the schema, or security. Copy this file to `docs/briefs/NNNN-short-title.md` (next number, kebab-case), fill it in, show it to the owner in chat, and change nothing else until the owner approves. This file is the only permanent file in the folder; any other file here is unfinished work.

How the brief is used is written once in `AGENTS.md`, section 3. The checklist below is the single list of what must hold before the work is called done.

---

# Audit of Data Pengguna and BMPK

Status: approved
Approved: 2026-10-20

## Goal

Prove that the two screens, the list of people (Data Pengguna) and the lending limit (BMPK), are correct and safe, and fix what the attack finds, each finding with a test that failed first.

## Acceptance criteria

- [x] The whole surface is listed in the report with a status per item: Data Pengguna (`/users`, `UserController`, `pages/users/index.tsx`, `User` model and its soft delete, `UserSeeder` and its 28 starter people, the meeting with the Codex sync, menu and Super Admin gate, `UserSeederTest` and the parts of `AuthTest` and `AccessControlTest`); BMPK (`/references/bmpk` GET and PUT, `BmpkController`, `pages/references/bmpk.tsx`, `App\Support\LendingLimit`, `Setting` model, the seed value in `CreditReferenceSeeder`, the use in `LoanApplicationController`, `BmpkTest`).
- [x] The nine production questions (`.ai/rules/quality.md`) are answered per action; who may act is checked before validation and every input limit is explicit.
- [x] Data Pengguna: hostile input (role, status, `per_page`, `sort`, search with `%` and `_`), the empty, deactivated and large list; every data column sorts on the server through a whitelist and a test in both directions; no sensitive data (NIK, password) is shown; Super Admin only.
- [x] BMPK: limit input (zero, negative, text, fraction, too large, empty), two saves at the same moment, a save that changes nothing, and the effect on loan applications (draft, submitted, in progress) is proven by tests; the audit entry shows the value before and after and the reason.
- [x] A reason is required whenever the BMPK is changed (owner decision) and is kept in the audit entry.
- [x] The starter people of `UserSeeder` carry clearly invented names and addresses (owner decision), and the seeder stays idempotent.
- [x] Checked in a browser at 1280 px and 375 px with the console clear; the antislop Delivery Gate is answered as PASS/FAIL with evidence.
- [x] Every finding has a test that fails before the fix and passes after; a finding that changes policy is explained to the owner first.
- [x] Documents and code are compared in both directions and fixed; `scripts/verify.sh` is green.

## Out of scope

The full BMPK calculation (per borrower, group, capital), adding or deleting people (they come from Codex), an audit of the Codex sign-in.

## Assumptions and open questions

Owner's decisions of 2026-10-20: a reason is required for every change of the BMPK; the starter people get invented names. Starting suspicions, none proven yet: (1) an unknown role in the filter of Data Pengguna ends in a server error; (2) the list cannot be sorted and shows 10 rows where other lists show 25; (3) the BMPK can be changed without a reason; (4) behaviour of an empty BMPK and of a lowered BMPK against loan applications already in progress.

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
