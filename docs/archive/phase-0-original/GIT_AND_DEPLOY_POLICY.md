# DeDe Git and Deploy Policy

## Repository type

Use one monorepo at the WordPress root.

## Why monorepo?

The current DeDe project has one theme and two dependent plugins. A single repository makes cross-cutting changes easier to review, test and deploy.

## Tracked paths

- `docs/`
- `tools/`
- `wp-content/themes/DeDeTemPlate/`
- `wp-content/plugins/DeDeV1/`
- `wp-content/plugins/DeDeV2/`
- future modular plugins:
  - `wp-content/plugins/dede-core/`
  - `wp-content/plugins/dede-product-operations/`
  - `wp-content/plugins/dede-store-features/`

## Ignored paths

- WordPress core: `wp-admin/`, `wp-includes/`, root WP PHP files
- `wp-config.php`
- uploads and generated media
- cache/logs/backups
- SQL dumps

## Branch model

- `main`: stable staging-ready branch
- `phase/x-name`: one phase or sprint branch
- `hotfix/x-name`: urgent bug fix

## Commit format

Recommended:

- `chore: initialize repository`
- `fix(core): guard WooCommerce dependency`
- `fix(cart): prevent duplicate ajax add to cart`
- `refactor(otp): move OTP logic to dede-store-features`
- `docs: update client progress log for phase 1.1`

## Definition of Done

A task is not complete until:

1. Code is changed only inside the task scope.
2. Persian/UTF-8 strings are intact.
3. A basic manual test is documented.
4. `docs/CLIENT_PROGRESS_LOG.md` is updated.
5. `docs/CODEX_CHANGE_REPORT_TEMPLATE.md` is filled or referenced.
