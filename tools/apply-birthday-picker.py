from pathlib import Path

ROOT = Path.cwd()


def replace(path: str, old: str, new: str) -> None:
    file_path = ROOT / path
    text = file_path.read_text(encoding='utf-8')
    if old not in text:
        raise RuntimeError(f'Expected block not found in {path}: {old[:120]!r}')
    file_path.write_text(text.replace(old, new, 1), encoding='utf-8', newline='\n')


def append_once(path: str, marker: str, content: str) -> None:
    file_path = ROOT / path
    text = file_path.read_text(encoding='utf-8')
    if marker not in text:
        file_path.write_text(text.rstrip() + '\n\n' + content.strip() + '\n', encoding='utf-8', newline='\n')


replace(
    'wp-content/plugins/dede-store-features/includes/class-dede-store-features-profile.php',
    """        $today = current_datetime();
        $jalali_today = $this->gregorian_to_jalali((int) $today->format('Y'), (int) $today->format('n'), (int) $today->format('j'));
        $max_birth_year = ((int) $jalali_today[0]) - 10;
""",
    """        $maximum_birth_date = current_datetime()->modify('-15 years')->setTime(0, 0, 0);
        $maximum_birth_jalali = $this->gregorian_to_jalali(
            (int) $maximum_birth_date->format('Y'),
            (int) $maximum_birth_date->format('n'),
            (int) $maximum_birth_date->format('j')
        );
        $max_birth_year = (int) $maximum_birth_jalali[0];
        $max_birth_date = sprintf(
            '%04d/%02d/%02d',
            (int) $maximum_birth_jalali[0],
            (int) $maximum_birth_jalali[1],
            (int) $maximum_birth_jalali[2]
        );
""",
)

replace(
    'wp-content/plugins/dede-store-features/includes/trait-dede-store-features-validation.php',
    """        $cutoff = current_datetime()->modify('-10 years')->setTime(0, 0, 0);
        if ($birth_date >= $cutoff) {
            return new WP_Error('under_age', 'سن کاربر باید بیشتر از ۱۰ سال باشد.');
        }
""",
    """        $cutoff = current_datetime()->modify('-15 years')->setTime(0, 0, 0);
        if ($birth_date > $cutoff) {
            return new WP_Error('under_age', 'سن کاربر باید حداقل ۱۵ سال باشد.');
        }
""",
)

old_birthday = """                    <fieldset class=\"dede-field dede-field--wide dede-birthday\">
                        <legend>تاریخ تولد <em>اختیاری</em></legend>
                        <div>
                            <select name=\"birthday_day\" aria-label=\"روز تولد\"><option value=\"\">روز</option><?php for ($day = 1; $day <= 31; $day++) : ?><option value=\"<?php echo esc_attr($day); ?>\" <?php selected((int) $profile['birthday_day'], $day); ?>><?php echo esc_html($day); ?></option><?php endfor; ?></select>
                            <select name=\"birthday_month\" aria-label=\"ماه تولد\"><option value=\"\">ماه</option><?php foreach ($months as $number => $month) : ?><option value=\"<?php echo esc_attr($number); ?>\" <?php selected((int) $profile['birthday_month'], $number); ?>><?php echo esc_html($month); ?></option><?php endforeach; ?></select>
                            <select name=\"birthday_year\" aria-label=\"سال تولد\"><option value=\"\">سال</option><?php foreach ($years as $year) : ?><option value=\"<?php echo esc_attr($year); ?>\" <?php selected((int) $profile['birthday_year'], $year); ?>><?php echo esc_html($year); ?></option><?php endforeach; ?></select>
                        </div>
                        <small data-error-for=\"birthday_year\"></small>
                    </fieldset>"""

new_birthday = """                    <fieldset class=\"dede-field dede-field--wide dede-birthday\"
                              data-birthday-picker
                              data-min-year=\"1300\"
                              data-max-date=\"<?php echo esc_attr($max_birth_date); ?>\">
                        <legend>تاریخ تولد <em>اختیاری</em></legend>

                        <div class=\"dede-birthday__control\">
                            <button type=\"button\"
                                    class=\"dede-birthday__trigger\"
                                    data-birthday-toggle
                                    aria-haspopup=\"dialog\"
                                    aria-expanded=\"false\">
                                <span data-birthday-display><?php echo $profile['birthday'] ? esc_html($profile['birthday']) : 'انتخاب تاریخ تولد'; ?></span>
                                <svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" aria-hidden=\"true\" focusable=\"false\">
                                    <path d=\"M7 2v3M17 2v3M3.5 9h17M5.5 4h13a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2h-13a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z\"/>
                                </svg>
                            </button>

                            <div class=\"dede-birthday__popover\" data-birthday-popover role=\"dialog\" aria-label=\"انتخاب تاریخ تولد\" hidden>
                                <div class=\"dede-birthday__toolbar\">
                                    <select data-birthday-month aria-label=\"ماه تولد\"></select>
                                    <select data-birthday-year aria-label=\"سال تولد\"></select>
                                </div>
                                <div class=\"dede-birthday__weekdays\" aria-hidden=\"true\">
                                    <span>ش</span><span>ی</span><span>د</span><span>س</span><span>چ</span><span>پ</span><span>ج</span>
                                </div>
                                <div class=\"dede-birthday__days\" data-birthday-days role=\"grid\"></div>
                                <div class=\"dede-birthday__footer\">
                                    <button type=\"button\" data-birthday-clear>پاک کردن</button>
                                    <button type=\"button\" data-birthday-close>بستن</button>
                                </div>
                            </div>
                        </div>

                        <div class=\"dede-birthday__fallback\" data-birthday-fallback>
                            <select name=\"birthday_day\" aria-label=\"روز تولد\"><option value=\"\">روز</option><?php for ($day = 1; $day <= 31; $day++) : ?><option value=\"<?php echo esc_attr($day); ?>\" <?php selected((int) $profile['birthday_day'], $day); ?>><?php echo esc_html($day); ?></option><?php endfor; ?></select>
                            <select name=\"birthday_month\" aria-label=\"ماه تولد\"><option value=\"\">ماه</option><?php foreach ($months as $number => $month) : ?><option value=\"<?php echo esc_attr($number); ?>\" <?php selected((int) $profile['birthday_month'], $number); ?>><?php echo esc_html($month); ?></option><?php endforeach; ?></select>
                            <select name=\"birthday_year\" aria-label=\"سال تولد\"><option value=\"\">سال</option><?php foreach ($years as $year) : ?><option value=\"<?php echo esc_attr($year); ?>\" <?php selected((int) $profile['birthday_year'], $year); ?>><?php echo esc_html($year); ?></option><?php endforeach; ?></select>
                        </div>
                        <small data-error-for=\"birthday_year\"></small>
                    </fieldset>"""
replace('wp-content/plugins/dede-store-features/templates/customer-profile.php', old_birthday, new_birthday)

replace(
    'wp-content/plugins/dede-store-features/includes/class-dede-store-features.php',
    """        $css = DEDE_STORE_FEATURES_PATH . 'assets/css/customer-profile.css';
        $js = DEDE_STORE_FEATURES_PATH . 'assets/js/customer-profile.js';
        $guard = DEDE_STORE_FEATURES_PATH . 'assets/js/customer-profile-guard.js';
""",
    """        $css = DEDE_STORE_FEATURES_PATH . 'assets/css/customer-profile.css';
        $js = DEDE_STORE_FEATURES_PATH . 'assets/js/customer-profile.js';
        $guard = DEDE_STORE_FEATURES_PATH . 'assets/js/customer-profile-guard.js';
        $birthday_picker = DEDE_STORE_FEATURES_PATH . 'assets/js/birthday-picker.js';
""",
)

replace(
    'wp-content/plugins/dede-store-features/includes/class-dede-store-features.php',
    """        wp_enqueue_script(
            'dede-store-features-customer-profile-guard',
            DEDE_STORE_FEATURES_URL . 'assets/js/customer-profile-guard.js',
            array('dede-store-features-customer-profile'),
            file_exists($guard) ? (string) filemtime($guard) : DEDE_STORE_FEATURES_VERSION,
            true
        );
""",
    """        wp_enqueue_script(
            'dede-store-features-customer-profile-guard',
            DEDE_STORE_FEATURES_URL . 'assets/js/customer-profile-guard.js',
            array('dede-store-features-customer-profile'),
            file_exists($guard) ? (string) filemtime($guard) : DEDE_STORE_FEATURES_VERSION,
            true
        );
        wp_enqueue_script(
            'dede-store-features-birthday-picker',
            DEDE_STORE_FEATURES_URL . 'assets/js/birthday-picker.js',
            array('dede-store-features-customer-profile'),
            file_exists($birthday_picker) ? (string) filemtime($birthday_picker) : DEDE_STORE_FEATURES_VERSION,
            true
        );
""",
)

replace(
    'wp-content/plugins/dede-store-features/dede-store-features.php',
    ' * Version: 1.0.5',
    ' * Version: 1.0.6',
)
replace(
    'wp-content/plugins/dede-store-features/dede-store-features.php',
    "define('DEDE_STORE_FEATURES_VERSION', '1.0.5');",
    "define('DEDE_STORE_FEATURES_VERSION', '1.0.6');",
)

birthday_js = r"""(function () {
    'use strict';

    const monthNames = [
        'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
        'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'
    ];

    function pad(value) {
        return String(value).padStart(2, '0');
    }

    function parseDate(value) {
        const match = String(value || '').match(/^(\d{4})\/(\d{1,2})\/(\d{1,2})$/);
        return match ? {year: Number(match[1]), month: Number(match[2]), day: Number(match[3])} : null;
    }

    function compareDates(left, right) {
        if (left.year !== right.year) return left.year - right.year;
        if (left.month !== right.month) return left.month - right.month;
        return left.day - right.day;
    }

    function jalaliToGregorian(jy, jm, jd) {
        jy += 1595;
        let days = -355668 + (365 * jy) + (Math.floor(jy / 33) * 8) + Math.floor(((jy % 33) + 3) / 4) + jd;
        days += jm < 7 ? (jm - 1) * 31 : ((jm - 7) * 30) + 186;
        let gy = 400 * Math.floor(days / 146097);
        days %= 146097;
        if (days > 36524) {
            gy += 100 * Math.floor((--days) / 36524);
            days %= 36524;
            if (days >= 365) days += 1;
        }
        gy += 4 * Math.floor(days / 1461);
        days %= 1461;
        if (days > 365) {
            gy += Math.floor((days - 1) / 365);
            days = (days - 1) % 365;
        }
        let gd = days + 1;
        const leap = (gy % 4 === 0 && gy % 100 !== 0) || gy % 400 === 0;
        const months = [0, 31, leap ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        let gm = 1;
        while (gm <= 12 && gd > months[gm]) {
            gd -= months[gm];
            gm += 1;
        }
        return {year: gy, month: gm, day: gd};
    }

    function utcValue(date) {
        const gregorian = jalaliToGregorian(date.year, date.month, date.day);
        return Date.UTC(gregorian.year, gregorian.month - 1, gregorian.day);
    }

    function daysInMonth(year, month) {
        const next = month === 12
            ? {year: year + 1, month: 1, day: 1}
            : {year: year, month: month + 1, day: 1};
        return Math.round((utcValue(next) - utcValue({year: year, month: month, day: 1})) / 86400000);
    }

    function firstWeekday(year, month) {
        const gregorian = jalaliToGregorian(year, month, 1);
        return (new Date(Date.UTC(gregorian.year, gregorian.month - 1, gregorian.day)).getUTCDay() + 1) % 7;
    }

    function initPicker(root) {
        if (root.dataset.birthdayInitialized === '1') return;

        const dayField = root.querySelector('[name="birthday_day"]');
        const monthField = root.querySelector('[name="birthday_month"]');
        const yearField = root.querySelector('[name="birthday_year"]');
        const trigger = root.querySelector('[data-birthday-toggle]');
        const display = root.querySelector('[data-birthday-display]');
        const popover = root.querySelector('[data-birthday-popover]');
        const monthPicker = root.querySelector('[data-birthday-month]');
        const yearPicker = root.querySelector('[data-birthday-year]');
        const daysGrid = root.querySelector('[data-birthday-days]');
        const clearButton = root.querySelector('[data-birthday-clear]');
        const closeButton = root.querySelector('[data-birthday-close]');
        const maxDate = parseDate(root.dataset.maxDate);
        const minYear = Number(root.dataset.minYear || 1300);

        if (!dayField || !monthField || !yearField || !trigger || !popover || !maxDate) return;

        let selected = dayField.value && monthField.value && yearField.value
            ? {year: Number(yearField.value), month: Number(monthField.value), day: Number(dayField.value)}
            : null;
        let view = selected ? {...selected} : {...maxDate};

        monthNames.forEach(function (name, index) {
            const option = document.createElement('option');
            option.value = String(index + 1);
            option.textContent = name;
            monthPicker.appendChild(option);
        });

        for (let year = maxDate.year; year >= minYear; year -= 1) {
            const option = document.createElement('option');
            option.value = String(year);
            option.textContent = String(year);
            yearPicker.appendChild(option);
        }

        function selectedText() {
            return selected ? selected.year + '/' + pad(selected.month) + '/' + pad(selected.day) : 'انتخاب تاریخ تولد';
        }

        function syncFields() {
            dayField.value = selected ? String(selected.day) : '';
            monthField.value = selected ? String(selected.month) : '';
            yearField.value = selected ? String(selected.year) : '';
            display.textContent = selectedText();
            [dayField, monthField, yearField].forEach(function (field) {
                field.dispatchEvent(new Event('change', {bubbles: true}));
            });
        }

        function close() {
            popover.hidden = true;
            trigger.setAttribute('aria-expanded', 'false');
        }

        function render() {
            if (view.year > maxDate.year) view.year = maxDate.year;
            if (view.year === maxDate.year && view.month > maxDate.month) view.month = maxDate.month;

            yearPicker.value = String(view.year);
            monthPicker.value = String(view.month);
            daysGrid.innerHTML = '';

            const offset = firstWeekday(view.year, view.month);
            const count = daysInMonth(view.year, view.month);
            for (let index = 0; index < offset; index += 1) {
                const empty = document.createElement('span');
                empty.className = 'dede-birthday__empty';
                daysGrid.appendChild(empty);
            }

            for (let day = 1; day <= count; day += 1) {
                const candidate = {year: view.year, month: view.month, day: day};
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = String(day);
                button.setAttribute('role', 'gridcell');
                button.disabled = compareDates(candidate, maxDate) > 0;
                if (selected && compareDates(candidate, selected) === 0) {
                    button.classList.add('is-selected');
                    button.setAttribute('aria-selected', 'true');
                }
                button.addEventListener('click', function () {
                    selected = candidate;
                    view = {...candidate};
                    syncFields();
                    render();
                    close();
                    trigger.focus();
                });
                daysGrid.appendChild(button);
            }
        }

        trigger.addEventListener('click', function () {
            const willOpen = popover.hidden;
            popover.hidden = !willOpen;
            trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            if (willOpen) render();
        });

        monthPicker.addEventListener('change', function () {
            view.month = Number(monthPicker.value);
            render();
        });

        yearPicker.addEventListener('change', function () {
            view.year = Number(yearPicker.value);
            render();
        });

        clearButton.addEventListener('click', function () {
            selected = null;
            view = {...maxDate};
            syncFields();
            render();
            close();
            trigger.focus();
        });

        closeButton.addEventListener('click', function () {
            close();
            trigger.focus();
        });

        document.addEventListener('click', function (event) {
            if (!popover.hidden && !root.contains(event.target)) close();
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !popover.hidden) {
                close();
                trigger.focus();
            }
        });

        root.dataset.birthdayInitialized = '1';
        root.classList.add('is-enhanced');
        display.textContent = selectedText();
        render();
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-birthday-picker]').forEach(initPicker);
    });
}());
"""
(ROOT / 'wp-content/plugins/dede-store-features/assets/js/birthday-picker.js').write_text(
    birthday_js,
    encoding='utf-8',
    newline='\n',
)

birthday_css = r"""
/* Simple Jalali birthday picker. */
.dede-birthday__control {
    position: relative;
    width: 100%;
}

.dede-birthday__trigger {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 12px;
    width: 100% !important;
    min-height: 40px;
    padding: 8px 11px !important;
    border: 1px solid #cfd4dd !important;
    border-radius: 9px !important;
    background: #fff !important;
    color: var(--dede-ink) !important;
    font: inherit !important;
    text-align: right;
    cursor: pointer;
    box-shadow: none !important;
}

.dede-birthday__trigger:hover,
.dede-birthday__trigger:focus {
    background: #fff !important;
    color: var(--dede-ink) !important;
    border-color: var(--dede-primary) !important;
    box-shadow: 0 0 0 3px rgba(47, 36, 131, .09) !important;
}

.dede-birthday__trigger svg {
    flex: 0 0 22px;
    fill: none;
    stroke: var(--dede-primary);
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.dede-birthday__popover[hidden] {
    display: none !important;
}

.dede-birthday__popover {
    position: absolute;
    z-index: 90;
    top: calc(100% + 7px);
    right: 0;
    width: min(350px, calc(100vw - 32px));
    padding: 12px;
    border: 1px solid var(--dede-line);
    border-radius: 13px;
    background: #fff;
    box-shadow: 0 18px 45px rgba(15, 23, 42, .16);
}

.dede-birthday__toolbar {
    display: grid;
    grid-template-columns: 1.35fr 1fr;
    gap: 8px;
    margin-bottom: 10px;
}

.dede-birthday__toolbar select {
    min-height: 38px !important;
    padding: 6px 9px !important;
}

.dede-birthday__weekdays,
.dede-birthday__days {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 4px;
}

.dede-birthday__weekdays {
    margin-bottom: 5px;
    color: var(--dede-muted);
    font-size: 11px;
    text-align: center;
}

.dede-birthday__weekdays span {
    padding: 4px 0;
}

.dede-birthday__days button {
    display: grid !important;
    place-items: center;
    aspect-ratio: 1;
    min-width: 0;
    min-height: 34px;
    padding: 0 !important;
    border: 0 !important;
    border-radius: 9px !important;
    background: transparent !important;
    color: var(--dede-ink) !important;
    font: inherit !important;
    font-size: 12px !important;
    cursor: pointer;
    box-shadow: none !important;
}

.dede-birthday__days button:hover,
.dede-birthday__days button:focus {
    background: #eeecff !important;
    color: var(--dede-primary) !important;
}

.dede-birthday__days button.is-selected,
.dede-birthday__days button.is-selected:hover,
.dede-birthday__days button.is-selected:focus {
    background: var(--dede-primary) !important;
    color: #fff !important;
}

.dede-birthday__days button:disabled {
    background: transparent !important;
    color: #c5c9d1 !important;
    cursor: not-allowed;
    opacity: 1;
}

.dede-birthday__footer {
    display: flex;
    justify-content: flex-end;
    gap: 7px;
    margin-top: 10px;
    padding-top: 9px;
    border-top: 1px solid var(--dede-line);
}

.dede-birthday__footer button {
    min-height: 34px;
    padding: 6px 11px !important;
    border: 0 !important;
    border-radius: 8px !important;
    background: #eeecff !important;
    color: var(--dede-primary) !important;
    font: inherit !important;
    font-size: 11px !important;
    cursor: pointer;
    box-shadow: none !important;
}

.dede-birthday__footer button:hover,
.dede-birthday__footer button:focus {
    background: var(--dede-primary) !important;
    color: #fff !important;
}

.dede-birthday__fallback {
    display: grid;
    grid-template-columns: 1fr 1.35fr 1fr;
    gap: 7px;
}

.dede-birthday.is-enhanced .dede-birthday__fallback {
    display: none;
}

@media (max-width: 480px) {
    .dede-birthday__popover {
        position: fixed;
        top: 50%;
        right: 12px;
        left: 12px;
        width: auto;
        transform: translateY(-50%);
        max-height: calc(100dvh - 24px);
        overflow-y: auto;
    }
}
"""
append_once(
    'wp-content/plugins/dede-store-features/assets/css/customer-profile.css',
    '/* Simple Jalali birthday picker. */',
    birthday_css,
)

replace(
    '.github/workflows/quality-check.yml',
    '          node --check wp-content/plugins/dede-store-features/assets/js/customer-profile-guard.js\n',
    '          node --check wp-content/plugins/dede-store-features/assets/js/customer-profile-guard.js\n'
    '          node --check wp-content/plugins/dede-store-features/assets/js/birthday-picker.js\n',
)
replace(
    '.github/workflows/quality-check.yml',
    """      - name: Check JavaScript syntax
""",
    """      - name: Test birthday validation
        shell: bash
        run: |
          set -Eeuo pipefail
          php tools/tests/birthday-validation.php

      - name: Check JavaScript syntax
""",
)

birthday_test = r"""<?php

define('ABSPATH', __DIR__ . '/');

class WP_Error
{
    private $code;
    private $message;

    public function __construct($code, $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    public function get_error_code()
    {
        return $this->code;
    }

    public function get_error_message()
    {
        return $this->message;
    }
}

function is_wp_error($value)
{
    return $value instanceof WP_Error;
}

function absint($value)
{
    return abs((int) $value);
}

function wp_timezone()
{
    return new DateTimeZone('Asia/Tehran');
}

function current_datetime()
{
    return new DateTimeImmutable('2026-07-13 12:00:00', wp_timezone());
}

require dirname(__DIR__, 2) . '/wp-content/plugins/dede-store-features/includes/trait-dede-store-features-validation.php';

class Birthday_Validation_Test
{
    use DeDe_Store_Features_Validation;

    public function validate($year, $month, $day)
    {
        return $this->validate_birthday(array(
            'birthday_year' => $year,
            'birthday_month' => $month,
            'birthday_day' => $day,
        ));
    }
}

$test = new Birthday_Validation_Test();
$cutoff = $test->gregorian_to_jalali(2011, 7, 13);
$too_young = $test->gregorian_to_jalali(2011, 7, 14);
$adult = $test->gregorian_to_jalali(1990, 1, 1);

$result = $test->validate($cutoff[0], $cutoff[1], $cutoff[2]);
if (is_wp_error($result)) {
    fwrite(STDERR, "Exactly 15 years old must be accepted.\n");
    exit(1);
}

$result = $test->validate($too_young[0], $too_young[1], $too_young[2]);
if (!is_wp_error($result) || 'under_age' !== $result->get_error_code()) {
    fwrite(STDERR, "A user younger than 15 was accepted.\n");
    exit(1);
}

$result = $test->validate($adult[0], $adult[1], $adult[2]);
if (is_wp_error($result) || !preg_match('/^\d{4}\/\d{2}\/\d{2}$/', $result['jalali']) || !preg_match('/^\d{13}$/', $result['timestamp'])) {
    fwrite(STDERR, "Birthday storage contract changed.\n");
    exit(1);
}

$result = $test->validate(0, 0, 0);
if (is_wp_error($result) || '' !== $result['jalali'] || '' !== $result['timestamp']) {
    fwrite(STDERR, "Optional empty birthday must remain valid.\n");
    exit(1);
}

echo "Birthday validation tests passed.\n";
"""
(ROOT / 'tools/tests/birthday-validation.php').write_text(birthday_test, encoding='utf-8', newline='\n')

manifest = ROOT / 'tools/cpanel-initial-deploy-files.txt'
manifest_text = manifest.read_text(encoding='utf-8')
new_asset = 'wp-content/plugins/dede-store-features/assets/js/birthday-picker.js'
if new_asset not in manifest_text:
    manifest.write_text(manifest_text.rstrip() + '\n' + new_asset + '\n', encoding='utf-8', newline='\n')

for doc_path in ('docs/CUSTOMER-PROFILE-MIGRATION.md', 'docs/CUSTOMER-PROFILE-TEST-RESULTS.md'):
    file_path = ROOT / doc_path
    text = file_path.read_text(encoding='utf-8')
    text = text.replace('سن بیشتر از ۱۰ سال', 'سن حداقل ۱۵ سال')
    text = text.replace('سن کمتر از ۱۰ سال', 'سن کمتر از ۱۵ سال')
    file_path.write_text(text, encoding='utf-8', newline='\n')

print('Birthday picker changes applied.')
