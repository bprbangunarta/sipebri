# Brief template

For whoever starts a feature or any change that touches behavior, a contract, the schema, or security. Copy this file to `docs/briefs/NNNN-short-title.md` (next number, kebab-case), fill it in, show it to the owner in chat, and change nothing else until the owner approves. This file is the only permanent file in the folder; any other file here is unfinished work.

How the brief is used is written once in `AGENTS.md`, section 3. The checklist below is the single list of what must hold before the work is called done.

---

# Title of the change

Status: draft
Approved: (only after the owner approves in chat for this brief: write the date, YYYY-MM-DD)

## Goal

One or two sentences: what the person who uses this can do afterwards, and why.

## Acceptance criteria

Each line can be answered "met" or "not met" with evidence (a test, a command, a screen checked in a browser).

- [ ] ...

## Out of scope

What this change deliberately does not touch.

## Assumptions and open questions

Defaults chosen for small things, and questions for the owner (none left open when the brief is approved).

## Checklist

Every line is ticked `[x]`; a line that does not apply is ticked and ends with `N/A: reason`. Nothing is "done" while a `[ ]` remains. The rule behind each line is in the file it names; do not copy the rule here. A rule that the project adds (`AGENTS.md`, section 6) adds its own line here, under the group it belongs to.

Reading and scope

- [ ] The PRD and the rule files matched to the paths touched are read (`AGENTS.md` section 1, `.ai/rules/index.md`)
- [ ] The skills of section 5 that apply are named and used
- [ ] The person who meets this change and the moment they are most likely to be confused are named, and handled (principle at the top of `AGENTS.md`)
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
