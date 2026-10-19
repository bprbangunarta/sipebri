# Brief template

For whoever starts a feature or any change that touches behavior, a contract, the schema, or security. Copy this file to `docs/briefs/NNNN-short-title.md` (next number, kebab-case), fill it in, show it to the owner in chat, and change nothing else until the owner approves. This file is the only permanent file in the folder; any other file here is unfinished work.

How the brief is used is written once in `AGENTS.md`, section 3. The checklist below is the single list of what must hold before the work is called done.

---

# Audit of Data Peranan and Data Perizinan

Status: approved
Approved: 2026-10-20

## Goal

Prove that the management of roles and permissions is correct and safe, fix what the attack finds, each finding with a test that failed first, and close the ways a person could lose access without anybody noticing.

## Acceptance criteria

- [x] The whole surface is listed in the report with a status per item: roles (`/roles` list, `/roles/{role}` permission matrix, create, rename, save permissions, delete) and permissions (`/permissions` list, the generator of custom permissions, delete of a custom one); `RoleController`, `PermissionController`, `App\Support\Modules`, the `Permission` model, `RoleName`, `PermissionSeeder`, `RoleSeeder`, the meeting with `CodexUserSync::syncRole` and with committee tiers; `pages/roles/index.tsx`, `pages/roles/show.tsx`, `pages/permissions/index.tsx`; `AccessControlTest`, `PermissionGeneratorTest`, `RoleSeederTest`, `AuditLogTest`.
- [x] The nine production questions (`.ai/rules/quality.md`) are answered per action; who may act is checked before validation and every input limit is explicit.
- [x] Roles: hostile input (empty name, edge spaces, length, Unicode, markup, array), rename or delete of a role held by an active and by a deactivated person, the system roles of `RoleName`, the Super Admin role, two requests at the same moment; every outcome is one Indonesian sentence, never a server error.
- [x] A role that the code refers to by name (`RoleName`) can be neither renamed nor deleted, and another role cannot be renamed while any person, active or deactivated, holds it, because Codex matches by name (owner decision); deleting a role never strips the role of a deactivated person silently.
- [x] Permissions: saving the matrix (a "manage" pulls in its "view", custom permissions, names outside the lists are refused), saving without a change, the generator (hostile entity, clash with a system permission, repeated actions), deleting a custom permission that a role holds and one that is free.
- [x] The audit record is proven by a test for every outcome (created, renamed, deleted, permissions changed, permission generated, permission deleted), and nothing is written when nothing changed.
- [x] Every data column of both lists sorts on the server through a whitelist and a test in both directions.
- [x] Every message shown to a person is Indonesian.
- [x] Checked in a browser at 1280 px and 375 px with the console clear; the antislop Delivery Gate is answered as PASS/FAIL with evidence.
- [x] Every finding has a test that fails before the fix and passes after; a finding that changes policy is explained to the owner first.
- [x] Documents and code are compared in both directions and fixed; `scripts/verify.sh` is green.

## Out of scope

New permissions for the workflow modules, a different model of access control (roles and permissions stay Spatie), an audit of the Codex sign-in, the roles inside committee tiers (brief 0008).

## Assumptions and open questions

Owner's decision of 2026-10-20: roles named in `RoleName` cannot be renamed or deleted at all; other roles cannot be renamed while anybody holds them. Starting suspicions, none proven yet: (1) renaming a role held by people makes Codex turn them into Guest at the next sign-in; (2) deleting a role held only by deactivated people strips their role silently; (3) the roles of `RoleName` can be renamed or deleted while unused; (4) a name with edge spaces passes the unique check and then collides in the database; (5) the rename error is in English; (6) saving nothing new still writes an audit entry; (7) the permission list cannot be sorted.

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
