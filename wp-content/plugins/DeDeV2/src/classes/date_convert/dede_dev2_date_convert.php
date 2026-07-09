<?php

namespace classes\date_convert;

use Morilog\Jalali\CalendarUtils;
use Morilog\Jalali\Jalalian;

class dede_dev2_date_convert
{
    function run(): void
    {
        add_action('dede_v2_jalali_date', [$this, 'date_now']);
        add_filter('dede_v2_convert_to_jalali', [$this, 'convert_to_jalali']);
    }

    function date_now(): Jalalian
    {
        return Jalalian::now();
    }

    function convert_to_jalali(string $date): string
    {
        $months = [
            "Jan" => 1, "Feb" => 2, "Mar" => 3, "Apr" => 4,
            "May" => 5, "Jun" => 6, "Jul" => 7, "Aug" => 8,
            "Sep" => 9, "Oct" => 10, "Nov" => 11, "Dec" => 12
        ];

        [$y, $m, $d] = explode('/', $date);

        if (!isset($months[$m])) {
            return "خطا: فرمت تاریخ نامعتبر است!";
        }
        $m = $months[$m];

        [$jy, $jm, $jd] = CalendarUtils::toJalali((int)$y, (int)$m, (int)$d);

        $jalali_months = [
            1 => "فروردین", 2 => "اردیبهشت", 3 => "خرداد",
            4 => "تیر", 5 => "مرداد", 6 => "شهریور",
            7 => "مهر", 8 => "آبان", 9 => "آذر",
            10 => "دی", 11 => "بهمن", 12 => "اسفند"
        ];

        return sprintf('%04d/%s/%02d', $jy, $jalali_months[$jm], $jd);
    }
}

?>
