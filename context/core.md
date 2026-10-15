# ns_social_login — Core facts

## Identity

| Item | Value |
|---|---|
| Extension key | `ns_social_login` |
| Composer name | `nitsan/ns-social-login` |
| Title (ext_emconf) / product name | TYPO3 Social Login & Register / Social Login |
| Namespace | `NITSAN\NsSocialLogin\` → `Classes/` (ext_emconf classmap: `Classes/`, `Library/`) |
| Version (ext_emconf) | 2.1.0 (latest tag 2.0.0; product page changelog lists 2.0.1) |
| Repository | https://github.com/nitsan-technologies/ns_social_login |
| Licensing status (NITSAN Free/Freemium sheet) | **Freemium** — this repo is the free version |
| Premium version (sheet) | https://t3planet.de/typo3-social-login-extension (price "unknown" in the sheet) |

## Compatibility

| Source | TYPO3 | PHP |
|---|---|---|
| `ext_emconf.php` | `9.5.0-13.4.99` | not declared |
| `composer.json` | `^9 \|\| ^10 \|\| ^11 \|\| ^12 \|\| ^13` | `>=7.4` |
| ns_license 14.5.4 (dependency) | `12.4.18-14.9.99` | — |

- Effective minimum is **TYPO3 12.4.18 / PHP 8.1** because of the ns_license dependency; the declared 9.5 lower
  bound and the v9–v11 code branches are kept.
- Only TYPO3 13.4.35 (PHP 8.4, Classic mode) was run in this session; v9–v12 are declared but untested.
- The plugin is a `list_type` plugin (`CType = list`). Plugin subtypes are deprecated in TYPO3 13.4 and
  removed in v14 — a v14 port needs a CType migration. On v13, `configurePlugin()` without plugin type (Pi1, Pi2)
  and `registerPlugin()` (Pi1) each log an `E_USER_DEPRECATED` for `list_type`.

## Dependencies

| Package | Constraint | Why |
|---|---|---|
| `typo3/cms-core` | see above | core |
| `hybridauth/hybridauth` | `^3` (Composer) | OAuth client; Classic mode uses the bundled copy in `Library/` (Facebook provider only) |
| `nitsan/ns-license` / `ns_license` | `^14.5.4` / `14.5.4-14.9.99` | `Setup` / `BackendUserLogin` call its `LicenseService` when it is active; the premium strip needs its module route |
| `typo3/cms-felogin` | not declared | optional; legacy marker hook only |

## Tooling

- No PHPUnit, PHPStan, CS fixer or package.json in the repo.
- CI: `.github/workflows/ter-release.yml` only (publishes tags to TER, PHP 7.4, deprecated `set-output`).
- Translations: Crowdin (`.crowdin.yml`), source `Resources/Private/Language/locallang.xlf`;
  `de.locallang.xlf` is committed and does not contain the newer keys.

## Conventions

- Extbase plugins `Pi1` (list, connect) and `Pi2` (endpoint, list), all non-cacheable; only `Pi1` is
  registered as a content element ("Social Login").
- `Configuration/Services.yaml` autowires `NITSAN\NsSocialLogin\` (models excluded) and tags the event
  listeners explicitly (no `#[AsEventListener]`, v13+ only).

## Known issues (code-read unless marked verified)

- **License check breaks the free extension.** `Setup::executeOnSignal()` (`AfterPackageActivationEvent`, i.e.
  Extension Manager / `extension:activate`) and `Hooks/BackendUserLogin::dispatch()` (`AfterUserLoggedInEvent`,
  backend *and* frontend logins) call ns_license `LicenseService::connectToServer('ns_social_login', 0)`.
  Without an `ns_product_license` row for `ns_social_login` it runs `updateFiles()`, which renames
  `ext_tables.php`, `Configuration/TCA/Overrides/sys_template.php` and every `.xlf` in this extension
  (`*..php`, `*..xlf`). Skipped while the browser has the 14-day `serverConnectionTime` cookie.
  Verified by reading ns_license 14.5.1; avoided during the 2026-10-14 test.
- `Configuration/SiteConfiguration/sites.php` is loaded as a "full table" file: its side effects add the
  provider/storage fields to `site`, but it also sets `$GLOBALS['SiteConfiguration']['sites'] = 1`. Belongs in
  `Configuration/SiteConfiguration/Overrides/site.php`. The Facebook app secret is stored in plain text there.
- `SocialLoginService::getUser()`: a new user without a downloaded photo calls
  `createFileReferenceFromFalFileObject(null, …)` → `TypeError` (condition `$new === true || …`).
  `$fields['image']` receives the file uid although `fe_users.image` is a reference count field.
- `SocialLoginService::authUser()` reads `$user['fromHybrid']` without `isset` → "Undefined array key" warning
  for every non-social frontend login (the service runs before the core one, priority 82).
- Errors end in `echo …; die;` (`AuthUtility::authenticate()`) or `var_dump(); die;` (`initAuth()`).
- `Hooks/FeLoginHook` targets the removed pibase felogin `postProcContent` hook, reads a non-existent
  `providers` extension configuration and links to page type `1712813110` with `tx_sociallogin_pi1` params —
  dead code on current TYPO3.
- Page types `1712813110` / `1712813073` use `switchableControllerActions` (ignored since v12), so they run the
  first action of the plugin: `1712813073` → `Pi2::endpoint` (works), `1712813110` → `Pi1::list` (not `connect`).
- `Popup.js` (included on every page) calls `window.opener.reloadParent()` on every `beforeunload` →
  `TypeError` when there is no opener.
- The "Clear Social Login Cache" toolbar action only resets the Hybridauth `provider` value in the *backend*
  PHP session — it does not affect frontend users.
- `Domain/Model/User` + `FrontendUserGroup` are unused; the two persistence mappings (`Classes.php`, TypoScript
  `config.tx_extbase.persistence`) use different, partly wrong column/property names; the TypoScript one is
  ignored since TYPO3 10 anyway.
- `ext_tables.php` registers icons via `IconRegistry` (should be `Configuration/Icons.php`) and contains an
  unused `$icon` array with helpdesk icon names. It imports the wizard TSconfig with
  `ExtensionManagementUtility::addPageTSConfig()`, deprecated in v13 and removed in v14 (`Configuration/
  page.tsconfig` replaces it).
- README: badge says v1.0.1 / TYPO3 12, the "Pro version" URL is broken (`https://t3planet.comns-social-login-typo3-extension`).
