# سند معماری و ساختار Git پروژه DeDe

## 1. تصمیم اصلی

برای این پروژه، پیشنهاد اصلی **یک monorepo** است؛ یعنی قالب و افزونه‌ها در یک مخزن Git نگهداری شوند.

دلیل:

- قالب و افزونه‌ها به هم وابسته‌اند.
- تغییرات معمولاً cross-component هستند.
- کارفرما یک پروژه واحد می‌بیند، نه سه پروژه جدا.
- گزارش مالی و فازی راحت‌تر می‌شود.
- Codex در یک context واحد بهتر می‌تواند وابستگی‌ها را ببیند.

## 2. ساختار پیشنهادی اولیه Git

در شروع، ساختار را تا حد ممکن شبیه WordPress نگه می‌داریم تا deploy ساده باشد:

```text
dede-project/
├── README.md
├── docs/
│   ├── DEDE_DEVELOPMENT_MASTER_PLAN.md
│   ├── DEDE_ARCHITECTURE_AND_GIT_GUIDE.md
│   ├── CODEX_CHANGE_REPORT_TEMPLATE.md
│   ├── CLIENT_PROGRESS_LOG.md
│   └── CODEX_WORKING_RULES.md
├── wp-content/
│   ├── themes/
│   │   └── DeDeTemPlate/
│   └── plugins/
│       ├── DeDeV1/
│       └── DeDeV2/
├── tools/
│   └── deploy-notes.md
└── logs/
    └── .gitkeep
```

این ساختار برای شروع عملیاتی‌تر از ساختارهای پیچیده است.

## 3. ساختار هدف بعد از جداسازی

در طول پروژه، ساختار به مرور به این سمت می‌رود:

```text
dede-project/
├── docs/
├── wp-content/
│   ├── themes/
│   │   └── dede-theme/
│   └── plugins/
│       ├── dede-core/
│       ├── dede-product-operations/
│       ├── dede-store-features/
│       ├── DeDeV1-legacy/
│       └── DeDeV2-legacy/
└── tools/
```

نکته مهم:

> در شروع، افزونه‌های قدیمی را حذف یا ادغام نمی‌کنیم. اول کنارشان افزونه‌های تمیز می‌سازیم و بعد قابلیت‌ها را مرحله‌ای منتقل می‌کنیم.

## 4. آیا همه چیز یک افزونه شود؟

برای DeDe، یک افزونه خیلی بزرگ پیشنهاد نمی‌شود. دلیل:

- عملیات محصول، Excel، PDF و import سنگین و مدیریتی است.
- کیف پول، گارانتی، OTP و پنل مشتری مستقیم با کاربر و فروش درگیر است.
- سرویس‌های مشترک مثل SMS، تاریخ، logger و guard باید مستقل و سبک باشند.
- اگر همه چیز یک افزونه باشد، دوباره همان قاطی‌پاتی فعلی فقط از قالب منتقل می‌شود به یک افزونه عظیم.

اما نباید هم ده‌ها افزونه کوچک بسازیم.

پیشنهاد هدف:

### 4.1 `dede-core`

افزونه مشترک و سبک.

مسئولیت‌ها:

- guard وابستگی‌ها
- helperهای عمومی
- SMS service
- logger
- تاریخ شمسی
- استان/شهر
- security مشترک
- feature flags
- تنظیمات پایه

### 4.2 `dede-product-operations`

افزونه عملیات محصول و مدیریت دیتای فروشگاه.

مسئولیت‌ها:

- import Excel/CSV
- import variation
- SKU matching
- PDF
- لیست قیمت
- موجودی
- mapping ستون‌ها
- متاهای حسابگر/شایگان

### 4.3 `dede-store-features`

افزونه قابلیت‌های اختصاصی فروشگاه و مشتری.

مسئولیت‌ها:

- OTP login/register
- پنل مشتری
- پروفایل
- کیف پول
- گارانتی
- سبد خرید رهاشده
- موجود شد خبرم کن
- پیامک‌های فروش
- منطق‌های اختصاصی checkout، در صورت نیاز

### 4.4 قالب `dede-theme`

مسئولیت‌ها:

- ظاهر سایت
- templateها
- header/footer
- single/archive product display
- CSS/JS نمایشی
- مگامنو UI
- اسلایدر و بنرهای نمایشی
- قالب‌بندی صفحات

## 5. قانون مالکیت قابلیت‌ها

| نوع قابلیت | محل درست |
|---|---|
| ظاهر، layout، template، CSS/JS نمایشی | قالب |
| ورود/ثبت‌نام، OTP، پروفایل، پنل مشتری | `dede-store-features` |
| کیف پول، گارانتی، پیامک فروش | `dede-store-features` |
| import، Excel، PDF، SKU، لیست قیمت | `dede-product-operations` |
| سرویس پیامک، logger، guard، تاریخ، helper | `dede-core` |
| WooCommerce hookهای حیاتی checkout/cart | ترجیحاً افزونه؛ template فقط در قالب |
| integration حسابگر/شایگان | بسته به ماهیت، معمولاً `dede-product-operations` یا `dede-core` |

## 6. روش مهاجرت بدون ریسک

برای هر قابلیت:

1. رفتار فعلی ثبت شود.
2. تست دستی و ورودی/خروجی فعلی نوشته شود.
3. کد جدید در محل درست ساخته شود.
4. مسیر قدیمی به شکل امن خاموش یا proxy شود.
5. تست تکرار شود.
6. گزارش تغییرات نوشته شود.
7. قابلیت legacy فقط وقتی حذف شود که حداقل یک چرخه تست موفق داشته باشد.

## 7. Branch Strategy

پیشنهاد ساده و عملیاتی:

```text
main        نسخه پایدار و قابل deploy
develop     نسخه تجمیع‌شده توسعه
phase/x     branch هر فاز
fix/x       branch رفع خطای محدود
hotfix/x    branch خطای فوری روی production
```

نمونه:

```text
phase/01-core-guards
phase/02-product-import
phase/03-checkout-stability
fix/otp-rate-limit
hotfix/woocommerce-fatal-guard
```

## 8. Commit Message

فرمت پیشنهادی:

```text
[Phase 1.2] Add WooCommerce dependency guard
[Phase 2.4] Fix SKU duplicate handling in import
[Hotfix] Prevent fatal error when WooCommerce is inactive
```

## 9. Tag و نسخه

برای تحویل به کارفرما یا deploy مهم:

```text
v0.1-stability
v0.2-product-ops
v0.3-checkout-stable
v0.4-account-stable
```

## 10. نکته مهم درباره Codex

Codex نباید آزادانه کل پروژه را refactor کند. هر task باید فقط یک محدوده مشخص داشته باشد. بعد از هر تغییر، Codex باید طبق قالب گزارش تغییرات، گزارش دقیق بنویسد.
