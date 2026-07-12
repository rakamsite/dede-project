# Deploy و Rollback پروژه DeDe

## مدل عملیاتی قطعی

- منبع کد: branch `main` در GitHub
- ریپوی cPanel: `/home/dedeir/repositories/dede`
- مسیر سایت: `/home/dedeir/public_html`
- SSH و cPanel API Token در دسترس نیست.
- Deploy از GitHub Actions انجام نمی‌شود.
- روش اصلی، Git Version Control خود cPanel است.

## Deploy

پس از Merge یا Push تغییرات به `main`:

1. وارد cPanel شوید.
2. به **Git Version Control** بروید.
3. ریپوی `dede-project` را Manage کنید.
4. در بخش **Pull or Deploy** ابتدا **Update from Remote** را اجرا کنید.
5. SHA نمایش‌داده‌شده را با آخرین commit شاخه `main` تطبیق دهید.
6. سپس **Deploy HEAD Commit** را اجرا کنید.
7. سایت، حساب کاربری، Checkout و لاگ Deploy را بررسی کنید.

فایل `.cpanel.yml` اسکریپت `tools/cpanel-deploy.sh` را اجرا می‌کند. این اسکریپت فقط فایل‌های تغییرکرده داخل allowlist را منتشر می‌کند و قبل از جایگزینی یا حذف کنترل‌شده، از فایل live بکاپ می‌گیرد.

## Rollback سریع

### روش اول: خاموش‌کردن قابلیت

برای افزونه‌های جدیدی مثل `DeDe Store Features`، ابتدا افزونه را از پیشخوان وردپرس غیرفعال کنید. قالب در صورت غیرفعال‌بودن افزونه به نسخه legacy برمی‌گردد.

### روش دوم: Git Revert

1. commit مشکل‌دار را در GitHub Revert کنید.
2. تغییر Revert را به `main` برسانید.
3. در cPanel، **Update from Remote** را اجرا کنید.
4. سپس **Deploy HEAD Commit** را بزنید.
5. سایت و لاگ Deploy را کنترل کنید.

اسکریپت Deploy تغییرات Revert را نیز از آخرین commit موفق تا HEAD محاسبه می‌کند. حذف فایل فقط زیر componentهای allowlist انجام می‌شود و نسخه live پیش از حذف در بکاپ timestamped نگهداری می‌شود.

## بکاپ و لاگ

- بکاپ‌ها: `/home/dedeir/dede-deploy-backups/YYYYMMDD-HHMMSS-*/`
- وضعیت آخرین Deploy موفق: `/home/dedeir/dede-deploy-state/dede.last_commit`
- هر Deploy دارای `deploy.log` و فهرست عملیات اعمال‌شده است.
- اگر کپی فایل در میانه Deploy شکست بخورد، اسکریپت برای فایل‌های اعمال‌شده تلاش به بازگردانی خودکار می‌کند.

## محدودیت Rollback

Git فقط کد قالب و افزونه‌های داخل allowlist را برمی‌گرداند. سفارش‌ها، کاربران، متاکی‌های ثبت‌شده و سایر تغییرات دیتابیس با Git Revert بازگردانده نمی‌شوند. هر تغییر ساختاری دیتابیس باید migration و برنامه بازگشت مستقل داشته باشد.
