# Push does not repeat a gate that already passed on the same content

Status: approved
Approved: 2026-10-21

## Goal

A push no longer waits for the whole gate a second time when the gate has just been green on exactly the content that is being pushed. Today `scripts/verify.sh` runs once before the commit (as the work requires) and again in the pre-push hook for the same code, which takes minutes (about two minutes for the tests alone) and checks nothing new. Nothing that the gate guarantees is given up: the commit rules for the pushed commits still run every time, a changed file, a different commit, an old stamp, or a failed run always means the full gate, and CI (`.github/workflows/ci.yml`) still runs the whole gate on every push.

## Acceptance criteria

Each line can be answered "met" or "not met" with evidence (a test, a command, a screen checked in a browser).

- [x] **A green run leaves a stamp, only for committed content.** When `scripts/verify.sh` runs every step of `scripts/verify.steps` and all are green, and the content checked is exactly the committed one (no tracked file differs from `HEAD`, no untracked file outside `.gitignore` matches `CODE_PATHS` of `devcore.conf`, so an untracked `.DS_Store` or a document does not count), it writes a stamp inside `.git` (never committed, never pushed) with the tree of `HEAD` and the time. A run that fails at any step, or is interrupted, removes the stamp; a green run on content that is not exactly the committed one writes none.
- [x] **A stamp is only trusted for exactly what was checked.** The pre-push hook skips the steps of `verify.steps` only when every commit being sent has the same tree as the stamp (the tree contains `verify.steps` and `devcore.conf` too, so a changed list of steps or a changed setting is a different tree) and the stamp is not older than `VERIFY_STAMP_HOURS` (new setting in `devcore.conf`, default 24, so a security advisory published since the audit steps ran is not missed for long). Any other case runs the whole gate as today.
- [x] **The commit rules always run.** The weakened-check and brief-line checks of the pushed range (`check-weakened.sh`, `check-brief.sh`) run on every push, stamp or not, because they depend on which commits are sent and not only on the tree.
- [x] **Only the push reuses.** A manual `scripts/verify.sh`, `composer verify`, and CI always run every step; only the pre-push hook sets the variable that allows reuse. The hook says plainly, in the project's message language, that the steps were skipped because the gate was green on this exact content, and when.
- [x] **No new way around the gate.** There is no option that skips the steps without a stamp; `--no-verify` stays forbidden as before; forging a stamp is no weaker than `--no-verify`, which already exists, and CI is the check that does not trust a local file.
- [x] **The gate's own self-test proves it** (`scripts/selftest.sh`, with real git in a throwaway repository): a green run writes the stamp; the push right after a green run skips the steps but still checks the commits; a push after a new commit, an expired stamp, or a failed run runs the whole gate; a run with an edited tracked file or an untracked code file leaves no stamp; an untracked `.DS_Store` does not stop the stamp; a stamp does not excuse a pushed commit that has no brief; a manual run never reuses.
- [x] **Docs.** `AGENTS.md` (section 4, git and autonomy), `CLAUDE.md` if it describes the gate, `memory/PRD.md` (the decision and the deviation from the standard devcore scripts, one line each), and the devcore feedback list in the PRD are brought in line with the change; the new setting is documented once, in `devcore.conf`.

## Out of scope

Making the steps themselves faster (running the tests in parallel, caching the build, trimming the audits). Changing what CI runs. The commit-msg hook. Any change to the rules the gate enforces.

## Assumptions and open questions

- `scripts/verify.sh`, `.githooks/pre-push`, `scripts/selftest.sh`, and `scripts/lib/msg.sh` are standard devcore files. This change deviates from the standard on purpose; it is recorded in the PRD as a deviation and added to the list of feedback for devcore, so that it can be taken upstream and the next devcore update does not silently undo it.
- The stamp lives in `.git` (for example `.git/devcore-verified`) so it is never committed and every clone starts without one. A fresh clone therefore runs the full gate on its first push.
- 24 hours is the default life of a stamp: long enough for one working day, short enough that the dependency audits are repeated daily. The value is one setting in `devcore.conf`.
- The order of work that benefits most: finish the commits (including the commit that closes a brief), run `scripts/verify.sh` once on that final content, then push. A commit made after the green run changes the tree, and the push then runs the whole gate again.

## Checklist

Every line is ticked `[x]`; a line that does not apply is ticked and ends with `N/A: reason`. Nothing is "done" while a `[ ]` remains. The rule behind each line is in the file it names; do not copy the rule here. A rule that the project adds (`AGENTS.md`, section 6) adds its own line here, under the group it belongs to.

Reading and scope

- [x] The PRD and the rule files matched to the paths touched are read (`AGENTS.md` section 1, `.ai/rules/index.md`)
- [x] The skills of section 5 that apply are named and used
- [x] The person who meets this change and the moment they are most likely to be confused are named, and handled: whoever pushes and waits, and the moment a push is skipped that should not have been (principle at the top of `AGENTS.md`)
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
