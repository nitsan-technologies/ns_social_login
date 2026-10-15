# Run quality checks

The repo has no PHPUnit, PHPStan or CS config. Available checks (`Library/` is third-party, skip it):

```bash
# PHP syntax, once per PHP version in range (7.4 and 8.x)
for f in $(find Classes Configuration -name "*.php") ext_*.php; do php -l "$f"; done

# composer.json
composer validate --no-check-publish

# XLF well-formedness
for f in Resources/Private/Language/*.xlf; do xmllint --noout "$f"; done
```

After adding or renaming classes in Classic mode run `typo3 dumpautoload`, then `typo3 cache:flush`.

## Before activating on a test install

If ns_license is active and has no `ns_product_license` row for `ns_social_login`, activating via the
Extension Manager or logging in can rename files in this extension (see `core.md`). Either add the package to
`PackageStates.php` by hand and run `typo3 extension:setup -e ns_social_login` (no activation event), or make
sure the browser carries the `serverConnectionTime` cookie. Check `git status` afterwards.

## Manual test (per TYPO3 major in range)

Setup: static template "Social Login", `Sociallogin.yaml` imported, Facebook test app with redirect URI
`https://<site>/oauth`, provider and storage fields filled in the site config, plugin on a page.

1. Edit form → tab "Plugin": one premium strip under "Plugin Options"; other plugins' FlexForms keep their own
   strip or none.
2. Page module: one strip above the "Social Login" label of the element.
3. "Start Free Trial" opens the T3Planet Shop (system maintainer) — with ns_license 14.5.4+ the trial wizard
   for `ns_social_login` opens. Check light and dark backend themes.
4. Frontend, page mode: Facebook login → new `fe_users` record in the storage page with source 1, identifier,
   default group; logged in; second login updates the same record.
5. Popup mode: popup opens, closes after login, parent reloads.
6. Logout link logs out; a user disabled in the backend gets the "user disabled" notice.
7. A normal felogin login (non-social user) still works without warnings in the log.

Remove test `fe_users`, avatar files, content elements and temporary backend users afterwards.
