---
paths:
    - 'src/**'
    - 'resources/js/**'
    - 'resources/views/**'
---

# User interface

The standard rules for a React and TypeScript front end. They are checked by `scripts/check-standards.mjs` (what a machine can check), by the tests, and by review. What is a standing choice of the project (icons, theme, density, tone of voice) is written once in the section "The project's design choices" below, so the antislop skills have a direction to filter against.

## Changing the UI

Change a component by improving the shared component, never with a one-off style in a page. Reuse the shared components (buttons, fields, dialogs, tables, cards, charts) before writing a new one; a new shared component gets an example on the UI guideline page, and a new colour token gets an entry in its token list (a test enforces both when the project has that page).

## Language and words

Everything a person reads (labels, titles, messages, errors, toasts, emails) is written in the project's language and in one voice; a person who signs in is called the same thing on every screen. Identifiers, file names, and URLs are English; a URL matches the screen title (`Audit Logs` is `/audit-logs`). A server's validation message is shown as it is, never rewritten.

## Dates, times, and numbers people read

A date, a time, and a number are turned into text for a person in exactly one place each (`lib/format.ts`). A screen never calls `Intl`, `toLocale...String`, or a date library's formatter itself. Values meant for machines (database, API, tokens) stay ISO and plain numbers, and an empty value shows an en dash.

## Talking to the server

Every request goes through one module (`lib/http.ts`: `get`, `post`, `put`, `delete`); forms use one form hook and reads use one resource hook. A direct `fetch` is wrong. A 422 fills the fields' errors, a 401 returns to the sign-in page, and a 403 that asks for a recent sign-in opens the confirmation dialog. Never keep secrets (passwords, codes, tokens) in `localStorage`, `sessionStorage`, URLs, or logs.

## TypeScript and React

Strict mode; no `any`, `@ts-ignore`, or `@ts-nocheck`. Default exports only for pages, layouts, and the app. Function components and hooks only; hooks at the top level. `useEffect` only to sync with the outside world, never to load data or to derive state. A list `key` is a stable id, never the index. A promise in a handler is handled or marked `void`. A controlled input pairs `value` with `onChange`. No `dangerouslySetInnerHTML`, no `console.log`.

## The screen enforces what the server enforces

A server rule about what may be typed (digits only, a name without digits, a length, a range) is enforced by the input as well, so a field that cannot hold a character never shows one; the server stays the authority and checks again. Pick the control by the rule (a masked input for digit formats, a filtered input for letters, a date picker for dates, a combobox for choices, `maxLength` on every free-text field). `type="number"` and `type="tel"` are not used (they accept letters and signs). Send the unformatted value. When a rule changes on one side, change the other in the same commit, with a test that types wrong characters into the field.

## Forms and fields

A field never carries explanatory text under its control. A description is the field's `hint`, shown as a tooltip on an info icon beside the label and read to screen readers as the control's description. Only the validation error shows under the control. A control with buttons beside it (generate, copy) is one group inside the field; the buttons are as tall as the control.

## Buttons and dialogs

Buttons follow their place, not taste: page header, toolbar, card header, and settings rows use icon plus label; dialog and card footers use text only (cancel left, confirm right); table row actions and close buttons are icon only with an accessible name and a tooltip. The buttons of a page header are one set of equal widths (two equal columns on a phone). A dialog has header, body, and footer, and the footer exists only when there are buttons. A dialog with inputs does not close on a click outside; Esc and the close button always close it, focus moves inside when it opens and returns to the button that opened it. An action that cannot be undone, or that signs people out, asks first and says what will happen. Floating layers (dialog, drawer, popover, dropdown, tooltip) come only from the shared components.

## Tables and lists

Every table is the shared table. Cells and headers never wrap onto a second line; a wide table scrolls sideways inside its card, and no column is hidden on a narrow screen. Every list has loading, empty, and error states, and every action gives feedback. A list paged by the server sorts on the server, every column of data has a sort key (a column that cannot sort says why), and the keys are a whitelist on the server.

## Charts

A ring for few parts of a whole, columns for ordered bands, bars for long names. The number is always written beside each part; a picture is only a picture of it, hidden from screen readers. A series colour carries no status meaning and is at least 3:1 against its background.

## Error screens and illustrations

An error screen is full screen and outside the app shell: an illustration, what happened in words (never a bare number or an English phrase of the protocol), and a way out. The words and the picture of each status live in one list that feeds the app and the static pages served when the app cannot answer; a test fails when a saved static page is out of date. A static page carries no script and no `javascript:` link.

## Tokens and CSS

One file is the only source of colours and sizes. No literal colours, no arbitrary text sizes, and no raw palette for a status: use the status tokens (`success`, `warning`, `info`, `danger`, each with `-soft`, `-line`, `-ink`).

## Accessibility

Contrast, keyboard, focus, and states are `antislop-human`; these are the project's component rules: icon buttons have an accessible name, inputs are wrapped in the field component (label, hint, and error wired by id), anything clickable is a `button` or a link, and nothing works with a mouse only.

## Validate the screen, not only the code

Check any changed screen at 1280 px (desktop; never the default pane width), 768 px, and 375 px, with realistic long content, in loading, empty, and error states. At each width `document.documentElement.scrollWidth` equals the screen width (only a table scrolls, inside its card), no control lacks a name, there is one `h1`, and dialogs fit the screen. Guest screens (sign-in) are checked too.

## The project's design choices

Written once so the antislop skills filter against a direction and not against their own defaults. The owner accepted these for codex on 2026-10-17 and for SIPEBRI on 2026-10-19 (mode DURING); a change to any line is made here and nowhere else.

- **Design Read (R-37):** an internal tool for BPR staff, dense and compact, calm: ENERGY 1 / RHYTHM 1 / MOTION 1. A uniform rhythm is deliberate (tables, forms, and cards repeat on purpose). Colours and sizes come from `resources/css/app.css` only, and `/ui-guideline` (Super Admin) shows them.
- **Icons (R-04):** `lucide-react` is the one icon set; each icon is picked for what it means on that screen, and a submenu entry has a plain dot instead of an icon.
- **Theme (R-21):** light only, no toggle (a daytime office tool; the tokens exist for one theme). A second theme is a new task: every token and component in both modes.
- **Density (R-03):** controls are `h-8` and the sidebar is `w-60`, below the 44 px tap target that `antislop-layoutmobile` asks for. This is a known deviation chosen for density on desktop. Surveyors use phones, so the survey screens are checked for overflow and reach, but the control size stays.
- **Copy (R-02, R-16):** Indonesian text, no em dash in screen text, no hype words, no invented numbers or claims.
- **Honest placeholders (R-23, R-38):** a page that is not built yet says so on the page and carries no invented data, dates, or feature list.
