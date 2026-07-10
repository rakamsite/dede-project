# قوانین کار با Codex در پروژه DeDe

این فایل باید در هر task به Codex یادآوری شود.

## 1. قانون محدوده

Codex فقط باید روی محدوده همان task کار کند. تغییرات خارج از محدوده ممنوع است.

ممنوع:

- refactor کل قالب
- تغییر همزمان چند قابلیت نامرتبط
- دستکاری checkout بدون درخواست صریح
- تغییر متن‌های فارسی بدون نیاز
- حذف فایل یا کد legacy بدون تست و گزارش

## 2. قانون فارسی و UTF-8

بسیار مهم:

- هیچ متن فارسی نباید خراب شود.
- هیچ متن فارسی نباید به `????` تبدیل شود.
- encoding فایل‌ها باید UTF-8 بماند.
- labelها، placeholderها، validationها، دکمه‌ها، پیام‌ها و متن‌های تاریخ نباید بی‌دلیل تغییر کنند.
- در صورت نیاز به تغییر متن فارسی، تغییر باید دقیق و محدود باشد.

## 3. قانون WooCommerce

- قبل از استفاده از کلاس‌ها و توابع WooCommerce، guard لازم اضافه شود.
- checkout، cart، order و payment حساس هستند.
- اگر task روی WooCommerce اثر دارد، تست دستی cart و checkout اجباری است.
- WooCommerce overrideها بدون دلیل نباید بیشتر شوند.

## 4. قانون قالب و افزونه

- منطق تجاری جدید نباید به قالب اضافه شود.
- قالب فقط باید UI و template را نگه دارد.
- منطق SMS، OTP، wallet، warranty، import، export و integrations باید به افزونه مناسب منتقل شود.
- اگر انتقال کامل در همان task پرریسک است، یک adapter/proxy امن ساخته شود و انتقال در گزارش ثبت شود.

## 5. قانون گزارش‌دهی

بعد از هر تغییر، Codex باید این موارد را بنویسد:

1. چه مشکلی حل شد؟
2. چه فایل‌هایی تغییر کرد؟
3. چه تست‌هایی انجام شد؟
4. چه چیزی عمداً دست نخورد؟
5. چه ریسکی باقی مانده؟
6. متن قابل ارائه به کارفرما چیست؟

گزارش باید طبق `CODEX_CHANGE_REPORT_TEMPLATE.md` باشد.

## 6. قانون تست حداقلی

قبل از پایان task، اگر مرتبط است باید این مسیرها تست شوند:

- صفحه اصلی
- صفحه محصول
- سبد خرید
- checkout
- ورود/ثبت‌نام
- پنل مشتری
- مدیریت وردپرس
- AJAX مربوط به task

## 7. قالب دستور پیشنهادی برای Codex

```text
You are working on the DeDe WordPress/WooCommerce project.

Read these docs before changing code:
- docs/DEDE_DEVELOPMENT_MASTER_PLAN.md
- docs/DEDE_ARCHITECTURE_AND_GIT_GUIDE.md
- docs/CODEX_WORKING_RULES.md
- docs/CODEX_CHANGE_REPORT_TEMPLATE.md

Task scope:
[اینجا محدوده دقیق task نوشته شود]

Hard rules:
- Do not refactor unrelated files.
- Do not change Persian labels/text unless required.
- Preserve UTF-8 Persian text; never convert Persian text to ???? or broken encoding.
- Do not touch checkout/cart/order logic unless explicitly required.
- Keep changes minimal, testable, and reversible.
- After finishing, update docs/CLIENT_PROGRESS_LOG.md using the required report format.
```
