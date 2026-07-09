<?php

namespace dede_dev_admin_menu;

use DOMDocument;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpWord\Writer\HTML;
use React\EventLoop\Factory;
use React\Promise\Deferred;
use React\Promise\Promise;

class dede_dev_ajax_exel_importer_handler
{
    public string $log_path;
    public \React\EventLoop\ExtUvLoop|\React\EventLoop\ExtLibevLoop|\React\EventLoop\ExtEvLoop|\React\EventLoop\LoopInterface|\React\EventLoop\ExtEventLoop|\React\EventLoop\ExtLibeventLoop|\React\EventLoop\StreamSelectLoop $loop;
    public string $post_type;

    public function __construct()
    {
        $this->log_path = plugin_path . '/assets/js/import-log.txt';
        $this->loop = Factory::create();
    }


    public function exel_extractor(): void
    {
        $file = $_POST['dede_exel_file_for_upload'];
        $file_type = $_POST['type_of_exel'];
        $this->post_type = $file_type;
        $request_type = $_POST['dede_dev_request_type'];
        $exel_title = [];
        $exel_array = [];

        if (empty($file)) {
            wp_send_json_error(["message" => "فایل اکسل را انتخاب نمایید"]);
        } elseif ($file_type == false) {
            wp_send_json_error(["message" => "نوع فایل را انتخاب کنید"]);
        } else {
            $exel_path = get_attached_file($file);
            $exel_render = IOFactory::load($exel_path);
            $exel_array = $exel_render->getActiveSheet()->toArray();
        }
        if ($request_type === "upload") {
            if (file_exists($this->log_path)) {
                wp_send_json_success(["message" => "درحال حاظر یک فایل درحال وارد شدن میباشد. میتوانید در ادامه لاگ رو برسی نمایید.", "data" => "exists", "options" => ["accept" => "باشه"]]);
            }
            foreach ($exel_array as $exel) {
                foreach ($exel as $title) {
                    if (empty($title)) {
                        continue;
                    }
                    $exel_title[$title] = [];
                }
                wp_send_json_success(['message' => 'اگر تغییراتی لازم است انجام دهید.', 'data' => $exel_title, "options" => ["accept" => "باشه"]]);
            }
        } else if ($request_type === "import") {
            $log_file = fopen($this->log_path, 'c');
            $this->product_importer($exel_array, $log_file);
        }
    }

    public function word_extractor($word_file_path, $post_title): bool|string
    {
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($word_file_path);
        $htmlWriter = new HTML($phpWord);
        $html = $htmlWriter->getContent();
        $contentType = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        $dom = new DOMDocument();
        $dom->loadHTML($contentType . $html);
        $dom->encoding = 'UTF-8';
        $images = $dom->getElementsByTagName('img');
        if ($images->length > 0) {
            $i = 0;
            foreach ($images as $image) {
                $src = $image->getAttribute('src');
                $attachment_id = $this->upload_docx_images($src, $post_title . $i);
                $newSrc = wp_get_attachment_url($attachment_id);
                $image->setAttribute('src', $newSrc);
                $i++;
            }
        }
        return $dom->saveHTML();
    }

    private function multi_file_uploader($file): \WP_Error|bool|int
    {
        $upload_dir = wp_upload_dir();
        $target_file = $upload_dir['path'] . '/' . basename($file["name"]);

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $attachment = array(
                'guid' => $upload_dir['url'] . '/' . basename($file["name"]),
                'post_mime_type' => $_FILES["fileToUpload"]["type"],
                'post_title' => preg_replace('/\.[^.]+$/', '', basename($file["name"])),
            );

            $attach_id = wp_insert_attachment($attachment, $target_file);

            if (!is_wp_error($attach_id)) {
                return $attach_id;
            } else {
                wp_send_json_error(['message' => "مشکلی در آپلود فایل"]);
            }
        } else {
            wp_send_json_error(['message' => "مشکلی در آپلود فایل"]);
        }
        return false;
    }

    private function create_attribute($attr_name, $attr_values, $i): \WC_Product_Attribute|bool
    {
        if (isset($attr_name)) {
            $attribute = new \WC_Product_Attribute();
            $attribute->set_name($attr_name);
            $attribute->set_options(explode("|", $attr_values));
            $attribute->is_taxonomy();
            $attribute->set_variation(true);
            $attribute->set_visible(true);
            $attribute->set_position($i);
            return $attribute;
        } else {
            return false;
        }
    }

    private function upload_docx_images($base64_image, $post_title): \WP_Error|int
    {
        $data = explode(',', $base64_image);
        $decoded_data = base64_decode($data[1]);
        $f = finfo_open();
        $mime_type = finfo_buffer($f, $decoded_data, FILEINFO_MIME_TYPE);
        finfo_close($f);
        $extension = explode('/', $mime_type)[1];
        $file_name = $post_title . '.' . $extension;

        $wp_upload_dir = wp_upload_dir();
        $existing_file_path = $wp_upload_dir['path'] . '/' . $file_name;
        if (file_exists($existing_file_path)) {
            $attachment_id = attachment_url_to_postid($wp_upload_dir['url'] . '/' . $file_name);
            return $attachment_id;
        }

        // If file doesn't exist, upload the new file
        $file_path = $wp_upload_dir['path'] . '/' . $file_name;
        file_put_contents($file_path, $decoded_data);

        // Creating attachment data
        $attachment = array(
            'guid' => $wp_upload_dir['url'] . '/' . basename($file_path),
            'post_mime_type' => $mime_type,
            'post_title' => preg_replace('/\.[^.]+$/', '', basename($file_path)),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        // Inserting attachment into WordPress media library
        $attach_id = wp_insert_attachment($attachment, $file_path);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
        wp_update_attachment_metadata($attach_id, $attach_data);

        return $attach_id;
    }

    private function images_attachment_id_extractor($text_file_id): array
    {
        $file_path = get_attached_file($text_file_id);
        $file_lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $images_attachment_ids = [];
        foreach ($file_lines as $line_content) {
            $attachment_id = attachment_url_to_postid(trim($line_content));
            if ($attachment_id !== 0) {
                $images_attachment_ids[] = $attachment_id;
            }
        }
        return $images_attachment_ids;
    }

    private function product_importer($exel_array, $log_file): void
    {
        $counter = 0;
        $import_meta_data = $_POST['type_of_row_metadata'];
        // this line for testing
        // foreach ($exel_array as $exel_item) {
        // var_dump(array_filter($exel_item));
        // var_dump($import_meta_data);
        // }

        $import_ready = array_filter(
            array_map(
                function ($exel) use ($import_meta_data) {
                    if ($exel[0] === null) {
                        return null;
                    }
                    return array_combine($import_meta_data, $exel);
                },
                array_slice($exel_array, 1)
            )
        );

        $posts_found = count($import_ready);
        fwrite($log_file, "<p class='dev-text-green-500 dev-font-bold'>مجموع تعداد پست ها برای وارد کردن :$posts_found</p>");
        $exel_point = 0;
        if ($this->post_type === "variable") {
            foreach ($import_ready as $post) {
                set_time_limit(0);
                $deferred = new Deferred();
                $exel_point++;
                $promise = $this->process_import_variable($post, $log_file, $counter, $posts_found, $exel_point);
                $promise->then(function ($result) use ($deferred) {
                    $deferred->resolve($result);
                });
            }
        } else if ($this->post_type === "variation") {
            foreach ($import_ready as $post) {
                set_time_limit(0);
                $deferred = new Deferred();
                $exel_point++;
                $promise = $this->process_import_variation($post, $log_file, $counter, $posts_found, $exel_point);
                $promise->then(function ($result) use ($deferred) {
                    $deferred->resolve($result);
                });
            }
        }

        try {
            $this->loop->run();
        } catch (Exception $e) {
            $this->upload_file_to_media_library(time());
            unlink($this->log_path);
            wp_send_json_error(['message' => $e->getMessage(), "options" => ["accept" => "باشه"]]);
        }
        $this->upload_file_to_media_library(time());
        unlink($this->log_path);
        wp_send_json_success(['message' => "تمام پست ها وارد شدند", "options" => ["accept" => "باشه"]]);
    }

    private function Create_variable_product($check_exist, $post_title, $post_content, $post_excerpt, $sku, $term_id, $menu_order, $attributes, $attribute_default, $thumbnail_id, $gallery, $meta_data): bool
    {
        if (is_int($check_exist)) {
            $product = new \WC_Product_Variable($check_exist);
        } else {
            $product = new \WC_Product_Variable();
        }
        $product->set_name($post_title);
        if (!empty($post_content)) {
            $product->set_description($post_content);
        }
        if (!empty($post_excerpt)) {
            $product->set_short_description($post_excerpt);
        }
        $product->set_sku($sku);
        $product->set_default_attributes($attribute_default);
        $product->set_attributes($attributes);
        $product->set_menu_order($menu_order);
        $product->set_category_ids($term_id);
        if (!empty($thumbnail_id)) {
            $product->set_image_id($thumbnail_id);
        }
        if (!empty($gallery)) {
            $product->set_gallery_image_ids($gallery);
        }
        foreach ($meta_data as $meta_key => $meta_value) {
            $product->update_meta_data($meta_key, $meta_value);
        }
        $product_id = $product->save();
        if ($product_id > 0) {
            return true;
        } else {
            return false;
        }
    }

    private function Create_variation_product($check_exist, $sku, $mother_post_id, $html_description, $attributes, $image_id, $backorder, $meta_data): bool
    {
        if (is_int($check_exist)) {
            $product = new \WC_Product_Variation($check_exist);
        } else {
            $product = new \WC_Product_Variation();
        }
        $product->set_sku($sku);
        $product->set_parent_id($mother_post_id);
        if (!empty($html_description)) {
            $product->set_description($html_description);
        }
        foreach ($meta_data as $meta_key => $meta_value) {
            $product->update_meta_data($meta_key, $meta_value);
        }
        $product->set_attributes($attributes);
        if (!empty($image_id)) {
            $product->set_image_id($image_id);
        }
        $product->set_manage_stock(true);
        $product->set_backorders($backorder);
        $product_id = $product->save();
        if ($product_id > 0) {
            return true;
        } else {
            return false;
        }
    }

    private function process_import_variable($post, $log_file, $counter, $posts_found, $exel_point): Promise
    {
        return new Promise(function ($resolve, $reject) use ($post, $log_file, $counter, $posts_found, $exel_point) {

            fwrite($log_file, "<p><===============================================></p>");
            fwrite($log_file, "<p class='dev-text-green-500'>درحال وارد کردن پست شماره : $exel_point</p>");
            $importable = true;
            $taxonomy = 'product_cat';
            $term_id = [];
            $attribute_count = 2;
            $attributes = [];
            $sku = $post['sku'];
            $post_title = $post['post_title'];
            $exel_post_content = $post['post_content'];
            $exel_post_excerpt = $post['post_excerpt'];
            $exel_post_images = $post['images'];
            $post_content = '';
            $post_excerpt = '';
            $gallery_images_id = [];
            $thumbnail_id = '';
            if (empty($post_title)) {
                fwrite($log_file, "<p class='dev-text-rose-500'>این پست شامل product title نمیباشد و نمیتوان وارد کرد.</p>");
                $importable = false;
            }
            if (empty($sku)) {
                fwrite($log_file, "<p class='dev-text-rose-500'>این پست شامل SKU نمیباشد و نمیتوان وارد کرد.</p>");
                $importable = false;
            }
            if (!$importable) {
                fwrite($log_file, "<p class='dev-text-rose-500'>این محصول (پست شماره : $exel_point ) به دلیل مشکل(های) بالا قابل وارد شدن نیست.</p>");
                fwrite($log_file, "<p><===============================================></p>");
                $resolve($exel_point);
                return;
            }
            if (!empty($exel_post_content)) {
                if (!$this->check_file_exists_on_server($exel_post_content)) {
                    fwrite($log_file, "<p class='dev-text-rose-500'>لینک وارد شده برای long mother description به دلیل داخلی نبود یه موجود نبودن فایل ، مورد دارد.</p>");
                } else {
                    $post_content_attachment_id = attachment_url_to_postid($exel_post_content);
                    $post_content = $this->word_extractor(get_attached_file($post_content_attachment_id), $post_title);
                }
            }
            if (!empty($exel_post_excerpt)) {
                if (!$this->check_file_exists_on_server($exel_post_excerpt)) {
                    fwrite($log_file, "<p class='dev-text-rose-500'>لینک وارد شده برای short mother description به دلیل داخلی نبود یه موجود نبودن فایل ، مورد دارد.</p>");
                } else {
                    $post_excerpt_attachment_id = attachment_url_to_postid($exel_post_excerpt);
                    $post_excerpt = $this->word_extractor(get_attached_file($post_excerpt_attachment_id), $post_title);
                }
            }
            if (!empty($exel_post_images)) {
                if (!$this->check_file_exists_on_server($exel_post_images)) {
                    fwrite($log_file, "<p class='dev-text-rose-500'>لینک وارد شده برای images به دلیل داخلی نبود یه موجود نبودن فایل ، مورد دارد.</p>");
                } else {
                    $images_attachment_id = attachment_url_to_postid($exel_post_images);
                    $images_id = $this->images_attachment_id_extractor($images_attachment_id);
                    for ($i = 0; $i < count($images_id); $i++) {
                        if ($i === 0) {
                            $thumbnail_id = $images_id[$i];
                        } else {
                            $gallery_images_id[] = $images_id[$i];
                        }
                    }

                }
            }
            $meta_data = [
                "yoast-google-preview-title-metabox" => $post['yoast-google-preview-title-metabox'] ?? "خالی",
                "yoast-google-focus-title-metabox" => $post['yoast-google-focus-title-metabox'] ?? "خالی",
                "yoast-google-google-title-metabox" => $post['yoast-google-google-title-metabox'] ?? "خالی",
                "_shy_product_inventory_reduction_factor" => $post['_shy_product_inventory_reduction_factor'] ?? 0.1
            ];
            $menu_order = $post['menu_order'];
            $check_exist = wc_get_product_id_by_sku($sku);
            $category = get_term_by('name', $post['term_name'], $taxonomy);
            $array_default_key = [];
            $array_default_value = [];
            if ($category === false) {
                $taxonomy_id = wp_insert_term($post['term_name'], $taxonomy);
                $term_id[] = $taxonomy_id['term_id'];
                fwrite($log_file, "<p class='dev-text-green-500'>دسته بندی تعیین شده برای این محصول وجود ندارد ، این دسته بندی ساخته شد .</p>");
            } else {
                $term_id[] = $category->term_id;
            }

            for ($i = 1; $i <= $attribute_count; $i++) {
                $attribute_name_ = $post["attribute_name_$i"];
                $attribute_value_ = $post["attribute_value_$i"];
                $array_default_key[] = sanitize_title($attribute_name_);
                $array_default_value[] = $post["attribute_default_$i"];
                $attr = $this->create_attribute($attribute_name_, $attribute_value_, $i);
                if ($attr !== false) {
                    $attributes[$i] = $attr;
                }
            }
            $attribute_default = array_combine($array_default_key, $array_default_value);
            $submit_post = $this->Create_variable_product($check_exist, $post_title, $post_content, $post_excerpt, $sku, $term_id, $menu_order, $attributes, $attribute_default, $thumbnail_id, $gallery_images_id, $meta_data);
            if ($submit_post) {
                fwrite($log_file, "<p class='dev-text-green-500'>پست شماره $exel_point وارد شد و در صفحه محصولات قابل مشاهده میباشد.</p>");
            } else {
                fwrite($log_file, "<p class='dev-text-rose-500'>پست شماره $exel_point وارد نشد ، لطفا اکسل را برسی نمایید</p>");
            }
            fwrite($log_file, "<p><===============================================></p>");
            if ($exel_point === $posts_found) {
                $resolve(true);
                fwrite($log_file, "<p><===============================================></p>");
                fwrite($log_file, "تمام پست ها وارد شدند .\n");
                fwrite($log_file, "<p><===============================================></p>");
            }
        });
    }

    private function process_import_variation($post, $log_file, $counter, $posts_found, $exel_point): Promise
    {
        return new Promise(function ($resolve, $reject) use ($post, $log_file, $counter, $posts_found, $exel_point) {

            fwrite($log_file, "<p><===============================================></p>");
            fwrite($log_file, "<p class='dev-text-green-500'>درحال وارد کردن پست شماره : $exel_point</p>");
            $importable = true;
            $attribute_count = 2;
            $attributes = [];
            $sku = $post['sku'];
            $mother_sku = $post['mother_sku'];
            $exel_post_content = $post['content'];
            $exel_post_image = $post['image'];

            if (empty($mother_sku)) {
                fwrite($log_file, "<p class='dev-text-rose-500'>این پست شامل SKU Mother نمیباشد و نمیتوان وارد کرد.</p>");
                $importable = false;
            }
            if (empty($sku)) {
                fwrite($log_file, "<p class='dev-text-rose-500'>این پست شامل SKU نمیباشد و نمیتوان وارد کرد.</p>");
                $importable = false;
            }
//            if (empty($exel_post_content)) {
//                fwrite($log_file, "<p class='dev-text-rose-500'>این پست شامل Short Description (HTML) نمیباشد و نمیتوان وارد کرد.</p>");
//                $importable = false;
//            }
            if (!$this->check_file_exists_on_server($exel_post_image)) {
                fwrite($log_file, "<p class='dev-text-rose-500'>لینک وارد شده برای images به دلیل داخلی نبود یه موجود نبودن فایل ، مورد دارد.</p>");
            }
            if (empty($post['ajax_code'])) {
                fwrite($log_file, "<p class='dev-text-rose-500'>این پست شامل AJAX-Code نمیباشد و نمیتوان وارد کرد.</p>");
                $importable = false;
            }
            if (!$importable) {
                fwrite($log_file, "<p class='dev-text-rose-500'>این محصول (پست شماره : $exel_point ) به دلیل مشکل(های) بالا قابل وارد شدن نیست.</p>");
                fwrite($log_file, "<p><===============================================></p>");
                $resolve($exel_point);
                return;
            }

            $check_exist = wc_get_product_id_by_sku($sku);
            $mother_post_id = wc_get_product_id_by_sku($mother_sku);
            $description = $post['content'];
            $html_description = "";
            if (!empty($description)) {
                $description_explode = explode('|', $description);
                if (isset($description_explode)) {
                    $html_description .= "<ul>";
                    foreach ($description_explode as $html) {
                        $html_description .= "<li>$html</li>";
                    }
                    $html_description .= "</ul>";
                }
            }
            $meta_data = [
                "AJAX-Code" => $post['ajax_code'],
                "_dede_main_unit" => $post['main_unit'],
                "_dede_sub_unit_name_1" => $post['sub_unit_name_1'],
                "_dede_sub_unit_value_1" => $post['sub_unit_value_1'],
                "_dede_sub_unit_name_2" => $post['sub_unit_name_2'],
                "_dede_sub_unit_value_2" => $post['sub_unit_value_2'],
                "minimum_quantity" => $post['_shy_product_inventory_reduction_factor'],
                "package_quantity" => $post['_shy_product_inventory_reduction_factor'],
                "_shy_product_guid" => $post['_shy_product_guid'],
            ];
            for ($i = 1; $i <= $attribute_count; $i++) {
                $attribute_name_ = $post["attribute_name_$i"];
                $attribute_value_ = $post["attribute_value_$i"];
                if (isset($attribute_value_)) {
                    $attributes['attribute_' . sanitize_title($attribute_name_)] = trim($attribute_value_);
                }
            }
            $image_id = '';
            if (!empty($exel_post_image) && $this->check_file_exists_on_server($exel_post_image)) {
                $image_id = attachment_url_to_postid($exel_post_image);
            }
            $backorder_exel = $post['on_back_order'];
            $backorder = $backorder_exel == "1" ? "yes" : "no";
            if (!$post['updated']) {
                fwrite($log_file, "<p class='dev-text-green-500'>این محصول نیاز به بروز رسانی ندارد. </p>");
            } else {
                $submit_post = $this->Create_variation_product($check_exist, $sku, $mother_post_id, $html_description, $attributes, $image_id, $backorder, $meta_data);
                if ($submit_post) {
                    fwrite($log_file, "<p class='dev-text-green-500'>پست شماره $exel_point وارد شد و در صفحه محصولات قابل مشاهده میباشد.</p>");
                } else {
                    fwrite($log_file, "<p class='dev-text-rose-500'>پست شماره $exel_point وارد نشد ، لطفا اکسل را برسی نمایید</p>");
                }
                fwrite($log_file, "<p><===============================================></p>");

            }
            fwrite($log_file, "<p><===============================================></p>");
            if ($exel_point === $posts_found) {
                $resolve(true);
                fwrite($log_file, "<p><===============================================></p>");
                fwrite($log_file, "تمام پست ها وارد شدند .\n");
                fwrite($log_file, "<p><===============================================></p>");
            }
        });
    }

    function upload_file_to_media_library($file_name): void
    {
        $plugin_file_path = $this->log_path;
        $upload_dir = wp_upload_dir();
        $destination_file_path = $upload_dir['path'] . "/$file_name.txt";

        copy($plugin_file_path, $destination_file_path);
        $file_array = array(
            'name' => basename($destination_file_path),
            'tmp_name' => $destination_file_path
        );
        media_handle_sideload($file_array, 0);
    }

    function check_file_exists_on_server($url): bool
    {
        // Get the WordPress site URL
        $wordpress_site_url = get_site_url();

        // Remove the site URL from the file URL to get the relative path
        $relative_path = str_replace($wordpress_site_url, '', $url);

        // Get the absolute path on the server
        $absolute_path = ABSPATH . $relative_path;

        // Check if the file exists and is a file
        if (file_exists($absolute_path) && is_file($absolute_path)) {
            return true;
        } else {
            return false;
        }
    }
}