---
paths:
    - '**'
---

# Quality bar

How code is written in this project, whatever the language. It sits on top of the conventions of the stack (the stack's own rule file and skills). Consistency first: follow the pattern already used by the files beside the one you touch.

## Production, not prototype

No silent mocks, stubs, or placeholders: mark `TODO: <reason>` and tell the owner in the report. Never bypass a gate (`--no-verify`, a disabled lint or type rule, a suppression comment, a baseline entry); fix the root cause. `scripts/check-weakened.sh` catches the common ways of bypassing on every commit and every push: a skipped or focused test, a suppression comment, a new analysis baseline, a deleted test file, or a test file that loses cases. A line that must stay carries `weakening-allowed: <reason>`; a commit that weakens on purpose (a feature removed with its tests) carries a `Weakened: <reason>` line in its message; both need a real reason of at least ten characters. Technical debt is a conscious decision, recorded in `memory/PRD.md`.

## Errors and validation

No empty `catch` and no catch-all that hides failures: catch the narrowest error you can handle, then log or rethrow. The one accepted catch-all that neither logs nor rethrows is parsing hostile input where the only answer is a plain refusal, and it carries a comment that says so. Validate at the boundary and return a clear status and message, never a raw stack trace. Do not add validation or fallbacks for impossible cases.

## Secrets and configuration

No hardcoded secrets, credentials, or environment-specific addresses. Configuration that the application cannot run safely without is checked when it starts and stops it when missing or weak, never replaced by a quiet default that lowers security. Environment variables are read in one place (the configuration), and the example environment file is kept in step with the variables the code uses. No real or default passwords in seed data or code.

## Production standard (every line written, every line reviewed)

Nothing is written or approved as "works on the happy path". Before code is done, and again when it is reviewed, go through this list and answer each line for the code at hand (the answers go in the report; a line that does not apply says why):

1. **Who can do this, and to what?** Authorization is decided before validation and before a record is looked up, so a stranger learns nothing, not even whether an id exists. A person can never grant, change, or reach more than they hold.
2. **What input can arrive?** Missing, empty, wrong type, too long, too many, hostile, duplicated, in another encoding. Every limit is explicit: lengths, ranges, page sizes, row counts, file size and type. Hostile input is named by class, not only by shape: a path that climbs out of its folder, an address the program is told to open or redirect to (allow-list the scheme and the host), and anything built into a query, command, or template from raw text (never). The screen blocks what the server would refuse; the server never trusts the screen.
3. **What does every outcome look like?** Success, not found, refused, invalid, throttled, too large, conflict, crash: each has one fixed response. Nothing internal (exception text, class names, queries, paths) reaches a response or a log meant for people. Every kind of caller counts, not only the screen.
4. **What if two happen at once, or the same one twice?** Anything checked before it is acted on is checked under a lock or a constraint, and repeating a request or an import is safe (idempotent or refused clearly).
5. **What does a failure leave behind?** Writes that belong together are one short transaction; files, counters, locks, and half-linked rows are cleaned up; the person can retry at once.
6. **What does it cost, and how does it grow?** No query in a loop, every list is paginated with a cap, filters and sorts use an index, bulk work streams or chunks, every outside call has a connect and a total timeout, retries are few and spaced out and only for an operation that is safe to repeat, the answer of an outside system is checked before it is used, and its failure degrades one feature instead of taking the application down.
7. **What does it leak?** Secrets, personal data, and the existence of accounts in responses, logs, error messages, and timing.
8. **Is it on the record?** Whatever the project says must be on record (data changes, sign-ins, exports) is, with who, what, and why. A refusal is on the record too.
9. **Is each risk above proven by a test?** One test per decision and per failure mode, including the refusals.

A reviewer asks the same questions of someone else's diff and returns the first line that fails. "I did not think of it" is a finding, not an excuse; when a risk is accepted instead of fixed, it is written down in `memory/PRD.md` with the reason.

## Bug fixes

Fix the root cause, then search for the same pattern elsewhere and fix it too (or report it when large). The regression test is written first and seen to fail for the right reason, then to pass after the fix. A local patch that leaves duplicate bugs is not a fix. Prefer the structural fix over a small diff.

## Design quality

Write the code a careful colleague would write for a product that stays in production for years: nobody should be able to tell by reading it that a tool wrote it. The limits below are held by a check of the project where the stack allows it (a test or a linter rule in `scripts/verify.steps`); the rest is held by the report (the brief's "Design" line) and by review.

- **Readable and clean.** One reason to change per class or module. A function does one thing and fits on a screen: at most 80 lines (aim for 40) and four levels of nesting; a source file at most 400 lines (500 for front-end files). The entry point of a request stays thin: authorize, validate, call, answer; rules and queries live in a unit named for what it does. A name says what the thing is or does: `Helper`, `Util`, `Manager`, `Misc`, and `Common` are not names. Early returns over nested conditions; a flag that changes what a function does means two functions; a closed set of states is a type, not a string; no dead code.
- **Upgradeable.** Use the framework's public API and its documented extension points, never its internals and never a copied vendor file. A library is used through one unit of ours, so replacing it touches one file. Versions live in the dependency manifests, never in code. A value an operator may change is read from configuration; a value that is policy (a limit, a rule) is written once, in configuration or a named constant, with the decision recorded in `memory/PRD.md`. A deprecation notice is fixed when it is seen, not left for the upgrade.
- **Extensible.** A new case of something that already has a family is added in one place: a registry, an enum case, or a configuration row. If the Nth case means editing several files, or copying a branch and changing a string, build the registry first. What varies is chosen by data, a type, or an interface with implementations, not by a growing chain of conditions on a type name.
- **Scalable.** Question 6 of the production standard, and one more rule: nothing is kept in a process between requests (no static state beyond a request's own context), because the next request may be served by another process or another server; work that grows with the data streams or chunks, has a stated limit, and never loads everything.
- **Written by a person.** A comment says why, never what the next line does; no banners, no narration of steps (`antislop-code`). No abstraction for one use and no wrapper without a second user; no defensive code for a state that cannot happen; the same style as the files beside it. A rule that exists twice is extracted once (the second copy is the signal), and a fix is searched for in every copy. A check for copied code (for example `jscpd`) belongs in `scripts/verify.steps` when the stack has one.

Code that already breaks a limit when the project adopts this rule is listed in the project's check with a reason of at least ten letters (the honest debt, removed when the code is touched); a new exception needs the owner's agreement, and an entry that no longer matches anything fails the check so the list cannot rot.

## Comments, naming, and language

What a comment may say is `antislop-code`. Descriptive names; no premature abstraction (three similar lines beat a wrong early abstraction); no dead or commented-out code. The language of identifiers, comments, commit messages, and everything a person reads is a project rule: write it in `AGENTS.md` or a project rule file, once.

## Dependencies and scope

Do not add or change dependencies without approval. Prefer maintained packages and avoid heavy dependencies for small needs. The request for approval names the need, the alternatives considered, the licence, the state of maintenance, and what it means for security. A vulnerability audit of the dependencies belongs in `scripts/verify.steps`; a finding that cannot be fixed is written down as an accepted risk in `memory/PRD.md`, never silenced. Remove a dependency that nothing uses any more. Never run a script fetched from an unknown source (`curl | sh`).
