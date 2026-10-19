---
paths:
    - 'tests/**'
    - 'test/**'
    - '**/*.test.*'
    - '**/*.spec.*'
---

# Tests

- Test behavior and contracts, not implementation: a test must still pass when the code is rewritten and the behavior is the same.
- One test per decision and per failure mode, including every refusal. A test that cannot fail is not a test.
- A bug fix starts with a test that fails for the right reason (`.ai/rules/quality.md`, "Bug fixes").
- Tests do not depend on real time, the network, the order they run in, or data left by another test.
- Do not delete, skip, or loosen a test to get green (the gate refuses it: `scripts/check-weakened.sh`). Do not delete tests or test files without the owner's approval.
- Run the narrowest set of tests that covers the change first, then everything (`scripts/verify.sh`). A failing test is reported with its output; a test that was not run is said so.
- Guard the rules, not only the code: where a rule can be checked by a machine (documents against code, a size limit, a naming rule), write the check once and keep it in `scripts/verify.steps`, so the rule is enforced and not only written.
- The language of test names is a project rule (`AGENTS.md` or a project rule file).
