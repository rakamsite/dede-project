<?php
add_action("wp_ajax_dede_get_edit_cart" , "dede_get_edit_cart_callback");
add_action("wp_ajax_nopriv_dede_get_edit_cart" , "dede_get_edit_cart_callback");
function dede_get_edit_cart_callback(){
    $cart_item_key = $_POST["cart_item_key"];
    $cart_info = WC()->cart->get_cart_item($cart_item_key);
    $product_id = $cart_info['product_id'];
    $variation_id = $cart_info['variation_id'];
    $totalPrice = wc_price($cart_info['line_total']);
    $post = "";
    if ($variation_id !== 0){
        $post = new WC_Product_Variation($variation_id);
    }
    else{
        $post = new WC_Product($product_id);
    }
    $unit_selected =$cart_info['unit_selected'];
    $unit_quantity = $cart_info['unit_quantity'];
    $main_unit = $cart_info['main_unit'];
    $unit_selected_pakage = $cart_info['unit_selected_pakage'];
    if ($main_unit == $unit_selected){
        $quantity = $unit_quantity ;
    }else{
        $quantity = $unit_quantity * $unit_selected_pakage;
    }
    $name = $post->get_name();
    $image_url = wp_get_attachment_url($post->get_image_id());
    $attributes = $post->get_attributes();
    $url = $post->get_permalink();
    $attr = "";
    foreach ($attributes as $key => $value) {
        $key = urldecode($key);
        $key = str_replace('-', ' ', $key);
        $value = urldecode($value);
        $attr .= "<p class='text-sm leading-loose text-[#525252]' >$key: $value</p>";
    }

    $price = wc_price($post->get_price());
    $final_HTML = <<<HTML
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <img src="$image_url" class="aspect-square w-full md:w-auto my-auto rounded-lg shadow" >
            <div class="md:col-span-2">
                <div class=" leading-loose ">
                    <h2 class="text-lg leading-loose ">$name</h2>
                    $attr
                </div>
                <table class="w-full table-auto mt-3" >
                    <thead>
                        <tr>
                            <td class="w-1/4 bg-[#D9D9D9] text-[#525252] text-center py-2 rounded-r-lg">واحد</td>
                            <td class="w-1/4 bg-[#D9D9D9] text-[#525252] text-center py-2">مقدار</td>
                            <td class="w-1/4 bg-[#D9D9D9] text-[#525252] text-center py-2">واحد فرعی</td>
                            <td class="w-1/4 bg-[#D9D9D9] text-[#525252] text-center py-2 rounded-l-lg">مقدار(واحد فرعی)</td>
                        </tr>
                    </thead>
                    <tbody class="">
                        <tr class="">
                            <td class="text-center py-4" id="Edit_cart_main_unit">$main_unit</td>
                            <td class="text-center" id="Edit_cart_final_quantity">$quantity</td>
                            <td class="text-center" id="Edit_cart_unit_selected">$unit_selected</td>
                            <td class="text-center">
                                <div class="flex gap-2">
                                    <button class="quantityUp_card_list">
                                        <svg width="31" height="31" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="21.5124" cy="21.5124" r="21.0124" fill="white" stroke="#4B5259"></circle>
                                            <rect x="37.4731" y="19.4304" width="4.06503" height="30.8942" rx="2.03252" transform="rotate(90 37.4731 19.4304)" fill="#4B5259"></rect>
                                            <rect x="23.5942" y="37.4731" width="4.06503" height="30.8942" rx="2.03252" transform="rotate(-180 23.5942 37.4731)" fill="#4B5259"></rect>
                                        </svg>
                                    </button>
                                    <label class="hidden" for="Edit_cart_quantity"></label>
                                    <input type="hidden" name="edit_cart_main_unit_name" id="edit_cart_main_unit_name" value="$main_unit">
                                    <input type="hidden" name="edit_cart_selected_unit" id="edit_cart_selected_unit" value="$unit_selected">
                                    <input name="Edit_cart_quantity" type="number" id="Edit_cart_quantity" data-package-quantity="$unit_selected_pakage" value="$unit_quantity" class=" border-none py-1 text-center focus:outline-none active:!border-none w-10">
                                    <button class="quantityDown_card_list">
                                        <svg width="31" height="31" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="21.5124" cy="21.5124" r="21.0124" fill="white" stroke="#4B5259"></circle>
                                            <rect x="37.4731" y="19.4304" width="4.06503" height="30.8942" rx="2.03252" transform="rotate(90 37.4731 19.4304)" fill="#4B5259"></rect>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                    <div class="flex flex-row md:flex-col gap-3 justify-between ">
                        <div class="flex justify-around items-center w-full">
                            <p>فی: </p>
                            <p class="text-sm Edit_cart_price">$price</p>
                        </div>
                        <div class="flex justify-around items-center w-full">
                            <p>مبلغ: </p>
                            <p class="text-lg Edit_cart_price_total">$totalPrice</p>
                        </div>
                    </div>
                    <div class="flex gap-2 justify-between">
                         <a class="rounded-lg text-white p-3 bg-[#E3000F] flex items-center justify-center w-full" href="$url">
                             تغییر واحد
                         </a>
                        <button class="rounded-lg flex items-center justify-center gap-1 text-white p-3 bg-[#2F2483] w-full Edit_cart_update" value="$product_id" data-var-id="$variation_id">
                            بروزرسانی
                            <div id="Edit_cart_update_spinner" class="hidden inline" role="status">
                                <svg  aria-hidden="true" class=" w-5 h-5 text-gray-200 animate-spin dark:text-gray-600 fill-gray-600 dark:fill-gray-300" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                                </svg>
                                <span class="sr-only">Loading...</span>
                            </div>

                        </button>
                    </div>
                </div>
            </div>
        </div>
HTML;

    wp_die($final_HTML);
}