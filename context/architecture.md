# ns_social_login — Architecture

## Plugins (`ext_localconf.php`, `Configuration/TCA/Overrides/tt_content.php`)

| Plugin | Signature | `AuthController` actions (all non-cacheable) | Content element |
|---|---|---|---|
| Pi1 | `nssociallogin_pi1` | `list`, `connect` | "Social Login" (`CType = list`), FlexForm `flexform_list.xml` |
| Pi2 | `nssociallogin_pi2` | `endpoint`, `list` | none — only rendered by page type `1712813073` |

- Both are `list_type` plugins: `configurePlugin()` is called without plugin type; only Pi1 gets
  `registerPlugin(…, 'Pi1', 'Social Login')` (no icon). Pi1 TCA: `subtypes_addlist` `pi_flexform`,
  `subtypes_excludelist` `recursive,select_key,pages`, FlexForm via `addPiFlexFormValue('nssociallogin_pi1', …)`.
- FlexForm: one sheet `listConfig` with `settings.useGlobalStyle` (toggle, reload) and `settings.style`
  (s1/s2/s3, shown when the toggle is off).
- Wizard entry in `Configuration/TSconfig/ContentElementWizard.tsconfig` (group `plugins`, icon
  `plugin-nssociallogin`, `CType = list` + `list_type`), imported from `ext_tables.php` (see `core.md`).
- `ext_localconf.php` adds `tx_nssociallogin_pi1[provider]`, `[redirect]` and `[error]` to
  `FE.cacheHash.excludedParameters`.
- Page module preview: `Preview/SocialLoginPreviewRenderer` registered as
  `TCA.tt_content.types.list.previewRenderer.nssociallogin_pi1` (premium strip + core plugin label).

## Authentication

| Piece | Where | Role |
|---|---|---|
| Auth service `SocialLoginService` | `ext_localconf.php` `addService()`, subtypes `authUserFE,getUserFE`, priority/quality 82 | Reads `tx_nssociallogin_pi1[provider]` in `init()`, stores it in the Hybridauth session; `getUser()` authenticates with the provider and creates/updates the `fe_users` record; `authUser()` returns 200 for social users |
| `FE_fetchUserIfNoSession = true` | `ext_localconf.php` | Lets the service run without a login form submit |
| `Utility/AuthUtility` | — | Builds the Hybridauth config from the site settings, callback `TYPO3_SITE_URL . 'oauth'` |
| `Utility/SiteConfigUtility::getAllConstants()` | — | Matches the current request to a site and returns its full configuration array |
| `Hooks/LogOffHook` | `logoff_post_processing` | Disconnects Hybridauth adapters, clears session data and the `PHPSESSID` cookie (FE only) |
| `Hooks/FeLoginHook` | felogin `postProcContent` | Legacy pibase marker, dead (see `core.md`) |

## Page types and routing (`Configuration/TypoScript/setup.typoscript`)

| typeNum | Object | Runs |
|---|---|---|
| `1712813073` | `HybridEndPoint` | Pi2 → `endpointAction()` (OAuth start and callback) |
| `1712813110` | `HybridAuth` | Pi1 (meant for `connect`) — only referenced by the dead felogin hook |

`Configuration/Routes/Sociallogin.yaml` (`PageType` enhancer, `oauth: 1712813073`) must be imported into the
site config so the callback URL `/oauth` resolves.

## Classes

| Class | Role |
|---|---|
| `Controller/AuthController` | `list` (buttons / logout / disabled-user notice, closes popup on `isRedirect`), `connect`, `endpoint`, `clearCache` (AJAX) |
| `Service/SocialLoginService` | Auth service (above), `fe_users` upsert, avatar import, unique username |
| `Utility/AuthUtility`, `Utility/SiteConfigUtility` | Hybridauth wrapper, site settings |
| `EventListener/ClearCacheEventListener` (v11+), `Hooks/ClearCacheHook` (v9/v10) | "Clear Social Login Cache" toolbar entry → AJAX route `ajax_clear_cache` (`Configuration/Backend/AjaxRoutes.php`) |
| `Setup`, `Hooks/BackendUserLogin` | ns_license `connectToServer()` on activation / login (see `core.md`) |
| `ViewHelpers/CurrentUriViewHelper` | `{sociallogin:currentUri()}` (global Fluid namespace `sociallogin`) |
| `Domain/Model/User`, `FrontendUserGroup` | Unused |
| `Backend/PremiumStrip`, `Form/Container/…`, `Form/Resolver/…`, `Preview/SocialLoginPreviewRenderer` | Premium strip (see `features/premium-cta.md`) |

`Library/` is a bundled Hybridauth 3 (namespace `Hybridauth\`, Facebook provider only) for Classic mode.

## Database (`ext_tables.sql`, `TCA/Overrides/fe_users.php`)

`fe_users.tx_ns_social_login_source` (int: 0 None, 1 Facebook) and `tx_ns_social_login_identifier`
(provider user id, read-only), index `socialauth_idx`, own tab "Social" in the fe_users form.

## Site configuration (`Configuration/SiteConfiguration/sites.php`)

Tab "Providers" with palettes Facebook (`facebook_enable`, `facebook_appid`, `facebook_app_secret`,
`facebook_app_scope` default `email`, `facebook_display_mode` page/popup) and Storage (`storage_page` default 1,
`usergroup` default 1, `file_storage` default 1, `avatar_image` default `user_upload`).

## TypoScript, views and assets

- Static template "Social Login" (`Configuration/TypoScript/`, no site set): extension-wide
  `plugin.tx_nssociallogin` (not `_pi1`) with view paths, `settings.styleConfig` (global style, Facebook
  label/icon overrides via constants in `Settings/StyleConfig.typoscript`, all empty), the two page types,
  `page.includeJS` `Popup.js` and `page.includeCSS` `button-style.css` on every page.
- `Templates/Auth/List.html` + `Layouts/Default.html`; `Css/font-awesome.min.css` is not referenced.
- Backend: `Css/backend-premium-strip.css` (strip only).
