# OPENCODE PROMPT — Adapt NexusSchool Filament panels to the MaterialM design

You are the implementation agent for NexusSchool (Laravel 12.63, Filament v5.6.8, Livewire 4, Tailwind v4 via Vite).
Task: restyle the existing Filament panels so they follow the visual language of the MaterialM Tailwind template
that sits in `MaterialM-Tailwind-Nextjs-Free-main/` at the repo root.

This is a **UI-only** task. Do not touch business logic, policies, tenancy, routing, migrations, factories or tests' intent.

---

## 0. Ground rules

1. The template is a **visual reference only**. It is React/Next.js; we are Blade/Livewire/Filament. Do NOT port components, do NOT install React, Next, shadcn, Radix, or any template dependency. Do NOT copy its images, logos or markup verbatim (its license terms have not been verified). Recreate the look from the design tokens listed below.
2. Do NOT modify anything inside `MaterialM-Tailwind-Nextjs-Free-main/`.
3. Do NOT change panel ids, paths, domains, middleware, `canAccessPanel`, or any `->resources()/->pages()` list. A routing redesign is planned separately and must not collide with this work.
4. Do NOT change user-facing French labels or translations.
5. Do NOT touch PDF/document views: `resources/views/documents/**`, `resources/views/academic-communication/**`.
6. Do NOT commit or push. Leave changes in the working tree.
7. Never report a command as passing unless you actually ran it and saw the output. Paste real outputs.
8. If something in this prompt contradicts what you find in the repo or in the Filament v5 docs, stop and report instead of guessing.

## 1. Pre-checks (stop and report if any fails)

- `git status --short`: if there are unrelated uncommitted changes (ESP-002 work), STOP and ask the user to commit/stash them or create a branch (suggested: `ui/materialm-theme`). The UI diff must be reviewable on its own.
- The template folder is currently tracked by git (about 238 files) and Tailwind v4 auto-scans the project for class names, so it would bloat the generated CSS. Report the situation and propose: `git rm -r --cached MaterialM-Tailwind-Nextjs-Free-main` plus a `.gitignore` entry (or moving it outside the repo). Do this only after the user approves.
- Confirm Node/npm work: `npm install` then `npm run build` succeed BEFORE any change (baseline).

## 2. Design tokens to reproduce (source: `MaterialM-.../package/src/app/css/theme/default-colors.css`, `dark-colors.css`, `globals.css`)

| Token | Light | Dark |
|---|---|---|
| Primary | `#00A1FF` | same |
| Secondary | `#16CDC7` | same |
| Info | `#46caeb` | same |
| Success | `#00ceb6` | same |
| Warning | `#FFB900` | same |
| Error / danger | `#FF6692` | same |
| Page background | `#F8FAFD` | `#1a2537` |
| Card / sidebar / header surface | `#ffffff` | `#111c2d` |
| Border | `#e0e6eb` | `#333f55` |
| Body text | `#2a3547` | `#7b8893` (muted) / `#ffffff` (headings) |
| Muted surface | `#EFF4FA` | `#2a3851` |

Derived rules:
- "Light" tints (icon bubbles, active menu items, soft badges) = the colour at **12 %** over transparent (`color-mix(in oklab, <color> 12%, transparent)`).
- "Emphasis" (hover/pressed) = the colour mixed **80 % with black**.
- Radius: cards about **12px**, inputs/buttons/menu items about **7-10px**, small chips 4px.
- Shadow: very light, `0 1px 4px rgba(133,146,173,.2)`. Cards are mostly flat with a 1px border.
- Typography: **Inter**, body 14px (`text-sm`), headings `font-semibold` in the dark text colour.
- Content container max width about **1400px**.
- Layout: white sidebar with group labels; active/hovered menu item = light-primary background + primary text; white header with moderate vertical padding (about 16px), breadcrumb, dark-mode toggle; stat cards with a small tinted rounded icon bubble.

## 3. Implementation approach (Filament v5 + Tailwind v4)

Read the Filament v5 docs/vendor sources first (custom themes, `viteTheme`, panel colors, fonts, dark mode). Do not invent APIs or `fi-*` selectors from memory; verify them in `vendor/filament/**`.

1. **One shared source of truth.** Create a single PHP helper (for example `app/Support/Filament/NexusPanelTheme.php`) that applies the common panel settings: colour map (primary `#00A1FF`, danger `#FF6692`, success `#00ceb6`, warning `#FFB900`, info `#46caeb`, a slate-based `gray` scale), font, dark mode, content width, sidebar behaviour. The 8 panel providers call it; they must NOT each hold a diverging copy.
2. **One shared CSS token file** (for example `resources/css/filament/nexus-tokens.css`) holding the variables and component overrides. Each panel gets a thin `theme.css` (generated with `php artisan make:filament-theme <panel>` or equivalent) that only imports the shared file. Register with `->viteTheme(...)` and add the entries to `vite.config.js`.
3. Override Filament through its CSS variables and `fi-*` hooks (sidebar, topbar, cards/sections, tables, buttons, badges, form inputs, stats widgets, login page). Prefer variables over `!important`.
4. **Dark mode** must work with the dark tokens above.
5. **Font**: Inter, preferably served locally rather than from a third-party CDN. Verify the Filament v5 font-provider API.
6. **Icons**: keep Heroicons for now. Do NOT add an icon package. If you think a Tabler/Solar set is worth it, propose it in your report with the package name verified and its maintenance status; do not install it.
7. **Per-role colour.** Default: all panels share the MaterialM primary. Distinguish roles with the brand name/badge only (current per-panel accent colours are removed). Implement this default unless the user says otherwise.

## 4. Accessibility constraint

`#00A1FF` on white is only about 2.8:1 contrast, below WCAG AA (4.5:1) for normal text. Keep `#00A1FF` for brand, accents, icons and tints, but for **text and button fills on white**, pick a darker shade that reaches at least 4.5:1, and report the exact ratios you computed. Check hover/focus states and the light-tint-on-tint combinations too (for example primary text on the 12 % primary background).

## 5. Phases and stop points

**Phase 0 (read-only):** report the current state: how each panel is themed today, existing `resources/css/app.css`, Filament v5 theme mechanism you verified, list of custom Blade views under `resources/views/filament/**`, risks. **STOP and wait for approval.**

**Phase 1 (pilot):** shared helper + shared tokens + theme for `school-admin` only (the largest panel). Provide: files changed, `npm run build` output, and screenshots (light + dark, desktop + 360px wide) of login, dashboard, one list page, one form page. **STOP and wait for review.**

**Phase 2:** roll out to the other 7 panels (`super-admin`, `director`, `teacher`, `student`, `parent`, `accountant`, `secretary`), then the custom Blade views under `resources/views/filament/**` and the dashboard widgets (stat cards). Parent and student portals will be used mostly on phones: check 360px width.

## 6. Quality gates (run and paste real output)

- `npm run build` passes.
- `php artisan test` still passes at the same count as before your changes. Note: pages that load a Vite theme can fail in tests if the manifest is missing. If that happens, build assets first; do not weaken or delete tests.
- `vendor/bin/phpstan analyse` reports no new error and `phpstan-baseline.neon` is NOT modified.
- `vendor/bin/pint --test` is clean on the PHP files you changed.
- `git diff --stat` shows no changes outside: the shared helper, panel providers (theme-related lines only), `resources/css/**`, `vite.config.js`, `package.json` (only if needed and justified), and `resources/views/filament/**` styling.

## 7. Final report format

For each phase: files created/modified (with reason), commands run with real output, contrast ratios, screenshots, open questions, and anything you deliberately did NOT do. If you hit a Filament v5 limitation, say so plainly rather than working around it with fragile CSS.
