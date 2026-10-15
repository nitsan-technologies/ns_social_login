# AGENTS.md — ns_social_login (TYPO3 Social Login & Register)

Agent router for the TYPO3 Social Login free extension. Load only what your task needs.

> **Principles:** `context/principles.md`
> **Session log:** `context/session-state.md`
> **Doc index:** `context/docs-map.md`

---

## Always load

| File | Why |
|---|---|
| `context/principles.md` | Guardrails, "nothing that works may break" rule |
| `context/core.md` | Identity, TYPO3/PHP constraints, dependencies, tooling, known issues |

---

## Task router

| Task | Load |
|---|---|
| Facebook login flow, auth service, fe_users creation, avatar import, site settings, plugin output, logout | `context/features/social-login.md` |
| Premium strip / free trial / ns_license | `context/features/premium-cta.md` |
| Classes, page types, routes, TCA, TypoScript, templates, assets, bundled Hybridauth | `context/architecture.md` |
| Lint / validate / manual test | `tasks/run-quality.md` |
| Update agent context after a change | `tasks/context-update.md` |

---

## Feature status

| Feature | Status | Agent entry |
|---|---|---|
| "Social Login" plugin (`nssociallogin_pi1`): provider buttons, page/popup mode, 3 button styles, logout button | Done | `context/features/social-login.md` |
| Facebook login via auth service (creates/updates `fe_users`, optional avatar) | Done (only provider in the free version) | `context/features/social-login.md` |
| Provider + storage settings in the site configuration | Done | `context/features/social-login.md` |
| Premium strip + ns_license free-trial link (edit form + Page module) | Done (wizard deep link needs ns_license 14.5.4) | `context/features/premium-cta.md` |

---

## Quick commands

```bash
cd typo3conf/ext/ns_social_login     # or packages/ns_social_login in Composer mode
for f in $(find Classes Configuration -name "*.php") ext_*.php; do php -l "$f"; done
composer validate --no-check-publish
for f in Resources/Private/Language/*.xlf; do xmllint --noout "$f"; done
```
