# Retro-check of briefs 0004 to 0014 against every rule

Status: approved
Approved: 2026-10-20

## Goal

The owner asked whether each brief from 0004 to 0014 really went through `.ai/rules/index.md` and every matching rule file and skill. It did not, systematically. This brief closes that gap: every rule that applies to what those briefs changed is checked with evidence, each violation is fixed with a test that failed first (or taken to the owner when it is a policy question), and the proof of reading is written down truthfully so the owner can trust the next brief.

## Reading done for this brief (opened and read in full in this session, before writing it)

- `AGENTS.md`, `memory/PRD.md`, `.ai/rules/index.md`.
- Project rules: `quality.md`, `general.md`, `testing.md`, `tests.md`, `laravel.md`, `php.md`, `audit-log.md`, `database.md`, `ui.md`, `js.md`.
- antislop skills: `antislop` (core), `antislop-ui`, `antislop-human`, `antislop-layoutmobile`, `antislop-copywriting`, `antislop-code`.
- Boost skills: `laravel-best-practices` (index plus `rules/` db-performance, architecture, routing, migrations, validation, security, eloquent, style), `testing-best-practices` (index plus `rules/` review, assertions, test-data, isolation, naming, endpoint-tests), `inertia-react-development`, `wayfinder-development`.
- Not read, so no claim is made about them: `tailwindcss-development` and the other `laravel-best-practices` rule files (queue-jobs, caching, http-client, error-handling, events-notifications, mail, scheduling, collections, blade-views, config, advanced-queries) and `testing-best-practices` rules `finding-features`, `security`, `performance`. If a finding needs one of them it is read first and named in the report.

antislop active: during (session override). Design Read: this is an internal admin application with a design system already fixed in `.ai/rules/js.md`; no new visual direction is introduced, dials ENERGY 1 / RHYTHM 1 / MOTION 1.

## Acceptance criteria

Each line is "met" or "not met" with evidence in the report.

- [x] A table lists, for each of the briefs 0004 to 0014, the files it changed (from git) and the rule files and skills that match those paths.
- [x] For every rule line of every matched file, the table says PASS, FAIL, or N/A with a file and line or a command output as evidence; nothing is ticked from memory.
- [x] Same-pattern searches are run over the whole code base (not only the files each brief touched) for the defect classes found: N+1 and queries in controllers, English-only messages, unvalidated decimals, writes that log even when nothing changed, soft-deleted holders, tests that depend on helpers of another test file, relations without a stated delete rule.
- [x] Every FAIL that is a plain violation is fixed, each with a test that fails before and passes after, or an exemption with a written reason where the rule allows one.
- [x] Every FAIL that changes policy (for example whether role, permission, and committee-level changes must carry a reason, `.ai/rules/php.md` item 8) is explained to the owner and decided by the owner before any change.
- [x] Documents and code that this check contradicts are fixed in both directions (the dangling and stale lines of `memory/PRD.md` and `CLAUDE.md` found while reading).
- [x] The screen checks that earlier briefs claimed are redone for real on the screens those briefs changed, at 1280 x 800, 768 and 375 (`resize_window`, then reset to `desktop`), with `scrollWidth` equal to the screen width, one `h1`, no control without a name, and the console clear. Screens that cannot be checked in this session are listed as not checked, not as passed.
- [x] The report follows `AGENTS.md`: `Brief: 0015-... (approved <date>)`, a `Docs <-> code:` line, the nine production questions answered, the person who meets the change named, antislop Delivery Gate as PASS/FAIL lines with evidence, what changed, what was verified, what was skipped, next steps.

## Out of scope

New features. PHPStan level 8 (own brief, already in the PRD). Audits of features 2 to 23 (deferred by the owner until they are adjusted). Making "rules read" machine-checkable in briefs (a separate brief that needs the owner's yes after this one closes).

## Assumptions and open questions

- A rule line that cannot be judged from code (for example the owner's design taste) is marked N/A with that reason, not PASS.
- Old tests that follow a project convention but conflict with `testing-best-practices` are reported once as a pattern, not rewritten (that skill says so), unless they break `.ai/rules/testing.md`.
- Starting suspicions, none proven yet: bulk-ticked earlier checklists and browser checks claimed at widths not really tested; PRD changelog style and stale lines; N+1 in `ReferenceController::usageTotal` and `extraUsage`; queries and hand-made pagination inside controllers (`byUsage`, `byModule`, `recordView`); tests that fail when run alone; `audit_verified_heads.entry_id` without a foreign key or a stated reason; no `/ui-guideline` example for the `PageHeader` `badge` prop; Audit Logs list sorted by nothing without a stated reason; `WorkbookReader` exceptions beyond `IOException` on hostile files; `whereJsonContains` unverified on MySQL; no reason required for role, permission, and committee-level changes.

## Checklist

Every line is ticked `[x]`; a line that does not apply is ticked and ends with `N/A: reason`. Nothing is "done" while a `[ ]` remains. The rule behind each line is in the file it names; do not copy the rule here. A rule that the project adds (`AGENTS.md`, section 6) adds its own line here, under the group it belongs to.

Reading and scope

- [x] The PRD and the rule files matched to the paths touched are read (`AGENTS.md` section 1, `.ai/rules/index.md`)
- [x] The skills of section 5 that apply are named and used
- [x] The person who meets this change and the moment they are most likely to be confused are named, and handled (principle at the top of `AGENTS.md`)
- [x] Out of scope is respected; findings outside the task are reported, not fixed on the side

Design

- [x] The impact of a changed function, interface, field, or contract is searched (callers, screens, documents, tests) before the change
- [x] Who may do this is decided before the input is validated, and every input limit is explicit (`.ai/rules/quality.md`, questions 1 and 2) N/A: no endpoint or input was added; the changes move code and fix tests and text.
- [x] Every outcome has one defined response, and a failure leaves nothing half done (`.ai/rules/quality.md`, questions 3 to 5) N/A: no new outcome; the list and delete responses are unchanged and still covered by the tests that passed.
- [x] Concurrency, repetition, and cost are handled (`.ai/rules/quality.md`, questions 4 and 6)
- [x] The code is readable, upgradeable, extensible, and scalable as `.ai/rules/quality.md`, "Design quality", asks: small units, one place to add a case, nothing kept in a process, nothing a person would not have written
- [x] Nothing leaks, and what must be on record is on record (`.ai/rules/quality.md`, questions 7 and 8)
- [x] Project rule: the change is on the audit record, a new model uses `Auditable`, and a new relation has a foreign key with a deliberate delete rule (`.ai/rules/audit-log.md`, `.ai/rules/general.md`, "Integritas relasi") N/A: no data write was added or changed.
- [x] Project rule: a schema change is a new migration, never an edited one, and the database is not reset (`.ai/rules/database.md`) N/A: no schema change in this brief.
- [x] Project rule: code, comments, tests, and URLs are English; screen text is Indonesian (`.ai/rules/general.md`)

Screen (N/A without a screen)

- [x] States (loading, empty, error), words, and confirmation of irreversible actions follow the UI rules (`.ai/rules/ui.md`, and the project's `.ai/rules/js.md`: shared components, one value per column, server-side sort keys, inputs that enforce the server rule) N/A: no state or dialog was changed; only the guideline example, the h2 option and two sentences of text.
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
