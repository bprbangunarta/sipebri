---
paths:
    - 'app/**'
    - 'routes/**'
    - 'config/**'
    - 'bootstrap/**'
    - 'database/**'
---

# Laravel

The Laravel specifics of the quality bar in `.ai/rules/quality.md`. Use the Laravel Boost guidance and the `laravel-best-practices` skill for how to write Laravel well; this file is only what the project adds on top. Follow the pattern of the sibling files first.

- **Authorization before validation.** A route is guarded by middleware or the Form Request's `authorize()`, so a stranger learns nothing, not even whether an id exists.
- **Validate with Form Requests** and return the framework's validation response; never a raw stack trace. Models name their fillable columns (never an empty `$guarded`), and the signed-in person is taken from one place (a base-controller method or the request), never guessed from a nullable `$request->user()`.
- **Controllers stay thin:** authorize, validate, call, answer. Rules and queries live in a model, a query class, or a service named for what it does; a controller never reads a table with `DB::table`.
- **Configuration:** `env()` is read only inside `config/*.php`. `.env.example` holds the variables the code reads and nothing else (an optional one is a commented-out `# NAME=value` line); explanations belong in the deployment document, not in the file.
- **Migrations are immutable.** Never edit a migration that has been migrated in any environment: write a new one. A migration works with the code that runs before the deploy as well as with the new code (add first, move data, remove later); one that destroys data or cannot be undone is raised with the owner before it is written. Seeders and factories hold invented data only: never a copy of production, and never a real or default password.
- **Performance:** no query in a loop (eager load or batch), lists are paginated with a cap, filters and sorts use an index.
- **Before finishing:** `vendor/bin/pint --dirty`, `vendor/bin/phpstan analyse`, the tests of the change, then the whole gate (`scripts/verify.sh`).
