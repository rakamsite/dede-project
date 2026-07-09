<?php
add_action('wp_ajax_update_variation_price', 'update_variation_price_callback');
add_action('wp_ajax_nopriv_update_variation_price', 'update_variation_price_callback');
add_action('wp_ajax_manage_voting_status', 'manage_voting_status_callback');
add_action('wp_ajax_nopriv_manage_voting_status', 'manage_voting_status_callback');
function update_variation_price_callback()
{
    $wallet_status = cmb2_get_option('wallet_option', 'wallet_active');
    $wallet_percentage = cmb2_get_option('wallet_option', 'wallet_percentage');
    function calculatePercentage($total, $percentage)
    {
        return ($percentage / 100) * $total;
    }

    if (isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];
        $product = wc_get_product($product_id);
        $variation_id = 0;
        $specific_id = 'attribute_';
        $filtered_post = array_filter($_POST, function ($key) use ($specific_id) {
            return str_starts_with($key, $specific_id);
        }, ARRAY_FILTER_USE_KEY);
        if ($product->is_type('variable')) {
            $args = array(
                'post_type' => 'product_variation',
                'post_status' => array('publish'),
                'numberposts' => -1,
                'order' => 'asc',
                'post_parent' => $product_id
            );
            $variations = get_posts($args);
            foreach ($variations as $variation) {
                $variation_product = wc_get_product($variation->ID);
                $variation_attribute = $variation_product->get_attributes();
                $attr_filter []= $variation_product->get_attributes();
                $variation_attribute = array_values($variation_attribute);
                $filtered_post = array_values($filtered_post);
                if ($variation_attribute === $filtered_post) {
                    $variation_id = $variation->ID;
                }
            }
        }
        $variation_product = wc_get_product($variation_id);
        if (!empty($variation_product)) {
            $image = get_the_post_thumbnail_url($variation_id, 'full');
            $description = $variation_product->get_description();
            $html = 'فی:&nbsp;';
            $wallet_price = '';
            $total_amount = '';
            $stock = '';
            $suk = '';
            $reg = $variation_product->get_regular_price();
            $sale = $variation_product->get_sale_price();
            $min_quantity = $variation_product->get_meta("minimum_quantity", true);
            $min_quantity = $min_quantity ? $min_quantity : 1;
            $max_quantity = $variation_product->get_meta("maximum_quantity", true);
            $package_quantity = $variation_product->get_meta("package_quantity", true);
            $package_quantity = $package_quantity ? $package_quantity : "1";
            $product_stock_quantity = $variation_product->get_stock_quantity();
            $suk = get_post_meta($variation_id, '_sku', true);
            $units = "";
            $units_blocks = "";
            $unit_options = '';
            $main_unit = $variation_product->get_meta("_dede_main_unit", true);
            $sub_unit_name_1 = $variation_product->get_meta("_dede_sub_unit_name_1", true);
            $sub_unit_value_1 = $variation_product->get_meta("_dede_sub_unit_value_1", true);
            $sub_unit_name_2 = $variation_product->get_meta("_dede_sub_unit_name_2", true);
            $sub_unit_value_2 = $variation_product->get_meta("_dede_sub_unit_value_2", true);
            $sub_unit_exist = 0;
            $stock_status = $variation_product->get_stock_status();
            if ( $stock_status === "outofstock") {
                $stock .= '<p class="text-[#E3000F]">نا موجود</p>';
                $subscription_button = out_of_stock_manager($product_id , $variation_id);
            } else {
                $stock .= '<p class="text-[#008826] ">موجود</p>';
            }

            if (!empty($sale)) {
                $html .= '<p class="text-sm">' . wc_price($reg) . '</p>';
                $html .= '<p class="text-sm price_final">&nbsp;' . wc_price($sale) . '</p>';
                $total_amount = wc_price(intval($sale) * $min_quantity);
                if ($wallet_status) {
                    $wallet_price .= wc_price(calculatePercentage(intval($sale), $wallet_percentage));
                }
            } else {
                $html .= '<p class="text-sm price_final">&nbsp;' . wc_price($reg) . '</p>';
                $total_amount = wc_price(intval($reg) * $min_quantity);

                if ($wallet_status && !empty($reg)) {
                    $wallet_price .= wc_price(calculatePercentage($reg, $wallet_percentage));
                }
            }
            if (isset($main_unit)) {
                $units .= str_replace("S", $main_unit, "<li>واحد اصلی:&nbsp;S </li>");
                $units_blocks .= <<<HTML
                                <div>
                                    <input id="order_main_unit" class="peer/main" type="radio" value="$package_quantity" name="order_Type" checked/>
                                    <label for="order_main_unit" class="peer-checked/main:text-black text-[#878787]">
                                        واحد (اصلی):&nbsp; 
                                        <span id="order_main_unit_name">$main_unit</span>
                                    </label>
                                </div>

HTML;
            }
            if ($sub_unit_name_1 !== '' && intval($sub_unit_value_1) !== 0) {
                $units .= str_replace(["A", "B", "C"], [$sub_unit_name_1, $sub_unit_value_1, $main_unit], "<li>واحد فرعی یک: A (هر A حاوی B C)</li>");
                $unit_options .= "<option value='$sub_unit_value_1'>$sub_unit_name_1</option>";
            } else {
                $sub_unit_exist++;
            }
            if ($sub_unit_name_2 !== '' && intval($sub_unit_value_2) !== 0) {
                $sub_val_2_in_main = intval($sub_unit_value_2) / intval($sub_unit_value_1);
                $units .= str_replace(["A", "B", "C", "D", "E"], [$sub_unit_name_2, $sub_unit_name_1, $sub_val_2_in_main, $sub_unit_value_2, $main_unit], "<li>واحد فرعی دو: A (هر A حاوی C B / D E)</li>");
                $unit_options .= "<option value='$sub_unit_value_2'>$sub_unit_name_2</option>";
            } else {
                $sub_unit_exist++;
            }
            $sub_unit_exist_status = $sub_unit_exist === 2 ? "disabled" :'';

            $units_blocks .= <<<HTML
                                <div class="flex items-center gap-2">
                                    <input id="sub_unit" class="peer/subs" type="radio" name="order_Type" $sub_unit_exist_status/>
                                    <label for="sub_unit" class="peer-checked/subs:text-black text-[#878787]">
                                        واحد فرعی:
                                    </label>
                                    <select id="sub_controller" class="peer-checked/subs:block hidden p-1 bg-white border rounded-lg" >
                                    $unit_options
                                    </select>
                                </div>
                HTML;

        } else {
            $stock = '<p class="text-[#E3000F]">نا موجود</p>';
            $html = '<p class="text-smprice_final">' . wc_price(0) . '</p>';
            $min_quantity = 0;
            $max_quantity = 0;
            $package_quantity = 0;
            $product_stock_quantity = 0;
        }
        wp_send_json_success([
            'html' => $html,
            'var_id' => $variation_id,
            'percentage' => $wallet_price ?? '',
            'total_amount' => $total_amount ?? '',
            'stock' => $stock,
            'stock_status' =>$stock_status ?? '',
            'min_quantity' => $min_quantity,
            'max_quantity' => ($max_quantity > $product_stock_quantity) ? $product_stock_quantity : $max_quantity,
            'package_quantity' => $package_quantity,
            'image' => $image ?? '',
            'description' => $description ?? '',
            'sukcode' => $suk ?? '',
            'main_unit' => $main_unit ?? '',
            'units' => $units ?? '',
            'unit_blocks' => $units_blocks ?? '',
            'sub_button' => $subscription_button ?? '',
            'filtered_post'=>$attr_filter ?? ''

        ]);
    }
    wp_die();
}

function manage_voting_status_callback()
{
    $commentId = intval($_POST['commentId']);
    $RatedComments = json_decode($_COOKIE['RatedComments']) ?? [];
    if (!in_array($commentId, $RatedComments)) {
        if ($_POST['voteUp']) {
            $current_value = get_comment_meta($commentId, "_dede_comment_vote_up_rate_", true);
            if (!$current_value) {
                $current_value = 0;
            }
            $new_value = $current_value + 1;
            update_comment_meta($commentId, "_dede_comment_vote_up_rate_", $new_value);
        } elseif ($_POST['voteDown']) {
            $current_value = get_comment_meta($commentId, "_dede_comment_vote_down_rate_", true);
            if (!$current_value) {
                $current_value = 0;
            }
            $new_value = $current_value + 1;
            update_comment_meta($commentId, "_dede_comment_vote_down_rate_", $new_value);
        }
        array_push($RatedComments, $commentId);
        setcookie('RatedComments', json_encode($RatedComments), time() + (60 * 60 * 24));
        wp_send_json_success("نظر شما درباره این دیدگاه ثبت شد");
    } else {
        wp_send_json_success("شما قبلا نظر خود را درباره این دیدگاه ثبت کرده اید");
    }
}
function out_of_stock_manager($product_id , $variation_id): string
{
    global $wpdb;
    $user_id = get_current_user_id();
    $table_name = $wpdb->prefix . (new \classes\stock_quantity_handler\dede_v2_stock_quantity())->db_name;
    $check_exist = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d AND variationId = %d AND productId = %d" , [$user_id, $variation_id, $product_id]));
    $button = '';
    if ($check_exist) {
        $button = <<<HTML
        <button class=" w-full bg-[#2f2483] rounded-lg text-white md:text-[24px] text-base p-4 flex items-center justify-center gap-3"
                value="0">
            <svg width="33" height="34" viewBox="0 0 33 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21.5698 26.2582V27.9436C21.5698 29.2847 21.0356 30.5708 20.0849 31.519C19.1341 32.4673 17.8446 33 16.5 33C15.1554 33 13.8659 32.4673 12.9151 31.519C11.9644 30.5708 11.4302 29.2847 11.4302 27.9436V26.2582M21.5698 26.2582H11.4302M21.5698 26.2582H27.6366C28.2839 26.2582 28.6083 26.2582 28.8703 26.1705C29.1166 26.0873 29.3404 25.9485 29.5242 25.765C29.708 25.5814 29.8468 25.3579 29.9298 25.1121C30.0194 24.8492 30.0194 24.5255 30.0194 23.875C30.0194 23.5901 30.0194 23.4485 29.9957 23.312C29.954 23.0568 29.854 22.8146 29.7034 22.6041C29.6223 22.4912 29.5209 22.3901 29.3198 22.1895L28.6607 21.5322C28.5556 21.4272 28.4722 21.3027 28.4154 21.1656C28.3586 21.0285 28.3294 20.8816 28.3295 20.7333V14.46C28.3295 12.9107 28.0235 11.3765 27.429 9.94506C26.8345 8.51364 25.9632 7.21302 24.8647 6.11746C23.7662 5.0219 22.4622 4.15285 21.0269 3.55994C19.5917 2.96702 18.0535 2.66186 16.5 2.66186C14.9465 2.66186 13.4083 2.96702 11.9731 3.55994C10.5378 4.15285 9.23376 5.0219 8.13529 6.11746C7.03682 7.21302 6.16547 8.51364 5.57098 9.94506C4.9765 11.3765 4.67052 12.9107 4.67052 14.46V20.7333C4.67062 20.8816 4.64143 21.0285 4.58459 21.1656C4.52776 21.3027 4.44441 21.4272 4.33929 21.5322L3.68022 22.1895C3.47743 22.3918 3.37773 22.4912 3.2983 22.6024C3.14625 22.8131 3.04504 23.0559 3.00256 23.312C2.98059 23.4469 2.98059 23.5901 2.98059 23.875C2.98059 24.5255 2.98059 24.8492 3.06847 25.1121C3.15184 25.3583 3.29116 25.582 3.47556 25.7656C3.65996 25.9492 3.88443 26.0878 4.13143 26.1705C4.39337 26.2582 4.71615 26.2582 5.36339 26.2582H11.4302M26.6717 1C29.0019 2.75186 30.8376 5.07568 32 7.74518M6.33003 1C3.99917 2.75165 2.16288 5.07548 1 7.74518" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>خبر دار میشوید</span>
            <svg class="animate-spin add-to-card-loading h-6 w-6 text-white hidden"
                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </button>
HTML;

    }else{
        $button = <<<HTML
        <button class="stock_quantity_manager w-full bg-[#E3000F] rounded-lg text-white md:text-[24px] text-base p-4 flex items-center justify-center gap-3"
                value="$product_id" data-value="$variation_id">
            <svg width="33" height="34" viewBox="0 0 33 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21.5698 26.2582V27.9436C21.5698 29.2847 21.0356 30.5708 20.0849 31.519C19.1341 32.4673 17.8446 33 16.5 33C15.1554 33 13.8659 32.4673 12.9151 31.519C11.9644 30.5708 11.4302 29.2847 11.4302 27.9436V26.2582M21.5698 26.2582H11.4302M21.5698 26.2582H27.6366C28.2839 26.2582 28.6083 26.2582 28.8703 26.1705C29.1166 26.0873 29.3404 25.9485 29.5242 25.765C29.708 25.5814 29.8468 25.3579 29.9298 25.1121C30.0194 24.8492 30.0194 24.5255 30.0194 23.875C30.0194 23.5901 30.0194 23.4485 29.9957 23.312C29.954 23.0568 29.854 22.8146 29.7034 22.6041C29.6223 22.4912 29.5209 22.3901 29.3198 22.1895L28.6607 21.5322C28.5556 21.4272 28.4722 21.3027 28.4154 21.1656C28.3586 21.0285 28.3294 20.8816 28.3295 20.7333V14.46C28.3295 12.9107 28.0235 11.3765 27.429 9.94506C26.8345 8.51364 25.9632 7.21302 24.8647 6.11746C23.7662 5.0219 22.4622 4.15285 21.0269 3.55994C19.5917 2.96702 18.0535 2.66186 16.5 2.66186C14.9465 2.66186 13.4083 2.96702 11.9731 3.55994C10.5378 4.15285 9.23376 5.0219 8.13529 6.11746C7.03682 7.21302 6.16547 8.51364 5.57098 9.94506C4.9765 11.3765 4.67052 12.9107 4.67052 14.46V20.7333C4.67062 20.8816 4.64143 21.0285 4.58459 21.1656C4.52776 21.3027 4.44441 21.4272 4.33929 21.5322L3.68022 22.1895C3.47743 22.3918 3.37773 22.4912 3.2983 22.6024C3.14625 22.8131 3.04504 23.0559 3.00256 23.312C2.98059 23.4469 2.98059 23.5901 2.98059 23.875C2.98059 24.5255 2.98059 24.8492 3.06847 25.1121C3.15184 25.3583 3.29116 25.582 3.47556 25.7656C3.65996 25.9492 3.88443 26.0878 4.13143 26.1705C4.39337 26.2582 4.71615 26.2582 5.36339 26.2582H11.4302M26.6717 1C29.0019 2.75186 30.8376 5.07568 32 7.74518M6.33003 1C3.99917 2.75165 2.16288 5.07548 1 7.74518" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>موجود شد خبرم کنید</span>
            <svg class="animate-spin add-to-card-loading h-6 w-6 text-white hidden"
                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </button>
HTML;
    }


    return $button;
}
