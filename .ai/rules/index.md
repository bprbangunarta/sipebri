# Project rules index

Before planning or editing, find the row whose paths match what you will touch and read that rule file. Every rule file of the project has a row here (`scripts/check-registry.sh` fails otherwise), the standard ones and the project's own.

| Applies to | Rule file |
| --- | --- |
| everything | .ai/rules/quality.md |
| everything (language, relations; project) | .ai/rules/general.md |
| tests (`tests/**`, `**/*.test.*`, `**/*.spec.*`) | .ai/rules/testing.md |
| tests (project additions) | .ai/rules/tests.md |
| application code (`app/**`, `routes/**`, `config/**`, `bootstrap/**`, `database/**`) | .ai/rules/laravel.md |
| application code (project additions) | .ai/rules/php.md |
| data changes and non-model events (`app/**`, `routes/**`, `database/**`, `bootstrap/app.php`; project) | .ai/rules/audit-log.md |
| migrations (`database/migrations/**`; project) | .ai/rules/database.md |
| front end (`src/**`, `resources/js/**`, `resources/views/**`) | .ai/rules/ui.md |
| front end (`resources/js/**`, `resources/css/**`; project additions) | .ai/rules/js.md |
