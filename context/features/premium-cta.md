# Feature — Premium strip and ns_license free trial

*Status: Done. Verified live on TYPO3 13.4.35 Classic (edit form, Page module, click-through) with
ns_license 14.5.1.*

## Licensing status

Freemium per the NITSAN Free/Freemium sheet. Premium product: https://t3planet.de/typo3-social-login-extension
(not linked from the UI; the trial stays inside the TYPO3 backend). Premium features:
`features/social-login.md`.

## Dependency

`composer.json` `"nitsan/ns-license": "^14.5.4"`, `ext_emconf.php` `'ns_license' => '14.5.4-14.9.99'`. This
raises the effective minimum to TYPO3 12.4.18 (see `core.md`). The strip renders only when the ns_license
module route exists, otherwise nothing.
The trial wizard deep link (`?trial=`) is handled by ns_license **14.5.4+** (`get-license.js`
`maybeOpenTrialDeepLink()`); 14.5.1 opens the T3Planet Shop on "My Extensions" without the wizard.

## Strip

Same implementation pattern as ns_revolution_slider (`Classes/Backend/PremiumStrip.php`), PHP 7.4 compatible.

- `Backend\PremiumStrip::render()` returns the HTML, or `''` if the ns_license route is missing.
  `PremiumStrip::PLUGIN_SIGNATURE` = `nssociallogin_pi1`.
- Content: `actions-rocket` core icon (`IconSize::SMALL` when the enum exists, `'small'` before v13), headline
  and description on two lines, one `btn btn-primary` "Start Free Trial →" link (the arrow is `aria-hidden`
  markup, not part of the label; same tab).
- Wording and design are the team-wide standard from the 2026-10-14 review: "You're using the free version of
  [Extension Name]" / "Unlock premium features and get more out of [Extension Name]."
- Labels in `locallang.xlf`:
  - `premium.strip.headline`: "You're using the free version of NS Social Login"
  - `premium.strip.description`: "Unlock premium features and get more out of NS Social Login."
  - `premium.strip.trial`: "Start Free Trial"
- Styling: `.ns-social-login-premium-strip` (+ `-icon`, `-headline`, `-description`) in
  `Resources/Public/Css/backend-premium-strip.css` — light-blue card (`#e7f2fc`, border `#8eb6e8`, 4px `#2b6cb0`
  left accent, 6px radius, padding 14px 18px, text `#0d2c4a`), semibold 1.1em headline, `light-dark()` for dark
  mode, button `#007bff` (hover `#0062cc`, no wrap). Inside a FlexForm `.panel` (v14) it is inset by 16px; on
  v13 the strip sits in a fieldset and keeps its 1rem bottom margin. Same design as the ns_event / ns_gallery /
  ns_helpdesk strips.

## Placements

1. **Edit form, tab "Plugin", under "Plugin Options"** (above "Use global settings").
   Node resolver `1791979347` (**priority 44**) on `flexFormNoTabsContainer` (the FlexForm has one sheet).
   `PremiumStripFlexFormNoTabsContainerResolver` only matches `tt_content`, field `pi_flexform`, record type
   `list` with `list_type = nssociallogin_pi1` (FormEngine passes `list_type` as array), not an inline child.
   v9–v12 pass data to the constructor, v13 calls `setData()`. The container extends core
   `FlexFormNoTabsContainer`, prepends the strip and adds the stylesheet via `stylesheetFiles`.
   - Priority must be unique per node name across all installed extensions: core's `NodeFactory` throws
     #1433155705 otherwise. Taken: ns_news_comments 40, ns_event 41, ns_gallery 42, ns_helpdesk 43,
     ns_social_login 44, ns_faq 45, ns_open_streetmap 46. Each resolver returns `null` for foreign plugins, so they chain safely.
2. **Page module preview.** `Preview/SocialLoginPreviewRenderer` extends core `StandardContentPreviewRenderer`
   and is registered for this list type only (`types.list.previewRenderer.nssociallogin_pi1`, skipped if another
   extension set a string renderer for all list plugins). It prepends the strip to the core preview, so the
   "Social Login" plugin label stays, and adds the CSS via `PageRenderer::addCssFile()`. A
   `PageContentPreviewRenderingEvent` listener was not used because setting preview content there replaces
   the core plugin label. Needs the preview renderer API of the Fluid-based Page module (TYPO3 10+); v9 shows
   no Page module strip (not tested below v13).

## Trial link

`UriBuilder::buildUriFromRoute('nitsan_nslicensemodule', ['trial' => 'ns_social_login'])` + `#free-section`.

The ns_license module ("T3Planet Shop") requires **system maintainer** access. Other users (even admins) are
redirected to the Dashboard; the strip is still shown to them.
