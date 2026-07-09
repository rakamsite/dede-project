<?php

namespace dede_dev_admin_menu;

use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use WP_Query;

class dede_dev_ajax_pdf_maker
{
    public string $title;

    /**
     * @throws \Mpdf\MpdfException
     */
    public function PDF_maker(): void
    {
        $this->title = $_POST['dede_dev_pdf_title'];
        if (empty($this->title)) {
            wp_send_json_success(["message" => "لطفا سر تیتر را وارد کنید .", "options" => ["accept" => "باشه"]]);
        }
        $cat_id = isset($_POST['cat']) ? intval($_POST['cat']) : 0;
        $query = [
            'post_type' => 'product_variation',
            'posts_per_page' => -1,
            'meta_key' => 'AJAX-Code',
            'orderby' => 'meta_value',
            'order' => 'ASC',
        ];

        if ($cat_id >= 0) {
            $parent_query = [
                'post_status' => 'publish',
                'post_type' => 'product',
                'posts_per_page' => -1,
                'tax_query' => [
                    [
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => [$cat_id]
                    ]
                ]
            ];
            $parent_products = new WP_Query($parent_query);
            if ($parent_products->have_posts()) {
                $product_ids = [];
                foreach ($parent_products->posts as $product) {
                    $product_ids[] = $product->ID;
                }
                $query['post_parent__in'] = $product_ids;
            }
        }
        $product_variation = new WP_Query($query);
//        wp_send_json_success(["message" => count($product_variation->posts), "options" => ["accept" => "باشه"]]);
        $post_per_page = array_chunk($product_variation->posts, 7);
        $posts_found = count($post_per_page);
        $page_index = 1;
        $pdfFilePath = plugin_path . '/assets/PdfFile';
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $post_counter = 1;
        $body = '';
        $mpdf = new Mpdf([
            'format' => 'A4-L',
            'mode' => 'utf-8',
            'fontDir' => array_merge($fontDirs, [
                __DIR__ . '/custom-fonts',
            ]),
            'fontdata' => $fontData + [
                    'vazir' => [
                        'R' => 'Vazirmatn[wght].ttf',
                        'useOTL' => 0xFF,
                        'useKashida' => 150,
                    ],
                ],
            'default_font' => 'vazir'
        ]);
        $mpdf->default_lang = 'fa';
        $mpdf->SetMargins(0, 0, 10);
        $mpdf->SetDirectionality('rtl');
        $html = <<<HTML
            <!DOCTYPE html>
            <html dir="rtl" lang="fa">
                <head>
                <meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
                      <title>{$this->title}</title>
                     <style>
                     body{
                         font-feature-settings: "ss01", "tnum";
                         direction: rtl;
                         display: flex;
                         justify-items: center;
                         justify-content: center;
                         width:100%
                     }
                     .header{
                        background-color:#D9D9D9 ;
                        padding: 8px;
                        border-radius: 10px;
                     }
                     .content-header th{
                         border-left: 1px solid #2F2483;
                     }
                     .content-header{
                         border: 1px solid #2F2483;
                     }
                     .content-header tr:nth-child(even) {
                         background-color: #D9D9D9;
                     }
                    </style>
                </head>
                <body>
                
                </body>
            </html>
        HTML;
        $mpdf->WriteHTML($html);

        foreach ($post_per_page as $posts) {
            $body = <<<HTML
            <div style="height: 100%;">
                <div class="header">
                <table style="width: 100%;">
                    <tr>
                        <td><h3>{$this->title}</h3></td>
                        <td style="text-align: left"><h5>صفحه $page_index از $posts_found</h5></td>
                    </tr>
                </table>
                </div>
                <div style="border-top-right-radius: 10px; border-top-left-radius: 10px; padding:5px; background-color: #2F2483; margin-top: 20px">                
                    <table style="width: 100%; color:#FFF; ">
                    <thead>
                        <tr>
                            <th  style="width: 5%;">ردیف</th>
                            <th  style="width: 10%">تصویر</th>
                            <th style="width: 35%;">نام و متغیر های کالا</th>
                            <th style="width: 10% ; text-align: left">کد کالا</th>
                            <th style=" width: 15%; text-align: left">فی (ریال)&emsp;</th>
                            <th style="width:23.5%;" >واحد ها</th>
                            <th style="width: 6.5%;">لینک</th>
                        </tr>
                    </thead>
                    </table>
                </div>
                <div>                
                    <table class="content-header" style="font-size: 10px; width: 100%; border-collapse: collapse; ">
                    {$this->create_post($posts, $post_counter)}
                    </table>
                </div>
            </div>

HTML;
            $page_index++;
            $post_counter += 7;
            $mpdf->WriteHTML($body);
        }
        $mpdf->OutputFile($pdfFilePath . '.pdf');

        wp_send_json_success(["message" => "ساخته شده .", "options" => ["accept" => "باشه"]]);
    }

    public function create_post($posts, $post_counter): string
    {

        $posts_html = "";
        foreach ($posts as $post) {
            $product = wc_get_product($post->ID);
            $image = get_attached_file($product->get_image_id());
            $title = $product->get_name();
            $attributes = $product->get_attributes();
            $attr = '';
            $url = $product->get_permalink();
            $regular_price = $product->get_regular_price();
            $offPrice = $product->get_sale_price();
            $main_unit = $product->get_meta('_dede_main_unit', true);
            $sub_unit_name_1 = $product->get_meta("_dede_sub_unit_name_1", true);
            $sub_unit_value_1 = $product->get_meta("_dede_sub_unit_value_1", true);
            $sub_unit_name_2 = $product->get_meta("_dede_sub_unit_name_2", true);
            $sub_unit_value_2 = $product->get_meta("_dede_sub_unit_value_2", true);

            $price = "";
            if (!empty($offPrice)) {
                $price = wc_price($offPrice);
            } else {
                $price = wc_price($regular_price);
            }
            foreach ($attributes as $key => $value) {
                $key = urldecode($key);
                $key = str_replace('-', ' ', $key);
                $value = urldecode($value);
                $attr .= "<p>$key : $value</p>";
            }
            $sku = $product->get_sku();
            $posts_html .= <<<HTML
                        <tr style="text-align: center;">
                            <th style="width: 5%;">$post_counter</th>
                            <th style=" width: 10%; padding-top:5px; padding-bottom: 5px;"><img src="$image" style="border-radius: 5px" align="center" height="73" width="73"></th>
                            <th style="text-align: right; width: 35%; padding-right:5px; line-height: 1.5 ">
                                    <p>$title</p>
                                    $attr
                            </th>
                            <th >$sku</th>
                            <th >$price</th>
                            <th style="text-align: right; padding-right:5px; line-height: 1.5 ">
                                <p>واحد اصلی: $main_unit</p>
                                <p>واحد فرعی 1: $sub_unit_name_1 $sub_unit_value_1 $main_unit</p>
                                <p>واحد فرعی 2: $sub_unit_name_2 $sub_unit_value_2 $main_unit</p>                
                            </th>
                            <th style="border-left: none">
                                <a href="$url" target="_blank">
                                    <svg width="15" height="10" viewBox="0 0 15 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7.33333 3C6.8029 3 6.29419 3.21071 5.91912 3.58579C5.54405 3.96086 5.33333 4.46957 5.33333 5C5.33333 5.53043 5.54405 6.03914 5.91912 6.41421C6.29419 6.78929 6.8029 7 7.33333 7C7.86377 7 8.37247 6.78929 8.74755 6.41421C9.12262 6.03914 9.33333 5.53043 9.33333 5C9.33333 4.46957 9.12262 3.96086 8.74755 3.58579C8.37247 3.21071 7.86377 3 7.33333 3ZM7.33333 8.33333C6.44928 8.33333 5.60143 7.98214 4.97631 7.35702C4.35119 6.7319 4 5.88406 4 5C4 4.11595 4.35119 3.2681 4.97631 2.64298C5.60143 2.01786 6.44928 1.66667 7.33333 1.66667C8.21739 1.66667 9.06524 2.01786 9.69036 2.64298C10.3155 3.2681 10.6667 4.11595 10.6667 5C10.6667 5.88406 10.3155 6.7319 9.69036 7.35702C9.06524 7.98214 8.21739 8.33333 7.33333 8.33333ZM7.33333 0C4 0 1.15333 2.07333 0 5C1.15333 7.92667 4 10 7.33333 10C10.6667 10 13.5133 7.92667 14.6667 5C13.5133 2.07333 10.6667 0 7.33333 0Z" fill="#525252"/>
                                    </svg>
                                </a>
                            </th>
                        </tr>

HTML;
            $post_counter++;
        }
        return $posts_html;
    }
}

