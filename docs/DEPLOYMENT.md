# راه‌اندازی Deploy و Rollback پروژه DeDe

## فایل‌ها

- `.github/workflows/deploy-production.yml`
- `.github/workflows/rollback-production.yml`
- `tools/cpanel-api-deploy.sh`
- `tools/cpanel-deploy.sh`
- `docs/DEPLOYMENT.md`

## تنظیم یک‌باره

### 1. ساخت API Token در cPanel

در cPanel به **Security → Manage API Tokens** بروید و یک توکن با نامی مثل `github-dede-deploy` بسازید.

### 2. ساخت GitHub Actions Secrets

در GitHub به **Settings → Secrets and variables → Actions** بروید و این سه Repository Secret را بسازید:

- `CPANEL_HOST`: hostname داخل URL ورود cPanel، بدون مسیر. نمونه: `server.example.com`
- `CPANEL_USER`: `dedeir`
- `CPANEL_API_TOKEN`: توکن مرحله قبل

### 3. مجوز نوشتن Workflow

در **Settings → Actions → General → Workflow permissions** گزینه **Read and write permissions** را فعال کنید. این دسترسی برای tagهای تولید و rollback commit لازم است.

## Deploy

در GitHub:

**Actions → Deploy production → Run workflow**

Workflow فقط دستی است و Push یا Merge به‌تنهایی سایت را تغییر نمی‌دهد.

## Rollback

در GitHub:

**Actions → Roll back production → Run workflow**

- `target_ref`: برای بازگشت سریع مقدار `production-previous` را نگه دارید؛ یا SHA/tag نسخه موردنظر را وارد کنید.
- `confirmation`: دقیقاً `ROLLBACK` بنویسید.

Rollback بدون force-push، یک commit عادی روی `main` می‌سازد، کدهای قابل‌انتشار نسخه انتخاب‌شده را برمی‌گرداند و همان commit را deploy می‌کند.

## اولین Deploy

Workflow پیش از انتشار، SHA آخرین نسخه ثبت‌شده در cPanel را می‌خواند و آن را به‌عنوان نسخه سالم قبلی ثبت می‌کند. بنابراین در حالت عادی، حتی اولین Deploy از GitHub Actions نیز یک نقطه بازگشت خواهد داشت. اگر cPanel هنوز هیچ `last_deployment` معتبری ثبت نکرده باشد، قبل از اولین انتشار باید SHA نسخه فعلی را دستی نگه دارید.

## محدودیت مهم Rollback

Rollback این سیستم فایل‌های قالب و افزونه‌های داخل allowlist را برمی‌گرداند. تغییرات دیتابیس، سفارش‌ها، کاربران و داده‌های تولید با Git بازگردانده نمی‌شوند. هر تغییر ساختاری دیتابیس باید migration نسخه‌دار و برنامه بازگشت جداگانه داشته باشد.

## رفتار در خطا

اگر Workflow انتشار قرمز شد، انتشار موفق ثبت نمی‌شود و tag `production-previous` روی نسخه‌ای می‌ماند که پیش از شروع Deploy روی cPanel فعال بوده است. برای بازگشت، Workflow **Roll back production** را با مقدار پیش‌فرض `production-previous` اجرا کنید.
