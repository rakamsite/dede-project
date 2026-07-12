from pathlib import Path

ROOT = Path.cwd()
PLUGIN = ROOT / 'wp-content/plugins/dedeaftersales'


def replace(path, old, new):
    p = ROOT / path
    text = p.read_text(encoding='utf-8')
    if old not in text:
        raise RuntimeError(f'Expected text not found in {path}: {old[:80]}')
    p.write_text(text.replace(old, new), encoding='utf-8', newline='\n')


replace('wp-content/plugins/dedeaftersales/templates/warranty-form.php',
        '<div class="w-full flex flex-col justify-center items-center mt-20">',
        '<div class="dede-aftersales dede-aftersales--warranty w-full flex flex-col justify-center items-center mt-20">')
replace('wp-content/plugins/dedeaftersales/templates/warranty-form.php',
        '<div class="container mx-auto p-6 max-w-2xl">',
        '<div class="dede-aftersales dede-aftersales--warranty container mx-auto p-6 max-w-2xl">')
replace('wp-content/plugins/dedeaftersales/templates/ticket-form.php',
        '<div class="w-full flex flex-col justify-center items-center mt-20">',
        '<div class="dede-aftersales dede-aftersales--tickets w-full flex flex-col justify-center items-center mt-20">')
replace('wp-content/plugins/dedeaftersales/templates/ticket-form.php',
        '<div class="container mx-auto p-4 max-w-2xl dir-rtl">',
        '<div class="dede-aftersales dede-aftersales--tickets container mx-auto p-4 max-w-2xl dir-rtl">')
replace('wp-content/plugins/dedeaftersales/templates/ticket-list.php',
        '<div class="w-full flex flex-col justify-center items-center mt-20">',
        '<div class="dede-aftersales dede-aftersales--tickets w-full flex flex-col justify-center items-center mt-20">')
replace('wp-content/plugins/dedeaftersales/templates/ticket-list.php',
        '<div class="container mx-auto p-4 max-w-4xl dir-rtl">',
        '<div class="dede-aftersales dede-aftersales--tickets container mx-auto p-4 max-w-4xl dir-rtl">')
replace('wp-content/plugins/dedeaftersales/templates/ticket-list.php',
        '<div id="ticket-popup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">',
        '<div id="ticket-popup" class="dede-aftersales dede-aftersales--tickets fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">')

(PLUGIN / 'assets/css/warranty-styles.css').write_text('''@tailwind base;
@tailwind components;
@tailwind utilities;

.dede-aftersales--warranty.container { @apply max-w-2xl mx-auto dir-rtl; }
.dede-aftersales--warranty input,
.dede-aftersales--warranty select,
.dede-aftersales--warranty textarea { @apply border border-gray-300 rounded-md p-3 w-full focus:outline-none focus:ring-2 focus:ring-blue-500; }
.dede-aftersales--warranty .warranty-section { @apply border border-gray-200 rounded-lg p-4 space-y-4; }
.dede-aftersales--warranty .warranty-section-title { @apply text-base font-semibold text-gray-800; }
.dede-aftersales--warranty .warranty-hologram-preview img { @apply w-full max-w-xs rounded-md border border-gray-200; }
.dede-aftersales--warranty .hologram-code-grid { margin-top:.25rem;display:grid;grid-template-columns:repeat(6,48px);gap:.5rem;justify-content:end; }
.dede-aftersales--warranty .hologram-digit { @apply text-center text-lg font-semibold rounded-md; width:48px;height:48px;padding:0;border:2px solid #6b7280;background:#fff;box-sizing:border-box;text-align:center;padding-inline:0;line-height:1; }
.dede-aftersales--warranty .hologram-digit:focus { border-color:#3b82f6;box-shadow:0 0 0 2px rgba(59,130,246,.2); }
.dede-aftersales--warranty button:hover { background-color:#2e3192 !important; }
#warranty-success-popup { @apply fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50; }
#warranty-success-popup > div { @apply bg-green-600 text-white p-6 rounded-lg text-center; }
''', encoding='utf-8', newline='\n')

(PLUGIN / 'assets/css/warranty-part-styles.css').write_text('''@tailwind base;
@tailwind components;
@tailwind utilities;

.dede-aftersales--warranty.container { @apply max-w-2xl mx-auto dir-rtl; }
.dede-aftersales--warranty input,
.dede-aftersales--warranty select,
.dede-aftersales--warranty textarea { @apply border border-gray-300 rounded-md p-3 w-full focus:outline-none focus:ring-2 focus:ring-blue-500; }
.dede-aftersales--warranty button:hover { background-color:#2e3192 !important; }
#warranty-part-success-popup { @apply fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50; }
#warranty-part-success-popup > div { @apply bg-green-600 text-white p-6 rounded-lg text-center; }
''', encoding='utf-8', newline='\n')

(PLUGIN / 'assets/css/ticket-styles.css').write_text('''@tailwind base;
@tailwind components;
@tailwind utilities;

.dede-aftersales--tickets.container { @apply max-w-4xl mx-auto dir-rtl; }
.dede-aftersales--tickets input,
.dede-aftersales--tickets select,
.dede-aftersales--tickets textarea { @apply border border-gray-300 rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500; }
.dede-aftersales--tickets textarea { @apply resize-y; }
.dede-aftersales--tickets button { @apply bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600; display:inline-block; }
@keyframes sts-spin { to { transform:rotate(360deg); } }
.dede-aftersales--tickets .animate-spin { animation:sts-spin 1s linear infinite; }
.dede-aftersales--tickets .alert { @apply p-4 rounded-md mb-4; }
.dede-aftersales--tickets .alert-success { @apply bg-green-100 text-green-700; }
.dede-aftersales--tickets .alert-error { @apply bg-red-100 text-red-700; }
.dede-aftersales--tickets table { @apply w-full border-collapse; }
.dede-aftersales--tickets th,
.dede-aftersales--tickets td { @apply border border-gray-200 p-3 text-center; }
.dede-aftersales--tickets tr:hover { @apply bg-gray-50; }
#ticket-popup { @apply fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center; }
#ticket-popup > div { @apply bg-white p-6 rounded-lg shadow-lg max-w-2xl w-full relative; max-height:80vh;overflow-y:auto; }
#close-popup { @apply absolute top-4 left-4 text-gray-600 hover:text-gray-800 cursor-pointer; font-size:60px !important; }
.dede-aftersales--tickets button.mt-2.bg-blue-600.text-white.py-2.px-4.rounded-md.hover\\:bg-blue-700.focus\\:outline-none.focus\\:ring-2.focus\\:ring-blue-600 { background:#2f2483;border-radius:8px;width:100%; }
.dede-aftersales--tickets .success-message { transition:opacity .3s ease-in-out; }
.dede-aftersales--tickets button:disabled { opacity:.6;cursor:not-allowed; }
''', encoding='utf-8', newline='\n')

replace('wp-content/plugins/dedeaftersales/simple-ticket-system.php', 'Version: 1.3', 'Version: 1.3.1')
replace('wp-content/plugins/dedeaftersales/includes/warranty.php',
        "function sts_enqueue_warranty_assets() {\n    wp_enqueue_script('warranty-scripts', plugin_dir_url(STS_PLUGIN_FILE) . 'assets/js/warranty-scripts.js', array('jquery'), '1.0', true);\n    wp_enqueue_style('warranty-styles', plugin_dir_url(STS_PLUGIN_FILE) . 'assets/css/warranty-styles.css', array(), '1.0');\n}",
        "function sts_enqueue_warranty_assets() {\n    $script_path = STS_PLUGIN_DIR . 'assets/js/warranty-scripts.js';\n    $style_path = STS_PLUGIN_DIR . 'assets/css/warranty-styles.css';\n    $script_version = file_exists($script_path) ? (string) filemtime($script_path) : '1.0';\n    $style_version = file_exists($style_path) ? (string) filemtime($style_path) : '1.0';\n\n    wp_enqueue_script('warranty-scripts', plugin_dir_url(STS_PLUGIN_FILE) . 'assets/js/warranty-scripts.js', array('jquery'), $script_version, true);\n    wp_enqueue_style('warranty-styles', plugin_dir_url(STS_PLUGIN_FILE) . 'assets/css/warranty-styles.css', array(), $style_version);\n}")

allow = ROOT / 'tools/cpanel-deploy-allowlist.txt'
text = allow.read_text(encoding='utf-8')
if 'wp-content/plugins/dedeaftersales/' not in text:
    allow.write_text(text.rstrip() + '\nwp-content/plugins/dedeaftersales/\n', encoding='utf-8', newline='\n')

manifest = ROOT / 'tools/cpanel-initial-deploy-files.txt'
text = manifest.read_text(encoding='utf-8')
for path in sorted(p.relative_to(ROOT).as_posix() for p in PLUGIN.rglob('*') if p.is_file()):
    if path not in text:
        text += path + '\n'
manifest.write_text(text, encoding='utf-8', newline='\n')

readme = ROOT / 'README.md'
text = readme.read_text(encoding='utf-8')
if '- `wp-content/plugins/dedeaftersales`' not in text:
    text = text.replace('- `wp-content/plugins/dede-store-features`\n', '- `wp-content/plugins/dede-store-features`\n- `wp-content/plugins/dedeaftersales`\n')
if '## فاز 5 — ورود خدمات پس از فروش به چرخه توسعه' not in text:
    text += '''\n\n## فاز 5 — ورود خدمات پس از فروش به چرخه توسعه\n\nافزونه `dedeaftersales` با قابلیت‌های تیکت، گارانتی و درخواست خدمات وارد scope رسمی Git و deploy شد. selectorهای عمومی CSS آن به محدوده خود افزونه محدود شدند تا روی دکمه‌ها، ورودی‌ها و جدول‌های کل سایت اثر نگذارند.\n'''
readme.write_text(text, encoding='utf-8', newline='\n')

master = ROOT / 'docs/PROJECT-MASTER.md'
text = master.read_text(encoding='utf-8')
text = text.replace('- branch کاری و deploy فعلی: `phase-0-local-baseline`', '- branch اصلی و deploy فعلی: `main`')
text = text.replace('- کاربر commit و push را با GitHub Desktop انجام می‌دهد.', '- توسعه و مدیریت نسخه از طریق GitHub و ابزارهای cloud انجام می‌شود؛ deploy نهایی در cPanel دستی است.')
text = text.replace('### خارج از scope فعلی\n\n- افزونه/پوشه `dedeaftersales` فعلاً در scope اصلی نیست و باید جداگانه بررسی شود.\n', '### افزونه خدمات پس از فروش\n\n- `wp-content/plugins/dedeaftersales/` داخل scope رسمی Git، allowlist deploy و برنامه توسعه قرار دارد.\n')
if '### افزونه خدمات پس از فروش\n\n`wp-content/plugins/dedeaftersales`' not in text:
    marker = 'این دو افزونه فعلاً حذف نمی‌شوند. قابلیت‌ها ابتدا کنار آن‌ها به ماژول‌های جدید منتقل می‌شوند و بعداً legacy می‌شوند.\n'
    text = text.replace(marker, marker + '\n### افزونه خدمات پس از فروش\n\n`wp-content/plugins/dedeaftersales` مسئول تیکت، ثبت درخواست خدمات و فعال‌سازی گارانتی است. این افزونه مستقل می‌ماند و به‌تدریج از نظر امنیت، UI و استفاده از سرویس‌های مشترک `dede-core` بازسازی می‌شود.\n')
if '### فاز 6 — خدمات پس از فروش' not in text:
    text += '''\n\n### فاز 6 — خدمات پس از فروش\n\n- ورود `dedeaftersales` به Git و deploy کنترل‌شده\n- حذف selectorها و assetهای سراسری\n- بررسی nonce، upload، دسترسی تیکت و اعلان‌ها\n- بازسازی UI تیکت و گارانتی در موبایل و دسکتاپ\n- انتقال سرویس‌های مشترک به `dede-core`\n'''
master.write_text(text, encoding='utf-8', newline='\n')

quality = ROOT / '.github/workflows/quality-check.yml'
quality.write_text('''name: Quality check

on:
  pull_request:
    branches:
      - main
  workflow_dispatch:

permissions:
  contents: read

jobs:
  static-checks:
    name: PHP, JavaScript, CSS scope and deploy script checks
    runs-on: ubuntu-latest
    steps:
      - name: Check out repository
        uses: actions/checkout@v4
      - name: Ensure PHP CLI is available
        run: |
          set -Eeuo pipefail
          if ! command -v php >/dev/null 2>&1; then sudo apt-get update && sudo apt-get install -y php-cli; fi
          php --version
      - name: Check PHP syntax
        run: |
          set -Eeuo pipefail
          find wp-content/plugins/dede-store-features wp-content/plugins/dedeaftersales -type f -name '*.php' -print0 | xargs -0 -r -n 1 php -l
          php -l wp-content/themes/DeDeTemPlate/woocommerce/myaccount/my-account.php
          php -l wp-content/themes/DeDeTemPlate/woocommerce/checkout/form-checkout.php
      - name: Check JavaScript syntax
        run: |
          set -Eeuo pipefail
          find wp-content/plugins/dede-store-features/assets/js wp-content/plugins/dedeaftersales/assets/js -type f -name '*.js' -print0 | xargs -0 -r -n 1 node --check
      - name: Prevent global after-sales CSS selectors
        run: |
          set -Eeuo pipefail
          if grep -RInE '^[[:space:]]*(button|input|select|textarea|table|th|td|tr)([[:space:]:,{.#\\[])|^[[:space:]]*\\.container[[:space:]]*\\{' wp-content/plugins/dedeaftersales/assets/css; then
            echo 'Unscoped global selector found in dedeaftersales CSS.' >&2
            exit 1
          fi
      - name: Check cPanel deploy script syntax
        run: bash -n tools/cpanel-deploy.sh
''', encoding='utf-8', newline='\n')
