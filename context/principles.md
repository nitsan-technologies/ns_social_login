# ns_social_login — Operating Principles

*Every change to code, context or docs must serve the goals below.*

---

## Goal 1 — Nothing that works may break

The extension is declared for TYPO3 v9.5 to v13.4 and PHP 7.4+ (Composer and Classic mode).

- Every change must keep the declared range working. PHP 7.4 syntax only: no constructor property promotion,
  `readonly`, `match`, enums, named arguments or `catch (Foo)` without a variable.
- Follow the existing version switches (`version_main` checks in `ext_localconf.php` / `AuthController`,
  `class_exists(IconSize::class)`, the constructor + `setData()` pair in the node resolver) instead of
  inventing new ones.
- Prefer the smallest change that fixes the root cause. No drive-by refactors.
- Run `tasks/run-quality.md` and re-test the affected flow in the frontend and backend before claiming done.

## Goal 2 — Token-cost efficiency (agent context)

- **DRY context:** each fact lives in exactly ONE file. Reference, don't copy.
- **Load on demand:** read `context/features/<area>.md` for the task, not everything.
- **Prune as you go:** keep `context/session-state.md` short.

## Goal 3 — Accuracy and trust

- Answer from context + code, never from invented API, table or field names.
- Conflicting facts across files = fix the source file.

---

## Guardrails (non-negotiable)

- This is an authentication service: it logs frontend users in. Any change to `SocialLoginService` or
  `AuthUtility` must keep normal (non-social) felogin logins working and must never log a user in without a
  successful provider response.
- Social profiles are personal data (name, e-mail, phone, address, photo). Don't log or output them.
- The Facebook app secret lives in the site configuration (`config.yaml`). Never commit a site config that
  contains real credentials.
- **Don't activate the extension through the Extension Manager or `extension:activate` on an installation
  with ns_license but no `ns_product_license` row for `ns_social_login`** — see the license-check known issue
  in `core.md` (it renames files inside this extension).
- Do not change ns_license from an ns_social_login task; reuse its module.
- All UI text goes into `Resources/Private/Language/locallang.xlf` (English source only, translations via
  Crowdin), no hard-coded labels.
- FormEngine node resolver priorities must be unique per node name across all installed extensions
  (see `features/premium-cta.md`).

---

## Apply this whenever you

- Edit `context/` or `AGENTS.md`
- Land a feature or bugfix (update the matching `context/features/*.md` + `session-state.md`)
