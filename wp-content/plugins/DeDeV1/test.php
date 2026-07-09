<?php
//
//public function exel_extractor(): void
//{
//    // توابع دیگر کلاس
//    $file = $_POST['dede_exel_file_for_upload'];
//    $file_type = $_POST['type_of_exel'];
//    $this->post_type = $file_type;
//    $request_type = $_POST['dede_dev_request_type'];
//    $exel_title = [];
//    $exel_array = [];
//
//    if (empty($file)) {
//        wp_send_json_error(["message" => "فایل اکسل را انتخاب نمایید"]);
//    } elseif ($file_type == false) {
//        wp_send_json_error(["message" => "نوع فایل را انتخاب کنید"]);
//    } else {
//        $exel_path = get_attached_file($file);
//        $exel_render = IOFactory::load($exel_path);
//        $exel_array = $exel_render->getActiveSheet()->toArray();
//    }
//
//    if ($request_type === "upload") {
//        if (file_exists($this->log_path)) {
//            wp_send_json_success(["message" => "درحال حاظر یک فایل درحال وارد شدن میباشد. میتوانید در ادامه لاگ رو برسی نمایید.", "data" => "exists", "options" => ["accept" => "باشه"]]);
//        }
//        foreach ($exel_array as $exel) {
//            foreach ($exel as $title) {
//                if (empty($title)) {
//                    continue;
//                }
//                $exel_title[$title] = [];
//            }
//            wp_send_json_success(['message' => 'اگر تغییراتی لازم است انجام دهید.', 'data' => $exel_title, "options" => ["accept" => "باشه"]]);
//        }
//    } elseif ($request_type === "import") {
//        $log_file = fopen($this->log_path, 'c');
//        $this->product_importer_batch($exel_array, $log_file);
//    }
//}
//
//// توابع دیگر کلاس
//
//private function product_importer_batch($exel_array, $log_file): void
//{
//    $counter = 0;
//    $import_meta_data = $_POST['type_of_row_metadata'];
//    $import_ready = array_filter(
//        array_map(
//            function ($exel) use ($import_meta_data) {
//                if ($exel[0] === null) {
//                    return null;
//                }
//                return array_combine($import_meta_data, $exel);
//            },
//            array_slice($exel_array, 1)
//        )
//    );
//
//    $posts_found = count($import_ready);
//    fwrite($log_file, "<p class='dev-text-green-500 dev-font-bold'>مجموع تعداد پست ها برای وارد کردن :$posts_found</p>");
//    $exel_point = 0;
//    if ($this->post_type === "variable") {
//        $this->process_import_variable_batch($import_ready, $log_file);
//    } elseif ($this->post_type === "variation") {
//        $this->process_import_variation_batch($import_ready, $log_file);
//    }
//
//    try {
//        $this->loop->run();
//    } catch (Exception $e) {
//        unlink($this->log_path);
//        wp_send_json_error(['message' => $e->getMessage(),  "options" => ["accept" => "باشه"]]);
//    }
//    unlink($this->log_path);
//    wp_send_json_success(['message' => "تمام پست ها وارد شدند", "options" => ["accept" => "باشه"]]);
//}
//
//private function process_import_variable_batch($posts, $log_file)
//{
//    return $this->process_batch_import($posts, $log_file, 5, function ($post, $log_file, $current_post) {
//        return $this->process_import_variable($post, $log_file, $current_post);
//    });
//}
//
//private function process_import_variation_batch($posts, $log_file)
//{
//    return $this->process_batch_import($posts, $log_file, 5, function ($post, $log_file, $current_post) {
//        return $this->process_import_variation($post, $log_file, $current_post);
//    });
//}