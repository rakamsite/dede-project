<?php

namespace dede_dev_admin_menu;

use WP_Query;

class dede_dev_admin_menu
{
    public string $Exel;
    public string $PDF;
    public string $email;
    public string $default_img;

    function __construct()
    {
        $this->Exel = 'dede_dev_exel_menu';
        $this->PDF = 'dede_dev_pdf_menu';
        $this->email = 'dede_dev_email_menu';
        $this->default_img = plugin_url . '/assets/img/default_img.png';
    }

    public function run()
    {
        add_action('admin_menu', function () {
            add_menu_page(
                'بارگذاری اکسل',
                'بارگذاری اکسل',
                'administrator',
                $this->Exel,
                [$this, "dede_dev_exel_menu_callback"],
                'dashicons-media-spreadsheet'
            );
            add_submenu_page($this->Exel, 'رفع خطا', 'رفع خطا', 'administrator', 'fix_problem', [$this, 'dede_dev_fix_error']);

        });
        add_action('admin_menu', function () {
            add_menu_page(
                'دریافت PDF',
                'دریافت PDF',
                'administrator',
                $this->PDF,
                [$this, "dede_dev_pdf_menu_callback"],
                'dashicons-pdf'
            );
        });
        add_action('admin_menu', function () {
            add_menu_page(
                'ایمیل ووکامرس',
                'ایمیل ووکامرس',
                'administrator',
                $this->email,
                [$this, "dede_dev_email_menu_callback"],
                'dashicons-email-alt'
            );
        });
    }

    public function dede_dev_exel_menu_callback(): void
    {
        $this->enqueue_styles_scripts();
        echo <<<HTML
            <h1>بارگذاری اکسل</h1>
            <form id="exel_importer" class="dev-container dev-mx-auto dev-mt-5 dev-border-solid dev-border-[1px] dev-rounded-lg dev-py-10 dev-font-bold dev-flex dev-flex-row md:flex-col dev-justify-evenly ">
                <input type="hidden" name="action" value="dede_dev_exel_extractor">
                <input type="hidden" name="dede_dev_request_type" value="upload">
                <label for="dede_exel_file_for_upload" class="dev-border-solid dev-border-[1px] dev-py-2 dev-px-10 dev-rounded-lg dev-bg-white dev-flex dev-items-center">
                    <input class="dev-hidden" name="dede_exel_file_for_upload" id="dede_exel_file_for_upload" type="text">
                    آپلود/انتخاب فایل اکسل
                </label>
                <select name="type_of_exel" id="type_of_exel" class="dev-bg-stone-100 dev-grow ">
                    <option value="false" selected>یک مورد را انتخاب کنید</option>
                    <option value="variable">محصولات مادر</option>
                    <option value="variation">محصولات فرزند</option>
                </select>
                <button type="submit" class="dev-bg-[#0058BF] dev-p-4 dev-text-white dev-ring-none dev-border-none">بارگذاری</button>
            </form>
            <form id="select_meta_data" class="dev-container dev-mx-auto dev-mt-5 dev-pb-10 dev-border-solid dev-border-[1px] dev-rounded-lg dev-font-bold dev-grid dev-grid-cols-1 lg:dev-grid-cols-2 dev-gap-10 dev-hidden">
            </form>
            <div id="importer_log_div" class="dev-container dev-mx-auto dev-py-10 dev-font-bold dev-text-center dev-hidden">
                <div id="importer_log" class="dev-h-80 dev-overflow-y-auto dev-rounded-lg dev-bg-white dev-px-10"></div>
            </div>
        HTML;
    }

    public function dede_dev_pdf_menu_callback(): void
    {
        $this->enqueue_styles_scripts();
        $pdfFilePath = plugin_url . '/assets/PdfFile.pdf';
        $categories = wp_dropdown_categories([
            'show_option_all' => '',
            'show_option_none' => 'همه دسته ها',
            'orderby' => 'id',
            'order' => 'ASC',
            'hide_empty' => 1,
            'echo' => 0,
            'name' => 'cat',
            'class' => 'postform',
            'taxonomy' => 'product_cat',
            'hide_if_empty' => false,
            'option_none_value' => 0,
            'value_field' => 'term_id',
            'required' => false,
        ]);
        echo <<<HTML
            <h1>دریافت PDF محصولات</h1>
            <form id="PDF_exporter" class="dev-container dev-mx-auto dev-mt-5 dev-border-solid dev-border-[1px] dev-rounded-lg dev-py-10 dev-flex dev-flex-row md:flex-col dev-justify-center dev-gap-10 dev-font-bold">
                <input type="hidden" name="action" value="dede_dev_pdf_maker">
                <input placeholder="سر تیتر صفحات" type="text" name="dede_dev_pdf_title" class="dev-bg-stone-100 dev-w-full md:dev-w-2/5  ">
                <input name="PDF_link_url" id="PDF_link_url" value="$pdfFilePath" type="hidden">
                $categories
                <button class="dev-bg-[#0058BF] dev-p-4 dev-text-white dev-ring-none dev-border-none">دریافت PDF</button>
                <a class="dev-bg-[#0058BF] dev-p-4 dev-text-white dev-ring-none dev-border-none" href="$pdfFilePath" target="_blank">دانلود pdf قدیمی</a>
            </form>
        HTML;
    }

    public function dede_dev_email_menu_callback(): void
    {
        $prefix = (new dede_dev_woocommerce_order_email())->prefix;
        $all_options = ["image_id", "description", "copyright", "instagram", "telegram", "facebook", "x", "aparat", "youtube", "linkedin", "product_1", "product_2", "product_3"];
        $options = get_options([
            "{$prefix}_image_id",
            "{$prefix}_description",
            "{$prefix}_copyright",
            "{$prefix}_instagram",
            "{$prefix}_telegram",
            "{$prefix}_facebook",
            "{$prefix}_x",
            "{$prefix}_aparat",
            "{$prefix}_youtube",
            "{$prefix}_linkedin",
            "{$prefix}_product_1",
            "{$prefix}_product_2",
            "{$prefix}_product_3",
        ]);
        $options_combined = array_combine($all_options, array_values($options));
        if (!empty($options_combined["image_id"])) {
            $this->default_img = wp_get_attachment_url($options_combined["image_id"]);
        }
        $this->enqueue_styles_scripts();
        echo <<<HTML
            <h1>تنظیمات ایمیل ووکامرس</h1>
            <form id="woocommerce_Email" class="dev-container dev-mx-auto dev-mt-5 dev-rounded-lg dev-py-10 dev-flex dev-flex-row md:dev-flex-col dev-justify-center dev-gap-10 dev-font-bold">
                <input type="hidden" name="action" value="dede_dev_woocommerce_email">
                <h1>هدر ایمیل</p>
                <div class="dev-grid dev-grid-cols-10 dev-w-full dev-border-solid dev-border-[1px] dev-rounded dev-py-10">
                    <div class="dev-col-span-3">
                        <img id="prev_header_image" src="$this->default_img" height="180" alt="">
                    </div>
                    <div class="dev-col-span-7 dev-flex dev-items-center dev-justify-center">
                    <input type="hidden" name="woocommerce_email_header_image_id" id="image_id" value='$options_combined[image_id]'>
                    <label for="image_id">
                    </label>
                    <button type="button" id="selet_header_email_img" class="dev-h-fit dev-p-4 dev-text-white dev-rounded dev-text-base dev-bg-[#0058BF]">انتخاب تصویر هدر ایمیل</button>
                    </div>
                </div>
                <h1>توضیحات اضافه</h1>
                <div class="dev-flex dev-items-center dev-justify-center dev-w-full dev-border-[1px] dev-border-solid dev-rounded *:dev-w-11/12 dev-py-5">
        HTML;
        wp_editor($options_combined['description'], 'woocommerce_email_description', [
            'media_buttons' => true,
            'drag_drop_upload' => false,
            'textarea_rows' => 20,
            'editor_class' => 'dev-w-full',
            'tinymce' => true,
            'quicktags' => true,
        ]);
        echo <<<HTML
                    </div>
                    <h1>فوتر ایمیل</h1>
                    <div class="dev-grid dev-grid-cols-4 dev-gap-3 dev-items-center dev-justify-around dev-border-[1px] dev-border-solid dev-rounded dev-p-5">
                        <input class="dev-p-3 deb-bg-white" value="$options_combined[instagram]" name="woocommerce_email_footer_instagram" placeholder="اینستاگرام"/>
                        <input class="dev-p-3 deb-bg-white" value="$options_combined[telegram]" name="woocommerce_email_footer_telegram" placeholder="تلگرام"/>
                        <input class="dev-p-3 deb-bg-white" value="$options_combined[facebook]" name="woocommerce_email_footer_facebook" placeholder="فیسبوک"/>
                        <input class="dev-p-3 deb-bg-white" value="$options_combined[x]" name="woocommerce_email_footer_x" placeholder="ایکس"/>
                        <input class="dev-p-3 deb-bg-white" value="$options_combined[aparat]" name="woocommerce_email_footer_aparat" placeholder="آپارات"/>
                        <input class="dev-p-3 deb-bg-white" value="$options_combined[youtube]" name="woocommerce_email_footer_youtube" placeholder="یوتوب"/>
                        <input class="dev-p-3 deb-bg-white" value="$options_combined[linkedin]" name="woocommerce_email_footer_linkedin" placeholder="لینکدین"/>
                        <input class="dev-p-3 deb-bg-white dev-col-span-2" value="$options_combined[copyright]" name="woocommerce_email_footer_copyright" placeholder="متن کپی رایت"/>
                    </div>
HTML;
        echo <<<HTML
                    <h1>انتخاب محصولات</h1>
                    <div class="dev-grid dev-grid-cols-3 dev-gap-3 dev-items-center dev-justify-around dev-border-[1px] dev-border-solid dev-rounded dev-p-5"> 
HTML;
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
        );
        $posts = get_posts($args);

        for ($i = 1; $i <= 3; $i++) {
            if ($posts) : ?>
                <select class="!dev-w-full !dev-p-3 !dev-bg-white" name="woocommerce_email_product_<?php echo $i ?>">
                    <option value="">انتخاب محصول</option>
                    <?php foreach ($posts as $post) : ?>
                        <?php if ($options_combined["product_$i"] == $post->ID): ?>
                            <option value="<?php echo $post->ID; ?>" selected><?php echo $post->post_title; ?></option>
                        <?php else: ?>
                            <option value="<?php echo $post->ID; ?>"><?php echo $post->post_title; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            <?php endif;
        }

        echo <<<HTML
            </div>
                    <button class="dev-w-full dev-bg-[#0058BF] dev-p-3 dev-text-white dev-border-none focus:dev-border ">ذخیره تنظیمات</button>
         </form>
HTML;
    }

    public function dede_dev_fix_error(): void
    {
        echo <<<HTML
<div>
    <form action="" method="POST">
    <input name="fix_problem" type="hidden" value="fixit">
    <button type="submit">حل مشکل</button>
    </form>
</div>
HTML;
        if (isset($_POST['fix_problem']) && $_POST['fix_problem'] === 'fixit') {
            $log_path = plugin_path . '/assets/js/import-log.txt';

            // بررسی وجود فایل قبل از حذف
            if (file_exists($log_path)) {
                unlink($log_path);
                echo "فایل لاگ پاک شد";
            } else {
                echo "فایل لاگ یافت نشد";
            }
        }
    }

    function enqueue_styles_scripts(): void
    {
        wp_enqueue_style('dede-dev-v1-style-admin-panel', plugin_url . '/assets/css/style.css');
        wp_enqueue_script('dede-script-handler', plugin_url . '/assets/js/main.js');
        wp_localize_script('dede-script-handler', 'ajax_admin', array('ajax_url' => admin_url('admin-ajax.php'), 'plugin_path' => plugin_url));
        echo <<<HTML
            <div id="Alert_confirm" class="dev-fixed dev-w-full md:dev-w-1/4 dev-h-full md:dev-h-1/4 dev-flex dev-flex-col dev-gap-3 dev-items-center dev-justify-center dev-rounded-lg dev-bg-white dev-shadow-lg dev-top-1/2 -dev-translate-y-1/2 dev-left-1/2 -dev-translate-x-1/2 dev-z-30 dev-px-5 dev-hidden">
                <p id="prompt_text" class="dev-text-gray-900 dev-font-bold"></p>
                <div class="dev-flex dev-gap-2 dev-justify-evenly">
                    <button id="prompt_button_accept" class="dev-bg-green-500 dev-text-white dev-font-bold dev-rounded dev-border-none dev-p-2" >ACCEPT</button>
                    <button id="prompt_button_denide" class="dev-bg-rose-500 dev-text-white dev-font-bold dev-rounded dev-border-none dev-p-2" >DENIDE</button>
                </div>
            </div>
            <div id="waiting_for_response" class="dev-fixed dev-top-1/2 -dev-translate-y-1/2 dev-left-1/2 -dev-translate-x-1/2 z-50 dev-hidden">
                <div role="status">
                    <svg aria-hidden="true" class="dev-inline dev-w-24 dev-h-24 dev-text-gray-200 dev-animate-spin dev-fill-gray-600 " viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                    </svg>
                </div>
            </div>
        HTML;

    }
}