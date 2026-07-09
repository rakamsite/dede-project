<?php
add_action('wp_ajax_add_to_cart_ajax', 'add_to_cart_ajax');
add_action('wp_ajax_nopriv_add_to_cart_ajax', 'add_to_cart_ajax');
/**
 * @throws Exception
 */
function add_to_cart_ajax()
{
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $product = wc_get_product($product_id);
    $unit_selected = $_POST['unit_selected'];
    $unit_quantity = $_POST['unit_quantity'];
    $unit_selected_pakage = $_POST['unit_selected_pakage'];
    $main_unit_name = $_POST['main_unit_name'];
    $var_id = !empty($_POST['var_id']) ? intval($_POST['var_id']) : $product_id;
    $minicartinformation = '';
    $backOrder = "";
    $productQyt = "";
    $cart_items = WC()->cart->get_cart();

    foreach ( $cart_items as $cart_item ) {
        if ( $cart_item['product_id'] === $product_id && ( ! isset( $var_id ) || $cart_item['variation_id'] === $var_id ) ) {
            WC()->cart->remove_cart_item( $cart_item['key'] );
            break;
        }
    }
    if (isset($_POST['product_id']) && isset($_POST['quantity']) && $_POST['update_cart'] !== 'true') {
        if (!$product) {
            wp_send_json_error("محصول نا معتبر");
        }
        if ($product->is_type('variable')) {
            if (isset($_POST['var_id'])) {
                $variation = wc_get_product($var_id);
                $backOrder = $variation->backorders_allowed();
                $productQyt = $variation->get_stock_quantity();
                if (!$variation->is_type('variation') || $variation->get_parent_id() !== $product_id) {
                    wp_send_json_error("محصول انتخاب شده درست نمیباشد.");
                }
                WC()->cart->add_to_cart($product_id, $quantity, $var_id ,[] ,["unit_selected" =>$unit_selected , "unit_quantity"=> $unit_quantity, "main_unit" => $main_unit_name,"unit_selected_pakage" =>$unit_selected_pakage]);
            } else {
                wp_send_json_error("محصولات متغیر نیاز به انتخاب یک متغیر دارند.");
            }
        } else {
            $backOrder = $product->backorders_allowed();
            WC()->cart->add_to_cart($product_id, $quantity, $var_id ,[] ,["unit_selected" =>$unit_selected , "unit_quantity"=> $unit_quantity, "main_unit" => $main_unit_name,"unit_selected_pakage" =>$unit_selected_pakage]);
        }
        $item_price = '';
        foreach ($cart_items as $cart_item_key => $cart) {
            if ($cart['data']->get_id() === $var_id) {
                $item_price .= $cart['data']->get_price();
            }
        }
        $cart_count = WC()->cart->get_cart_contents_count();
        $price_final ='';
        if (!empty($product->get_sale_price())){
            $price_final = $product->get_sale_price();
        }else{
            $price_final = $product->get_price();
        }
        $price_final_total = wc_price(WC()->cart->get_cart_contents_total());
        $product_info = '<img class="w-[140px] h-[140px] rounded-lg" src="' . wp_get_attachment_image_url($product->get_image_id()) . '"><div class="mr-5 w-full flex flex-col gap-8"><p class="text-[#525252] ">' . $product->get_name() . '</p><p>فی: ' . wc_price($price_final) . '</p><p>کل: ' . $price_final_total . '</p></div>';
        $cart_items = WC()->cart->get_cart();
        $added = false;
        if (count($cart_items) > 0) {
            $minicartinformation .= '<ul>';
            foreach ($cart_items as $cart_item_key => $cart_item) {
                $product_id = $cart_item['product_id'];
                $product = wc_get_product($product_id);
                $variation_id = $cart_item['variation_id'];
                $order_unit_quantity =$cart_item["unit_selected"];
                $order_unit_selected =$cart_item["unit_quantity"];
                $cart_item_exist = WC()->cart->find_product_in_cart($cart_item_key);
                if (!empty($cart_item_exist)) {
                    $added = true;
                }

                if ($product->is_type('variable')) {
                    $product = wc_get_product($variation_id);
                    $product_id = $variation_id;
                }
                $thumbnail_url = wp_get_attachment_image_url($product->get_image_id());
                $min_quantity = get_post_meta($product_id, 'minimum_quantity', true) ?? 1;
                $max_quantity = get_post_meta($product_id, 'maximum_quantity', true);
                $package_quantity = get_post_meta($product_id, 'package_quantity', true) ?? 1;
                $min_quantity = $min_quantity ? $min_quantity : "1";
                $max_quantity = $max_quantity ? $max_quantity : "999999";
                $package_quantity = $package_quantity ? $package_quantity : "1";
                $stock_quantity = $product->get_stock_quantity();
                $max_quantity = $max_quantity > $stock_quantity ? $max_quantity : $stock_quantity;
                $minicartinformation .= '<li class="flex flex-wrap border-b-[1px] py-5 relative">';
                $minicartinformation .= '<div class="flex">';
                $minicartinformation .= '<svg class="w-10 h-10 my-auto ml-5 text-[#525252] remove-product cursor-pointer" data-cart-items="' . $cart_item_key . '"  date-product-id="' . $product_id . '"  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"> <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>';
                $minicartinformation .= '<img class="w-[100px] h-[100px] my-auto object-fill rounded-lg" src="' . $thumbnail_url . '" />';
                $minicartinformation .= '<div class="flex flex-col gap-3 items-start md:text-[18px] font-[500] text-right mr-5">';
                $minicartinformation .= '<p class="text-[#525252]">' . $product->get_title() . '(' . $product->get_sku() . ')</p>';
                $minicartinformation .= "<p><span>مقدار:&nbsp;</span>$order_unit_selected $order_unit_quantity</p>";
                $minicartinformation .= "<p><span>قیمت:&nbsp;</span>".wc_price($cart_item['line_subtotal'])."</p>";
                $minicartinformation .= '</div>';
                $minicartinformation .= '</div>';
                $minicartinformation .= "<button value='$cart_item_key' class='md:absolute  bottom-5 left-2 show_or_edit_order self-end justify-self-start mx-auto mt-3 md:mt-0 bg-[#D9D9D9] text-[#0058BF] p-2 rounded-lg h-fit'>جزئیات / ویرایش</button>";
                $minicartinformation .= '</li>';

            }
            $minicartinformation .= '</ul>';
        }
        if ($quantity > $productQyt && $backOrder !== true) {
            $response = array(
                "added" => "false",
                "product_info" => "<p>با پوزش فراوان، مقدار مورد سفارش بیش از حد مجاز سفارش جهت این کالا می باشد. لطفا یا اقدام به سفارش مقدار کمتر نموده و یا با واحد فروش جهت امکان بررسی سفارش گذاری به مقدار بیشتر تماس حاصل فرمایید.</p>",
                "message" => "تعداد مورد سفارش شما مجاز نمیباشد",
//                "minicartinformation" => $minicartinformation,

            );
        } elseif ($backOrder === true) {
            $response = array(
                "added" => "true",
                "product_info" => $product_info,
                "message" => "محصول با موفقیت به سبدخرید اضافه شد",
                "minicartinformation" => $minicartinformation,
                "cart_count" => $cart_count,
                "final_price" =>$price_final_total
            );
        } else {
            $response = array(
                "added" => "true",
                'product_info' => $product_info,
                "message" => "محصول با موفقیت به سبدخرید اضافه شد",
                "minicartinformation" => $minicartinformation,
                "cart_count" => $cart_count,
                "final_price" =>$price_final_total
            );
        }
        wp_send_json_success($response);
    } elseif (isset($_POST['update_cart']) && $_POST['update_cart'] === 'true') {

        $data_cart_items = strval($_POST['cart_item_key']);
        WC()->cart->set_quantity($data_cart_items, $quantity);
        $cart_count = WC()->cart->get_cart_contents_count();
        wp_send_json_success(array("cart_count" => $cart_count));

    } elseif (isset($_POST['remove_product'])) {

        $data_cart_items = strval($_POST['cart_item_key']);
        WC()->cart->remove_cart_item($data_cart_items);
        $cart_count = WC()->cart->get_cart_contents_count();
        wp_send_json_success(array("cart_count" => $cart_count));

    }
}

