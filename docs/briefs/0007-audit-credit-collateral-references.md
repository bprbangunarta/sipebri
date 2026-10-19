# Brief template

For whoever starts a feature or any change that touches behavior, a contract, the schema, or security. Copy this file to `docs/briefs/NNNN-short-title.md` (next number, kebab-case), fill it in, show it to the owner in chat, and change nothing else until the owner approves. This file is the only permanent file in the folder; any other file here is unfinished work.

How the brief is used is written once in `AGENTS.md`, section 3. The checklist below is the single list of what must hold before the work is called done.

---

# Audit of the credit and collateral reference tables

Status: approved
Approved: 2026-10-20

## Goal

Prove that the eight reference tables of the menus Data Kredit and Data Agunan (Produk, Bunga, Angsuran, Jenis, Klasifikasi, Pengikatan, Kondisi, Penilaian) are correct and safe in every real situation, and fix what the attack finds, each finding with a test that failed first.

## Acceptance criteria

- [x] The whole surface is listed in the report with a status per item: the tables `products`, `interest-methods`, `installments`, `collateral-types`, `collateral-classifications`, `collateral-bindings`, `collateral-conditions`, `collateral-valuations` (config entries, models, migrations, seeder); `ReferenceController`, `ReferenceResource`, `pages/references/index.tsx`; who refers to each table (loan applications, collaterals, committee paths and tiers, product parameters, approval decisions, the collateral form); menu and Super Admin gate; existing tests.
- [x] The nine production questions (`.ai/rules/quality.md`) are answered per table and action; who may act is checked before validation and every input limit is explicit.
- [x] For every table: delete while used (loan application, committee path, product parameter, approval decision, collateral, deleted ones too), change of a code that is used, two writes at the same moment, hostile input, unknown id. Every outcome is one Indonesian sentence, never a server error, and no other data changes silently.
- [x] Every relation that deletes or empties other data (`cascadeOnDelete`, `nullOnDelete`) has a guard in the app or a written reason why none is needed.
- [x] A table that no code refers to is reported, with a proposal (keep, use, remove) for the owner to decide.
- [x] The audit record is proven by a test for every outcome.
- [x] Screen: each table checked in a browser at 1280 px and 375 px with the console clear; states loading, empty, error, refused delete; the antislop Delivery Gate is answered as PASS/FAIL with evidence.
- [x] Every finding has a test that fails before the fix and passes after; a finding that changes policy is explained to the owner first.
- [x] Documents and code are compared in both directions and fixed; `scripts/verify.sh` is green.

## Out of scope

BMPK and Parameter produk (own controllers, separate briefs if wanted), Excel import for these tables, any change of features.

## Assumptions and open questions

Starting suspicions, none proven yet: (1) deleting an interest method or installment system that is a product default or is recorded on an approval decision empties those silently (`nullOnDelete`) while the guard counts loan applications only; (2) deleting a product cascades to its parameters and committee paths while the guard names loan applications and paths; (3) Penilaian and Klasifikasi have no usage guard and may have no consumer at all; (4) collateral type is referred to by code from collaterals and classifications; (5) product alias (12) and code (8) lengths against what the pickers and lists show; (6) table-specific rules, such as the code unique per collateral type in Klasifikasi.

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
