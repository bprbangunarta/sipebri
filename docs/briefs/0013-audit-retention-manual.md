# Brief template

For whoever starts a feature or any change that touches behavior, a contract, the schema, or security. Copy this file to `docs/briefs/NNNN-short-title.md` (next number, kebab-case), fill it in, show it to the owner in chat, and change nothing else until the owner approves. This file is the only permanent file in the folder; any other file here is unfinished work.

How the brief is used is written once in `AGENTS.md`, section 3. The checklist below is the single list of what must hold before the work is called done.

---

# Audit log retention is done by hand, never by the system

Status: approved
Approved: 2026-10-20

## Goal

The retention of the audit log (5 years) is the owner's own policy, carried out outside the system. The system therefore never deletes audit entries on its own; when the owner deletes old entries, the command that does it keeps the hash chain verifiable.

## Acceptance criteria

- [x] `audit:prune` is no longer on the schedule (`routes/console.php`); `audit:verify` stays daily; a test proves the one is scheduled and the other is not.
- [x] `audit:prune` runs only when somebody runs it: it asks for confirmation before it deletes, and a run without a terminal refuses unless `--force` is given; `--dry-run` never asks and never deletes.
- [x] Deleting with it still leaves the anchor so that `audit:verify` stays green, and the run is itself recorded (existing behaviour, kept by its tests).
- [x] `AUDIT_RETENTION_YEARS` is described as the number of years the manual command keeps, not as an automatic rule.
- [x] The documents (`docs/deployment.md`, `CLAUDE.md`, `memory/PRD.md`, `config/security.php`) say that retention is done by hand with `audit:prune`, and the open question about retention is closed as decided.
- [x] `scripts/verify.sh` is green.

## Out of scope

The Audit Log screen (its own audit later), changing the retention period, the audit of other data.

## Assumptions and open questions

Owner's decision of 2026-10-20: the 5-year policy exists and is carried out by the owner directly on the data, not by the system; the owner chose to use `audit:prune` by hand (it writes the anchor) rather than delete with SQL.

## Checklist

Every line is ticked `[x]`. Reading and scope, design, tests and documents, and the gates are covered by the criteria above; the rules behind them are in the files they name.

- [x] Documents and code are compared in both directions, and `memory/PRD.md` is updated
- [x] `scripts/verify.sh` is green and the build runs
- [x] The owner is told what changed, what was not done, and why; this brief is deleted in the closing commit
