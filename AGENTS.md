# Agent guide (read this first)

This guide is the project's standard way of working with a coding agent, whichever agent it is. It comes from devcore. Where the project already had something like it, devcore is followed; what is specific to the project is added as extra rules on top of it (section 6), and those rules are treated exactly like the standard ones.

**Project:** SIPEBRI. Stack: Laravel 13, Inertia v3, React 19, MySQL. Working branch: `v3.0.0-alpha`.

**Principle: people first.** Every change is judged first by what the person who meets it sees, understands, and can do next, and only then by whether it works. When two designs are equal for the machine, take the one that is kinder to the person.

## 1. Reading order

Read in this order, every task, every agent. A step is never skipped because the task looks small, and a file that is missing or unreadable is said so in the report, not passed over.

| Step | Read | What you take from it |
| --- | --- | --- |
| 1 | this file | the principles, the workflow (section 3), and where everything else lives (section 2) |
| 2 | `memory/PRD.md` | what exists, what was decided, what is open, what is next |
| 3 | the request, then the track | a feature or a change to behavior, contract, schema, or security gets a brief (`docs/briefs/TEMPLATE.md`); anything small says `Brief: none: <reason>` |
| 4 | `.ai/rules/index.md`, then every rule file whose paths match what you will touch | the project's conventions and rules, scoped by path |
| 5 | the skills of section 5 that apply, each read from its `SKILL.md` | deep, rule-by-rule guidance for a kind of work |
| 6 | the documents in `docs/` that answer your question | how a thing is meant to behave, before you read or change the code that does it |
| 7 | the code: sibling files first, then the thing itself | the pattern to follow; search for an existing implementation before writing a new one |

Steps 3 to 5 end in the brief: the rule files and skills you read are named there before the first edit, and its checklist points back to them. **Proof of reading** is the brief (or, for `Brief: none`, the same checklist answered in the report).

## 2. Which document answers which question

| Question | Document |
| --- | --- |
| How do I work here (workflow, git, autonomy)? | this file |
| How is code written here (quality, tests, dependencies)? | `.ai/rules/` (listed in `.ai/rules/index.md`) |
| How do I start a feature, and what must be true before I call it done? | `docs/briefs/TEMPLATE.md` |
| What exists now, what was decided, what is open, what is next? | `memory/PRD.md` |
| How does the gate work, and how do I change its settings? | `devcore.conf` and `scripts/verify.steps` |
| Which part of the code does what, and how do I run the app? | `CLAUDE.md` (table "Arsitektur singkat") |
| What do the ten management forms (`docs/Form … BPR Bangunarta.docx`) say, and how are the two appraisal forms built? | `docs/management-forms.md` (read it instead of the .docx files) |

Each fact lives in exactly one document; link instead of copying. Do not create other tracking or memory files (`notes.md`, `state.md`, `backlog.md`, ...); the only temporary exception is a brief in `docs/briefs/`, deleted when its work is finished. Ideas and follow-ups go to "Open questions" or "Next steps" in `memory/PRD.md`, or to the issue tracker.

## 3. Per-task workflow

0. **Brief first (features and any change to behavior, contract, schema, or security).** Copy `docs/briefs/TEMPLATE.md` to `docs/briefs/NNNN-short-title.md` (next free number), fill in the goal, acceptance criteria, out of scope, and assumptions, and propose it to the owner. Revise it until the owner approves in chat; only then set `Status: approved` and `Approved: <date>` and start the work. The brief's checklist is the single list of rules that must hold: nothing is called done while a line is `[ ]`; a line that does not apply is ticked with `N/A: <reason>`. When everything is ticked, delete the brief in the closing commit (history stays in git; a lasting decision becomes one line in `memory/PRD.md`). A small fix, a text change, or a documents-only change needs no brief file: the commit message carries `Brief: none: <reason>` and the report answers the same checklist. `docs/briefs/` holds only `TEMPLATE.md` at rest; any other file there is unfinished work. The hooks enforce this: `.githooks/commit-msg` (`scripts/check-brief.sh`) refuses a code commit without an approved brief or `Brief: none:`, and refuses to close a brief that still has `[ ]` lines; `scripts/check-weakened.sh` refuses a commit that makes a check weaker (a skipped test, a suppression, a deleted test, a test file that loses cases) unless it carries `weakening-allowed: <reason>` on the line or `Weakened: <reason>` in the message. These rules bind every agent the same way.
1. **Understand.** Clarify ambiguous or high-impact requests first. External references (screenshots, other projects, pasted code) apply only to the aspects the owner named: say what you adopt, keep the rest on this project's rules, and ask when a reference conflicts with a written rule.
2. **Load context and skills.** Do the reading order of section 1 and name the skills of section 5 you will use (or "none" and why) before the first edit.
3. **Implement** following the matched rule files and skills. After two or three failed attempts with the same approach, stop: say what you think the cause is and change the approach or ask, never repeat it or find a way around a check.
4. **Verify.** Go through the questions of "Production standard" in `.ai/rules/quality.md` and answer them in the report. Run what the matched rule files say (narrowest tests first, then everything). Done means verified, not "looks right": `scripts/verify.sh` is the whole gate. It runs the lines of `scripts/verify.steps`, it runs on every push through `.githooks/pre-push`, and CI runs it too, so a push made with `--no-verify` is caught afterwards. Name in one line the person who will meet this change and the moment they are most likely to be confused or blocked, and say what was done about it.
4a. **Cross-check documents and code (every change, without being asked).** A task is not done until the documents that describe what you touched were compared with the code, in both directions: read what each document claims, read the code that does it, and fix whichever is wrong in the same commit. A stale document is a defect. `tests/Feature/DocsMatchCodeTest.php` does the mechanical half on every run (every file, class, command, and variable that a document names exists); comparing what is said with what is done is yours.
5. **Record.** Update `memory/PRD.md` when what exists changes, a decision is made, or a question opens or closes. Rewrite in place; the file header has the rules.
6. **Commit and push** (section 4).
7. **Report:** first line `Brief: NNNN-short-title (approved <date>)` or `Brief: none: <reason>`, then a `Docs <-> code:` line (which documents were compared with which code, what was stale and fixed, or that nothing was), then what changed, what was verified, what was skipped and why, and next steps. Never leave mocks or TODOs silently.

## 4. Git and autonomy

- Work only on `v3.0.0-alpha`; never commit or push to `main`. Small logical commits whose message explains the why. Never force-push, never commit `.env`, credentials, or generated secrets, and never use `--no-verify`.
- A commit that touches code needs an approved brief or a `Brief: none: <reason>` line (step 0). Install the hooks once per clone with `git config core.hooksPath .githooks`; `scripts/verify.sh` refuses to run without them.
- After a verified task, commit; push only when the owner says so.
- Never reset the database (schema changes are new migrations) and never type the owner's own Codex credentials: sign in only with accounts the owner gives for testing.
- Do not ask permission for routine local actions (tests, local migrations, fixing findings you discover). Ask only for destructive or irreversible actions outside the repository, and for decisions that are the owner's: naming policy, product behavior, and dependencies the task does not clearly require (state any dependency you add in the report and in `memory/PRD.md`).

## 5. Skills and tools: when to use which

This section is the only place that says when a skill is used; a rule file or a skill description never repeats it. Skills live in `.ai/skills/<name>/SKILL.md`. An agent that publishes skills to a folder of its own (for example `.claude/skills/`) does so from there; an agent without one reads the `SKILL.md` before the first edit of that kind of work. `scripts/check-registry.sh` fails when a skill folder is not named here, has no `SKILL.md`, or no longer matches its `MANIFEST.sha256` (a vendored skill is replaced by a newer release, never edited by hand).

### antislop: the filter for screens, text, people, mobile layout, and code comments

| Skill | Use it when | Do not use it when |
| --- | --- | --- |
| `antislop` (core) | together with any skill below, before the first edit of that kind of work | the task touches none of the concerns below |
| `antislop-ui` | building or editing a screen or component: color, layout, components, decoration, motion, and app screens | backend-only work |
| `antislop-copywriting` | writing or editing text a person reads: labels, messages, emails, error pages, and the prose of documents | code comments (use `antislop-code`) |
| `antislop-human` | UI work that must stay usable by everyone: contrast (run `.ai/skills/antislop-human/contrast-check.py`, never assert a pass), keyboard, focus, states | no UI is touched |
| `antislop-layoutmobile` | a layout that has to reflow between phone and desktop widths | no layout change |
| `antislop-code` | writing or editing code comments | no comment is written or touched |

- **Mode:** antislop is used DURING the work (apply the rules while writing). Announce `antislop active: during (session override).` once per session before the first edit. A full audit of existing work (numbered findings in `anti-slop/`) runs only when the owner asks.
- **Direction.** antislop filters slop and needs a direction to filter against: write the project's standing design choices once in the UI rule file and name any rule that collides with them to the owner, never follow or override it silently.
- **Gate in the report:** work on screens, text, and comments ends with the antislop Delivery Gate as PASS/FAIL lines with evidence, after the `Docs <-> code:` line.

### Stack: Laravel

The Laravel rules are in `.ai/rules/laravel.md`. Laravel Boost (`php artisan boost:install`) generates its own guideline block and the `laravel-best-practices`, `testing-best-practices`, and `tailwindcss-development` skills for the agents it supports; they are generated, so they are not committed here. An agent that has no skill folder reads them from the Boost package. Its block is at the end of this file under its own heading (`php artisan boost:update` regenerates it; never edit it by hand).

| Skill | Use it when | Do not use it when |
| --- | --- | --- |
| `laravel-best-practices` | writing, changing, or reviewing PHP: controllers, models, migrations, requests, policies, jobs, commands, services, queries, config | the task touches no PHP |
| `testing-best-practices` | writing, fixing, or reviewing any test | no test is added or changed |

### Stack: React and TypeScript

The front-end rules are in `.ai/rules/ui.md` (the standard ones) and `.ai/rules/js.md` (the project's own, taken from react-starter). `scripts/check-standards.mjs` holds what a machine can check. The gate runs `npx jscpd` (a block of 20 lines or more copied is refused; set in `.jscpd.json`), `npm run check` (format, lint, Vitest), and `npm run types:check`.


### Skills of the project

Add a row here for every skill the project adds (a folder in `.ai/skills/`), with when to use it and when not. A skill added by the project is registered and checked like the ones above.

Skills that Laravel Boost publishes for this project's stack (generated into `.claude/skills/` by `php artisan boost:update`, listed in `boost.json`, not committed):

| Skill | Use it when | Do not use it when |
| --- | --- | --- |
| `inertia-react-development` | pages, forms, navigation, deferred props, `useForm`, `router` in `resources/js` | backend-only work |
| `wayfinder-development` | a screen calls a route or controller action (`@/actions`, `@/routes`) | no route is called from a screen |
| `tailwindcss-development` | writing or fixing Tailwind classes in `resources/js` | backend-only work |
| `infer-conventions` | the owner asks (`/infer-conventions`), to record real conventions into `.ai/rules` (needs 3 or more consistent examples) | not asked |

## 6. Rules: the standard ones and the project's own

devcore is the standard way of working. The standard rules are in `.ai/rules/` (`quality.md` and `testing.md`, plus one file per stack layer), the standard skills in `.ai/skills/`, and the standard gates in `.githooks/` and `scripts/`. They are followed as written.

- **When the project already had something similar** (its own contribution guide, review checklist, or lint setup), devcore replaces it: keep one way, the standard one, and move only what is genuinely different into a project rule.
- **A rule that belongs only to this project** (a business rule, a security policy, a naming convention, a compliance duty) is added after devcore is fully installed and its gate is green. It is added the same way as a standard rule, and from then on it is applied the same way:
  1. one file in `.ai/rules/<name>.md`, with the paths it covers in its header;
  2. one row for it in `.ai/rules/index.md` (the registry check fails otherwise);
  3. when it can be checked, one line in the brief checklist that points to it, so a brief cannot be closed without answering it;
  4. when it can be checked by a machine, a check in `scripts/verify.steps` or a test, so it is enforced and not only written;
  5. any exception to it is listed with a reason and a date, never silent.
- When a project rule and a standard rule disagree, name the conflict to the owner; do not choose silently. The owner's answer is written in the project rule, which then says it overrides the standard one and why.

### Aturan khas SIPEBRI (bagian proyek, bahasa Indonesia)

SIPEBRI v3 adalah aplikasi kredit BPR Bangunarta (Laravel 13 + Inertia v3 + React 19 + Wayfinder); fungsional SIPEBRI lama ditulis ulang di sini, pengguna dan nasabah datang dari Codex, dan setiap perubahan data bisnis harus bisa diaudit (standar OJK). Peta kode, status alur berkas, dan cara menjalankan ada di `CLAUDE.md`; status dan keputusan di `memory/PRD.md`. Aturan khas proyek (semuanya terdaftar di `.ai/rules/index.md`):

| Aturan | Berkas |
| --- | --- |
| bahasa (kode Inggris, layar Indonesia), integritas relasi | `.ai/rules/general.md` |
| audit log wajib (append-only, rantai hash) | `.ai/rules/audit-log.md` |
| migrasi dan skema | `.ai/rules/database.md` |
| tambahan khas proyek atas standar PHP | `.ai/rules/php.md` |
| tambahan khas proyek atas standar test | `.ai/rules/tests.md` |
| Compact UI, komponen bersama, tabel, isian | `.ai/rules/js.md` |

**Pengecualian dari bawaan Boost** (Bagian 2 di bawah): Boost meminta pemilik menjalankan seluruh test, di sini agent menjalankannya sendiri (`scripts/verify.sh`); Boost melarang berkas dokumentasi baru, di sini memperbarui `AGENTS.md`, `CLAUDE.md`, `.ai/rules/*`, dan `memory/PRD.md` adalah bagian dari pekerjaan, berkas dokumentasi lain tetap butuh permintaan; folder dasar yang sudah disetujui: `.ai/`, `.githooks/`, `memory/`, `docs/briefs/`, `scripts/`; deploy ke VPS pemilik, jadi skill dan CLI Laravel Cloud tidak dipakai; tetap berlaku dari Boost: tanpa perubahan dependency dan tanpa membuat model di tinker tanpa persetujuan.

**Gerbang saat push (tambahan proyek, penyimpangan dari devcore):** `scripts/verify.sh` yang hijau penuh meninggalkan stempel di `.git` (isi pohon `HEAD` dan waktunya; hanya bila tidak ada berkas terlacak yang berubah dan tidak ada berkas kode yang belum terlacak). Hook pre-push melewati langkah `verify.steps` bila setiap commit yang dikirim memiliki pohon yang persis sama dan stempelnya belum lebih tua dari `VERIFY_STAMP_HOURS` (`devcore.conf`, bawaan 24 jam); pemeriksaan commit yang dikirim (pelemahan dan baris Brief) selalu jalan, dan `scripts/verify.sh` manual dan CI selalu menjalankan semuanya. Jadi urutan kerja yang menghemat waktu: selesaikan semua commit (termasuk penutup brief), jalankan `scripts/verify.sh` sekali pada isi akhir, lalu push.

**Tidak bisa ditawar (tambahan proyek):** setiap perubahan data bisnis, kejadian autentikasi, ekspor, dan baca data sensitif meninggalkan jejak audit (`.ai/rules/audit-log.md`); jangan mengubah migrasi yang sudah dijalankan dan jangan mereset database (`.ai/rules/database.md`); jangan menjalankan `vp check --fix` pada `resources/js` (`.ai/rules/js.md`); apa yang ditegakkan server tentang karakter yang diketik, ditegakkan juga oleh isian (`.ai/rules/js.md`); tidak ada status berkas baru tanpa dibahas (`CLAUDE.md`).

## 7. Non-negotiables

One line each; the rule itself and its reasons live in the file named, not here.

- Never use `--no-verify`, never skip, delete, or loosen a check to get green: fix the cause (`.ai/rules/quality.md`).
- Never overwrite an existing file you have not read in full; use targeted edits.
- Documents and code are compared with each other in every change (step 4a); a stale document is a defect fixed in the same commit.
- No hardcoded secrets, silent mocks, bypassed gates, or swallowed errors (`.ai/rules/quality.md`).
- Every change meets the production standard of `.ai/rules/quality.md`, and what can be tested is tested (`.ai/rules/testing.md`).

# Bagian 2: Pedoman Laravel Boost (dibuat otomatis; praktik menulis kode; jangan diubah)

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application running on PHP 8.3. You are an expert with the Laravel ecosystem. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists (settled decisions, non-obvious traps, standing constraints). Framework and package guidelines that only apply to specific paths (testing, frontend, components) also live there, under `.ai/rules/boost` — this is not just recorded decisions, it is load-bearing guidance you have not seen inline. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.
- Record a rule with `record-rule` only when the user explicitly asks for one. Instructions for the work at hand are not rules, no matter how emphatic: "remove this typo", "use X here" are work to do, not rules to record. Never record a rule on your own initiative, as a byproduct of a change, or to summarize what you just did. When the user does ask, pass a `glob` (e.g. `app/Http/Controllers/**`), a short `title`, and a few-line `note`. Use `record-rule` rather than your native memory or notes tool, because native memory is personal and session-scoped, while only `.ai/rules` is shared with the team and persists in the repo.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.
- Activate the `deploying-to-cloud` skill whenever deploying to Laravel Cloud, configuring Cloud environments or resources, using the Cloud CLI, or troubleshooting Cloud deployments.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-react-development` when working with Inertia client-side patterns.

# Inertia v3

- Use all Inertia features from v1, v2, and v3. Check the documentation before making changes to ensure the correct approach.
- New v3 features: standalone HTTP requests (`useHttp` hook), optimistic updates with automatic rollback, layout props (`useLayoutProps` hook), instant visits, simplified SSR via `@inertiajs/vite` plugin, custom exception handling for error pages.
- Carried over from v2: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.
- Axios has been removed. Use the built-in XHR client with interceptors, or install Axios separately if needed.
- `Inertia::lazy()` / `LazyProp` has been removed. Use `Inertia::optional()` instead.
- Prop types (`Inertia::optional()`, `Inertia::defer()`, `Inertia::merge()`) work inside nested arrays with dot-notation paths.
- SSR works automatically in Vite dev mode with `@inertiajs/vite` - no separate Node.js server needed during development.
- Event renames: `invalid` is now `httpException`, `exception` is now `networkError`.
- `router.cancel()` replaced by `router.cancelAll()`.
- The `future` configuration namespace has been removed - all v2 future options are now always enabled.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== wayfinder/core rules ===

# Laravel Wayfinder

Use Wayfinder to generate TypeScript functions for Laravel routes. Import from `@/actions/` (controllers) or `@/routes/` (named routes).

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

# Pest

- This project uses Pest. Create tests with `php artisan make:test --pest {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.
- Do not delete tests or test files without approval. They are part of the application.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/pest` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.
- After the feature tests pass, ask the user to run the complete suite with `php artisan test --compact`.

=== inertia-react/core rules ===

# Inertia + React

- IMPORTANT: Activate `inertia-react-development` when working with Inertia React client-side patterns.

</laravel-boost-guidelines>
