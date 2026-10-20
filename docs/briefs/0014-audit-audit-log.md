# Brief template

For whoever starts a feature or any change that touches behavior, a contract, the schema, or security. Copy this file to `docs/briefs/NNNN-short-title.md` (next number, kebab-case), fill it in, show it to the owner in chat, and change nothing else until the owner approves. This file is the only permanent file in the folder; any other file here is unfinished work.

How the brief is used is written once in `AGENTS.md`, section 3. The checklist below is the single list of what must hold before the work is called done.

---

# Audit of the Audit Log screen

Status: approved
Approved: 2026-10-20

## Goal

Prove that the Audit Log screen is correct, resists abuse, and is honest about what it proves, and fix what the attack finds, each finding with a test that failed first.

## Acceptance criteria

- [x] The whole surface is listed in the report with a status per item: `/audit-logs` (list, filters, detail, paging), `/audit-logs/export`, `/audit-logs/verify`; `AuditLogController`, `App\Audit\{Audit,AuditLog,AuditVerifier,Auditable}`, the commands `audit:verify` and `audit:prune`, the tables `audit_logs` and `audit_anchors` with their indexes, `pages/audit-logs/index.tsx`, `AuditLogTest`.
- [x] The nine production questions (`.ai/rules/quality.md`) are answered per action (view, filter, export, verify); who may act is checked before validation and every input limit is explicit.
- [x] The chain is attacked on purpose: change every hashed column, change a column outside the hash if there is one, insert a row, delete at the start without an anchor, delete in the middle, delete at the end, swap two rows, remove an anchor. Each attack is detected, or, where it cannot be, the document says so plainly.
- [x] A deletion at the end of the chain is detected: each verification records the last entry it checked (id and hash) in a table that can only be added to, and the next verification requires that entry to be still there (owner decision).
- [x] Opening the audit log is recorded as one entry `audit_logs.viewed`, at most once in ten minutes for the same person, so paging and filtering do not fill the log (owner decision).
- [x] The export is checked: content and columns, filters respected, formula characters neutralised in every text column, values with commas, quotes and new lines, Unicode, and its own audit entry.
- [x] The filters are attacked: dates that do not exist, from after to, unknown module and outcome, `%` and `_` in the search, wild `per_page`; the results are exactly right.
- [x] Speed is measured on tens of thousands of rows in MySQL (list, filter, export, verification) and the numbers are in the report; a slow part is fixed or reported with its number.
- [x] Checked in a browser at 1280 px and 375 px with the console clear (empty, error, a detail with long values); the antislop Delivery Gate is answered as PASS/FAIL with evidence.
- [x] Every finding has a test that fails before the fix and passes after; a finding that changes policy is explained to the owner first.
- [x] Documents and code are compared in both directions and fixed; `scripts/verify.sh` is green.

## Out of scope

The content of entries from other features (audited with their features), changing the retention (the owner does it by hand, brief 0013), moving the audit trail elsewhere.

## Assumptions and open questions

Owner's decisions of 2026-10-20: opening the audit log is recorded (once in ten minutes per person); the end of the chain is protected by a record of the last verified entry. Starting suspicions, none proven yet: (1) rows deleted at the end of the chain are not noticed; (2) the controller comment says looking is recorded, the code records only export and verification; (3) verification reads the whole table in one web request; (4) the list counts the whole table on every page and the module list reads it all; (5) cells that start with a space and then a formula character in the export.

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
