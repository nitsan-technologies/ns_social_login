# Feature — Facebook login and the "Social Login" plugin

*Status: Done (free version, Facebook only). Behaviour below is code-read; defects live in `core.md`.*

## Setup

1. Include the static template "Social Login".
2. Import `EXT:ns_social_login/Configuration/Routes/Sociallogin.yaml` into the site config (callback `/oauth`).
3. Site module → site → tab "Providers": enable Facebook, App ID, App secret, scope (default `email`),
   display mode (page/popup); storage page for `fe_users`, default user group, file storage uid and avatar
   folder (must exist, otherwise no avatar).
4. In the Facebook app, allow the redirect URI `https://<site>/oauth`.
5. Place "Social Login" on a page; plugin options: use the global style or pick style 1–3.

## Login flow

1. `listAction()` renders a button per enabled provider; the link is page type `1712813073` with
   `tx_nssociallogin_pi1[provider]` and `[redirect]` (popup mode opens it via `Popup.js`).
2. On that request `SocialLoginService::init()` stores the provider in the Hybridauth session;
   `getUser()` (thanks to `FE_fetchUserIfNoSession`) calls `AuthUtility::authenticate()`, which redirects to
   Facebook and, on the `/oauth` callback, returns the profile.
3. The `fe_users` record is matched by storage page + source + identifier, then updated or inserted
   (username = e-mail or cleaned display name, made unique with a counter; random hashed password; default
   group for new users). The photo is downloaded to `<file_storage>:<avatar_image>/<provider>_<id>.jpg`.
4. `authUser()` returns 200 → user logged in; `endpointAction()` redirects to `list` with `isRedirect`, which
   closes the popup window.

## Logout and disabled users

- The plugin shows a logout link (`logintype=logout`) for logged-in users; `LogOffHook` disconnects Hybridauth.
- A session whose user was disabled in the backend is logged out and shows the `userDisabled` notice.

## Premium-only (not in this repo)

Per the product page: login with multiple platforms (Google, X, Xing, Apple ID, LinkedIn and more — "10+
social networks"). Popup mode, custom labels/icons and the three button styles exist in the free code as well.
