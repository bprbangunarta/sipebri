# Brief template

For whoever starts a feature or any change that touches behavior, a contract, the schema, or security. Copy this file to `docs/briefs/NNNN-short-title.md` (next number, kebab-case), fill it in, show it to the owner in chat, and change nothing else until the owner approves. This file is the only permanent file in the folder; any other file here is unfinished work.

How the brief is used is written once in `AGENTS.md`, section 3. The checklist below is the single list of what must hold before the work is called done.

---

# Audit of Data Kantor (offices)

Status: approved
Approved: 2026-10-20

## Goal

Prove that Data Kantor works correctly and safely in every real situation (change, delete, in use, Codex sync, hostile input, small screen), and fix what the attack finds, each finding with a test that failed first.

## Acceptance criteria

- [x] The whole surface is listed in the report with a status per item: screen and routes `/references/offices` (list, add, change, delete, sort, search, paging); `ReferenceController`, `ReferenceResource`, `pages/references/index.tsx` (shared, so findings also reach Data Wilayah); `Office` model, `config/references.php` entry, the three migrations, `CreditReferenceSeeder`, `UserSeeder`; `CodexUserSync::syncOffice`; consumers (loan application form and validation, Data Pengguna, scheduling order, dashboard by office, committee members, profile); menu and Super Admin gate; existing tests.
- [x] The nine production questions (`.ai/rules/quality.md`) are answered per action; who may act is checked before validation and every input limit is explicit.
- [x] Hostile input is tried and handled: spaces, Unicode, overlong values, HTML, `%` and `_` in search, wild `sort` and `per_page`, unknown ids, double submit.
- [x] The guarantees are attacked on purpose: delete an office used by an active user, a deactivated user, an active application, and a soft-deleted application; change the code of an office that is in use; two writes at the same moment. Every outcome is one Indonesian sentence, never a server error.
- [x] The meeting with Codex is tried: a login with a new office, a changed alias, a clashing alias, a long code, and an office edited by hand. The result matches a policy the owner has decided; a finding that changes policy is explained to the owner first.
- [x] The audit record is proven by a test for every outcome (created, changed, delete refused, Codex sync).
- [x] Screen: loading, empty, error, and refused-delete states; checked in a browser at 1280 px and 375 px with the console clear; the antislop Delivery Gate is answered as PASS/FAIL with evidence.
- [x] Every finding has a test that fails before the fix and passes after.
- [x] Documents and code are compared in both directions and fixed; `scripts/verify.sh` is green.

## Out of scope

Data Wilayah (brief 0006), Excel import for offices, a full audit of the Codex login (feature 1 is done).

## Assumptions and open questions

Starting suspicions, none proven yet: (1) deleting an office that only soft-deleted users or applications still hold may end in a server error because the usage count skips them while the foreign key does not; (2) `alias` is nullable in the database but required in the form; (3) the code of an office in use can be changed while Codex matches offices by code; (4) a Codex login overwrites name and alias edited by hand (policy); (5) form limit of 8 characters for the code versus what Codex may send. Findings 3 and 4 are policy and go to the owner before any change.

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
