# Brief template

For whoever starts a feature or any change that touches behavior, a contract, the schema, or security. Copy this file to `docs/briefs/NNNN-short-title.md` (next number, kebab-case), fill it in, show it to the owner in chat, and change nothing else until the owner approves. This file is the only permanent file in the folder; any other file here is unfinished work.

How the brief is used is written once in `AGENTS.md`, section 3. The checklist below is the single list of what must hold before the work is called done.

---

# Import Data Resort from Excel

Status: approved
Approved: 2026-10-20

## Goal

A Super Admin adds or refreshes many resorts at once by uploading an Excel file that follows a downloadable template; adding one by one stays with the "Tambah" button. The button text "Tambah {label}" becomes "Tambah" on every reference table.

## Acceptance criteria

- [x] The header button and the empty-state button on every reference table read "Tambah" (the dialog title keeps the name); tests and screens that name the old text are updated.
- [x] Next to "Tambah" there is an "Import" button, only on tables whose entry in `config/references.php` has `'import' => true` (only Data Resort now).
- [x] "Import" opens a dialog with a link "Unduh template", an `.xlsx` file input, and an "Impor" button. The template is built from the table's fields (header row Kode, Nama; two example rows; a sheet "Petunjuk" with the limit of each column) and opens in Excel.
- [x] The whole file is checked before anything is saved: one bad row means nothing is saved, and the error lists row numbers with Indonesian reasons (empty code, code over 8 characters, name over 100 characters, a code twice in the file, ...). Every validation rule is the same one that guards the manual form (`ReferenceResource::rules`).
- [x] A code that already exists updates the name of that resort (the row keeps its id, so applications pointing at it stay valid); a row whose name is unchanged is counted as unchanged and writes nothing. The result says how many were created, updated, and unchanged.
- [x] Only Super Admin can download the template and import; the role check runs before the file is read.
- [x] Input limits: `.xlsx` only, at most 2 MB, at most 1,000 data rows; an unreadable, empty, macro-enabled, wrong-header, or oversize file is refused with a clear sentence. Cells that start with `=`, `+`, `-`, `@` are read as plain text, never run.
- [x] The import runs in one transaction; a failure (including a concurrent insert of the same code, caught from the unique index) saves nothing and returns a sentence, not a server error; the upload is not kept on disk.
- [x] Audit: each created or renamed resort is recorded through `Auditable`, plus one summary entry for the import (who, counts); a refused import is recorded too.
- [x] Relations: deleting a resort that applications use is refused by the app (message names the count) and by the database foreign key, with a test that fails first; an import never deletes or re-keys a resort.
- [x] After success a toast says how many were created and updated, and the list refreshes.
- [x] Tests cover: template content, successful import, every refusal above, access, formulas, a large and a broken file, audit entries, nothing saved on failure, renaming a resort that is in use, and the screen (buttons, dialog, error list).
- [x] Added at the owner's request: every reference table sorts by each data column (server-side whitelist from the config fields plus the usage count, both directions, tested; each sort column has an index through a new migration). Also at the owner's request: field and outline-button edges use the light `line` colour of the reference design (checkbox, radio, and switch keep `control`).
- [x] `CLAUDE.md`, `docs/`, and `memory/PRD.md` match the new behavior; checked in a browser at 1280 px and 375 px.

## Out of scope

Import for other reference tables (only the config key is prepared), export to Excel, a queue (1,000 rows run in the request), any change to the "Tambah" form, deleting rows that are missing from the file.

## Assumptions and open questions

Decided by the owner on 2026-10-20: "Tambah" applies to every reference table; the dependency `openspout/openspout` may be added (reads and writes `.xlsx` as a stream, never runs formulas); an existing code updates the name; 1,000 rows and 2 MB are enough. The key of a row is the field `code`. Nothing is open.

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
- [x] Project rule: a schema change is a new migration, never an edited one, and the database is not reset (`.ai/rules/database.md`) N/A: no schema change; the foreign key and unique index already exist
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
