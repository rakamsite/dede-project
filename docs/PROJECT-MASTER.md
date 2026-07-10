# DeDe — Project Master

> این فایل منبع حقیقت جاری پروژه DeDe است. هر تصمیم جدید درباره معماری، مسیرها، فازها، deploy، scope یا روش کار باید در همین فایل ثبت شود. اسناد قدیمی در صورت تعارض، تاریخی محسوب می‌شوند.

## 1. ترتیب اعتبار اطلاعات

1. آخرین دستور صریح کاربر
2. این فایل: `docs/PROJECT-MASTER.md`
3. `AGENTS.md`
4. فایل‌های ماشین‌خوان و کد واقعی پروژه
5. حافظه گفتگوهای قبلی
6. اسناد archive فقط برای سابقه تاریخی

## 2. هدف پروژه

بازسازی تدریجی و کم‌ریسک سایت WordPress/WooCommerce برند DeDe، با اهداف زیر:

- پایداری بیشتر سایت و کاهش خطاهای fatal
- خارج کردن منطق تجاری از قالب
- تقسیم قابلیت‌ها بین افزونه‌های اختصاصی با مسئولیت روشن
- قابل‌تست و قابل‌برگشت کردن تغییرات
- ایجاد Git، گزارش کار و deploy امن
- حفظ کامل رفتار فعلی سایت تا زمان جایگزینی کنترل‌شده

## 3. محیط و مسیرها

### لوکال

- پروژه: `D:\xampp\htdocs\dede`
- ساختار WordPress شامل `wp-admin`، `wp-content` و `wp-includes` است.

### سایت اصلی

- مقصد فعلی deploy: `dede.ir`
- مسیر live روی هاست: `/home/dedeir/public_html`
- `test.dede.ir` فعلاً از روند توسعه کنار گذاشته شده است.
- deploy مستقیم روی سایت اصلی انجام می‌شود و اختلال کوتاه‌مدت قابل قبول است.

### cPanel Git

- GitHub repository: `https://github.com/rakamsite/dede-project.git`
- وضعیت repo: عمومی
- cPanel repository name: `dede-project`
- cPanel repository path: `/home/dedeir/repositories/dede`
- branch کاری و deploy فعلی: `phase-0-local-baseline`
- کاربر commit و push را با GitHub Desktop انجام می‌دهد.

## 4. سیاست Git

- فقط یک branch کاری استفاده می‌شود.
- برای هر فاز یا تسک branch جدید ساخته نمی‌شود.
- هر تغییر باید commit کوچک، مشخص و قابل revert داشته باشد.
- در صورت مشکل از revert استفاده می‌شود.
- PR و workflow تیمی پیچیده فقط در صورت درخواست صریح کاربر مطرح می‌شود.
- تا وقتی کاربر branch را صریحاً تغییر نداده، branch جاری همان `phase-0-local-baseline` است.

## 5. scope فعلی Git

### داخل Git

- `README.md`
- `AGENTS.md`
- `docs/`
- `tools/`
- `.cpanel.yml`
- قالب و افزونه‌های اختصاصی تاییدشده در allowlist

### خارج از Git

- WordPress core
- `wp-config.php`
- `wp-content/uploads`
- افزونه‌های عمومی، تجاری و محیطی
- cache، logs، backups، temp
- `.git`های تو در تو
- archiveهای قدیمی
- `node_modules`
- dependencyهای build یا generated غیرضروری

### خارج از scope فعلی

- افزونه/پوشه `dedeaftersales` فعلاً در scope اصلی نیست و باید جداگانه بررسی شود.

## 6. اجزای فعلی و legacy

### قالب فعلی

`wp-content/themes/DeDeTemPlate`

قالب فعلی فقط ظاهر نیست و منطق‌های مهمی در خود دارد، از جمله:

- WooCommerce overrides
- AJAXهای خرید و حساب کاربری
- ورود و ثبت‌نام پیامکی
- پروفایل مشتری
- کیف پول
- گارانتی
- لیست قیمت و موجودی
- ویدئو، استوری و مجله
- مگامنو و جستجو
- تنظیمات CMB2
- بخشی از قرارداد حسابگر/شایگان

بنابراین تغییرات قالب باید تدریجی و regression-tested باشند.

### افزونه‌های فعلی

- `wp-content/plugins/DeDeV1`
- `wp-content/plugins/DeDeV2`

این دو افزونه فعلاً حذف نمی‌شوند. قابلیت‌ها ابتدا کنار آن‌ها به ماژول‌های جدید منتقل می‌شوند و بعداً legacy می‌شوند.

## 7. ساختار هدف

### `wp-content/plugins/dede-core/`

مسئول سرویس‌ها و foundation مشترک:

- guard وابستگی‌ها
- helperهای عمومی
- سرویس SMS
- logger
- تاریخ شمسی
- استان و شهر
- security مشترک
- feature flags
- تنظیمات پایه

### `wp-content/plugins/dede-product-operations/`

مسئول عملیات محصول و داده:

- import Excel/CSV
- variation
- SKU matching
- PDF
- لیست قیمت
- موجودی
- mapping ستون‌ها
- قرارداد و اتصال حسابگر/شایگان

### `wp-content/plugins/dede-store-features/`

مسئول قابلیت‌های مشتری و فروشگاه:

- OTP و ورود/ثبت‌نام
- پنل و پروفایل مشتری
- کیف پول
- گارانتی
- سبد خرید رهاشده
- موجود شد خبرم کن
- پیامک‌های فروش
- منطق‌های اختصاصی checkout در صورت نیاز

### `wp-content/themes/dede-theme/`

قالب هدف آینده فقط باید مسئول presentation باشد:

- templateها
- header/footer
- نمایش محصول
- CSS/JS نمایشی
- مگامنو
- اسلایدر
- بنرها

منطق تجاری جدید نباید در قالب هدف قرار گیرد.

## 8. نقشه فازها

### فاز 0 — زیرساخت

- Git و baseline
- مستندات پایه
- بکاپ
- لاگ
- export دوره‌ای
- cPanel Git deploy

### فاز 1 — Core، guard و وابستگی‌ها

- کاهش ریسک `functions.php`
- guard وابستگی WooCommerce و CMB2
- guard autoload افزونه‌ها
- ساخت foundation افزونه `dede-core`

### فاز 2 — عملیات محصول

- Excel/CSV
- PDF
- لیست قیمت
- موجودی
- variation و SKU
- انتقال به `dede-product-operations`

### فاز 3 — مسیر خرید حیاتی

- صفحه محصول
- add-to-cart
- minicart
- cart
- checkout
- variation price
- سفارش

### فاز 4 — ورود و حساب کاربری

- ورود/ثبت‌نام
- OTP
- پنل کاربری
- اطلاعات مشتری
- انتقال به `dede-store-features`

### فاز 5 — قابلیت‌های فروش و وفاداری

- کیف پول
- گارانتی
- سبد رهاشده
- پیامک فروش
- موجود شد خبرم کن

### فاز 6 — تجربه فروشگاه

- جستجو
- منو
- دسته‌بندی
- ظاهر فروشگاه

### فاز 7 — محتوا

- ویدئو
- استوری
- مجله
- تصمیم انتقال، بازطراحی یا حذف تدریجی

### فاز 8 — پاکسازی نهایی

- سبک شدن `functions.php`
- حذف duplicate/dead code
- کاهش WooCommerce override
- تثبیت مالکیت کدها
- خارج کردن منطق تجاری از قالب

## 9. وضعیت فعلی

### انجام‌شده

- فاز 0 انجام شده است.
- GitHub و cPanel Git راه‌اندازی شده‌اند.
- مسیر repo و live مشخص شده‌اند.
- deploy مستقیم روی `dede.ir` فعال است.
- فاز 1-A انجام و deploy شده است:
  - حذف load دستی WordPress/WooCommerce از قالب
  - safe require برای فایل‌های داخلی
  - guard وابستگی WooCommerce و CMB2
  - guard برای `vendor/autoload.php` در `DeDeV1` و `DeDeV2`
  - ثبت گزارش در README
- زمان فایل‌های live پس از deploy تغییر کرده و deploy اولیه موفق بوده است.

### کار جاری

- تبدیل مسیرهای hardcode deploy به allowlist معتبر.
- پس از آن، ادامه فاز 1 و ساخت اسکلت اولیه `dede-core`.

## 10. سیستم deploy

### محدودیت زیرساخت

- SSH در هاست فعال نیست.
- اطلاعات SSH قدیمی عملیاتی نیست.
- پورت‌های اصلی در دسترس: 80، 443 و 21.
- روش اصلی deploy cPanel Git Version Control است.
- FTP/WinSCP فقط fallback اضطراری است.

### روند ثابت deploy

1. Codex تغییر را روی لوکال انجام می‌دهد.
2. کاربر تغییرات را در GitHub Desktop بررسی می‌کند.
3. commit و push انجام می‌شود.
4. cPanel > Git Version Control > Manage > Pull or Deploy
5. `Update from Remote`
6. `Deploy HEAD Commit`
7. تست سریع `dede.ir`

### فایل‌ها و مسیرهای فنی

- `.cpanel.yml`
- `tools/cpanel-deploy.sh`
- allowlist: `tools/cpanel-deploy-allowlist.txt`
- initial bootstrap: `tools/cpanel-initial-deploy-files.txt`
- repo: `/home/dedeir/repositories/dede`
- live: `/home/dedeir/public_html`
- backups: `/home/dedeir/dede-deploy-backups/YYYYMMDD-HHMMSS/`
- state: `/home/dedeir/dede-deploy-state/dede.last_commit`

### رفتار deploy

- Git commit فعلی با آخرین commit موفق deploy‌شده مقایسه می‌شود.
- فقط فایل‌های تغییرکرده زیر componentهای مجاز deploy می‌شوند.
- قبل از overwrite، نسخه فعلی همان فایل live بکاپ می‌شود.
- مسیر نسبی در backup حفظ می‌شود.
- فایل جدید deploy می‌شود ولی backup قبلی ندارد.
- حذف فایل از Git فعلاً فایل live را حذف نمی‌کند و فقط log می‌شود.
- state فقط بعد از deploy موفق به‌روزرسانی می‌شود.
- هر deploy `deploy.log` دارد.

### allowlist هدف

`tools/cpanel-deploy-allowlist.txt` باید شامل rootهای تاییدشده باشد:

- `wp-content/themes/DeDeTemPlate/`
- `wp-content/themes/dede-theme/`
- `wp-content/plugins/DeDeV1/`
- `wp-content/plugins/DeDeV2/`
- `wp-content/plugins/dede-core/`
- `wp-content/plugins/dede-product-operations/`
- `wp-content/plugins/dede-store-features/`

قواعد:

- هر افزونه یا قالب اختصاصی جدید باید هنگام ایجاد به allowlist افزوده شود.
- اگر allowlist وجود نداشت، خالی بود یا entry نامعتبر داشت، deploy باید قبل از کپی fail-safe شود.
- deploy شدن افزونه به معنی فعال شدن آن در WordPress نیست؛ افزونه جدید یک‌بار باید از پیشخوان فعال شود.

## 11. بکاپ و rollback

- برای هر deploy فقط فایل‌های درگیر backup می‌شوند، نه کل سایت.
- بکاپ خارج از web root است.
- rollback اصلی برای تغییرات کد:
  1. revert commit در GitHub Desktop
  2. push
  3. Update from Remote
  4. Deploy HEAD Commit
- backup timestamped مسیر اضطراری برای بازگردانی دستی از File Manager است.
- بکاپ‌ها فعلاً خودکار حذف نمی‌شوند؛ سیاست retention بعداً جداگانه تصویب می‌شود.

## 12. لاگ و خطایابی بدون SSH

### WordPress

`wp-config.php` وارد Git نمی‌شود. در صورت نیاز و با backup هدفمند خود فایل:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);
```

### منابع خطا

- `wp-content/debug.log`
- cPanel > Metrics > Errors
- فایل‌های `error_log`
- WooCommerce > Status > Logs
- `wp-content/uploads/wc-logs`
- Browser DevTools Console/Network

## 13. گزارش کارفرما

تنها فایل گزارش قابل مشاهده کارفرما:

`README.md`

ساختار ثابت:

```md
## فاز X — عنوان

### هدف / اثر این فاز
توضیح کوتاه و واقعی

### موارد انجام‌شده
- X.1 ...
- X.2 ...
```

قواعد:

- هر چیزی که درج شده یعنی انجام شده؛ status جداگانه ننویس.
- «خروجی قابل ارائه» ننویس.
- مبلغ و قیمت ننویس.
- تبلیغ، اغراق یا توضیح فنی طولانی ننویس.
- متن نباید آن‌قدر مبهم باشد که ارزش کار معلوم نشود.
- فارسی، واقعی، کوتاه و مناسب فاکتور باشد.

## 14. مستندات داخلی

### فعال و مرجع

- `AGENTS.md`
- `docs/PROJECT-MASTER.md`
- `docs/LOCAL_BASELINE_TEST_CHECKLIST.md`
- `README.md` فقط برای کارفرما

### machine-readable / اجرایی

- `.cpanel.yml`
- `tools/cpanel-deploy.sh`
- `tools/cpanel-deploy-allowlist.txt`
- `tools/cpanel-initial-deploy-files.txt`
- ابزار export دوره‌ای

### اسناد قدیمی که باید archive شوند

این فایل‌ها ممکن است شامل تصمیم‌های قدیمی مثل staging، branchهای متعدد یا گزارش‌های موازی باشند و نباید مرجع جاری بمانند:

- `docs/CLIENT_PROGRESS_LOG.md`
- `docs/CODEX_CHANGE_REPORT_TEMPLATE.md`
- `docs/CODEX_WORKING_RULES.md`
- `docs/DEDE_ARCHITECTURE_AND_GIT_GUIDE.md`
- `docs/DEDE_DEVELOPMENT_MASTER_PLAN.md`
- `docs/GIT_AND_DEPLOY_POLICY.md`
- `docs/dede_phase_plan.xlsx`
- `tools/deploy-staging.sh`
- `tools/staging-git-deploy-guide.md`
- `tools/local-git-start.md`

پیشنهاد archive:

- `docs/archive/phase-0-original/`
- `tools/archive/phase-0-original/`

در ابتدای پوشه archive یک README کوتاه نوشته شود که این فایل‌ها تاریخی‌اند و منبع حقیقت جاری نیستند.

## 15. export دوره‌ای برای بررسی

ZIP دوره‌ای باید شامل موارد زیر باشد:

- `README.md`
- `AGENTS.md`
- `.cpanel.yml`
- `.gitignore` در صورت وجود
- کل `docs/`
- کل `tools/`
- تمام componentهای موجود در `tools/cpanel-deploy-allowlist.txt`
- `EXPORT-INFO.txt` شامل timestamp، branch و commit

نباید شامل موارد زیر باشد:

- `.git`
- `node_modules`
- cache، logs، tmp/temp
- archiveهای باینری قدیمی
- `.idea` و `.vscode`
- build/dist غیرضروری

خروجی ZIP در `D:\xampp\htdocs` ساخته می‌شود.

## 16. قانون تغییر قرارداد

هر تغییری در موارد زیر باید در همان commit این فایل را به‌روزرسانی کند:

- branch کاری
- مسیر لوکال یا سرور
- روش deploy یا rollback
- ساختار افزونه‌ها و قالب
- scope پروژه
- فازها و وضعیت جاری
- مالکیت قابلیت‌ها
- قواعد مستندات
- allowlist و componentهای جدید

تغییر کوچک باگ یا UI که قرارداد پروژه را عوض نمی‌کند، نیاز به تغییر این فایل ندارد.

## 17. قانون استفاده توسط ChatGPT

- ChatGPT دسترسی دائمی به فایل‌های لوکال کاربر ندارد.
- برای پاسخ‌های حساس درباره وضعیت فاز، قدم بعدی، deploy، معماری، پرامپت Codex یا مالکیت قابلیت‌ها، باید نسخه تازه این فایل و `AGENTS.md` از آخرین ZIP یا GitHub بررسی شود.
- اگر نسخه تازه در دسترس نباشد و احتمال تغییر وجود داشته باشد، نباید با قطعیت حدس زده شود.
- برای سوال‌های عمومی که به وضعیت جاری وابسته نیستند، مراجعه دوباره در هر پیام ضروری نیست.
