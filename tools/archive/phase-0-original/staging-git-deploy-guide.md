# Staging deploy guide for test.dede.ir

## Recommended model

The staging site root itself is a Git working tree. You push from local to GitHub, then run one command on staging:

```bash
bash tools/deploy-staging.sh
```

This pulls the latest code into the live staging folder. No manual import is needed for code-only changes.

## First-time staging setup

Before doing this, make a full backup of staging files and database.

```bash
cd /home/USER/domains/test.dede.ir/public_html
pwd
```

Initialize Git if the folder is not already a repo:

```bash
git init
git remote add origin https://github.com/YOUR_USER/YOUR_REPO.git
git fetch origin main
git checkout -f main
```

If the folder already contains WordPress core, uploads, caches and config, they should stay untracked because `.gitignore` excludes them.

## Normal deploy

```bash
cd /home/USER/domains/test.dede.ir/public_html
git pull origin main
bash tools/deploy-staging.sh
```

## Important notes

- `wp-config.php` must remain server-specific and should not be committed.
- `uploads/` must not be committed.
- Database changes are not deployed by Git. Any database/schema setting change must be documented in `docs/CLIENT_PROGRESS_LOG.md` and ideally handled by plugin activation/version upgrade code.
