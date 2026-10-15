# ns_social_login — Session state

## 2026-10-14 — Premium strip and agent context

- Branch `master` (HEAD 040a9ea). All changes uncommitted. Worked in the TYPO3 13.4.35 Classic install
  (`typo3_src-13.4.35`); the theme_camino copy (with the ns_license `^14.5.4` dependency) was not touched.
- Premium strip with "Start Free Trial" → ns_license module `?trial=ns_social_login#free-section` in the edit
  form and the Page module (see `features/premium-cta.md`). Resolver priority 44.
- Verified live on 13.4.35 / PHP 8.4: extension temporarily added to `PackageStates.php` (no Extension Manager
  activation, `serverConnectionTime` cookie set to keep the license check from renaming files), temporary
  system-maintainer admin, one hidden test element. All removed afterwards; `PackageStates.php` and
  `settings.php` restored byte-identical; the extension is **not active** in that install. ns_license there is
  14.5.1, so the wizard did not auto-open. v9–v12 and the frontend not tested.
- Created agent context (`AGENTS.md`, `CLAUDE.md`, `context/`, `tasks/`); no previous context existed.
- Added `nitsan/ns-license ^14.5.4` / `ns_license 14.5.4-14.9.99` (composer.json, ext_emconf.php).

## 2026-10-14 — Banner redesign from review

- Team-wide wording ("You're using the free version of NS Social Login" / "Unlock premium features and get
  more out of NS Social Login."), two-line layout, arrow on the button, more padding, rounded card
  (see `features/premium-cta.md`). Version 2.0.0 → 2.1.0 (`ext_emconf.php`).
- Same markup and CSS verified live in ns_event / ns_gallery / ns_helpdesk on 14.3.7; not re-tested here on
  13.4.35 (extension not active there). Uncommitted.

## 2026-10-15 — Context audit

- Code-read audit: ns_license is declared (was "not declared"; effective minimum TYPO3 12.4.18); added plugin
  registration details, cHash exclusions, site config defaults, v13 deprecations.
