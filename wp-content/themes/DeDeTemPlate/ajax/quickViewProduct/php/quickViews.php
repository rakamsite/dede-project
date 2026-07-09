<?php
add_action('wp_ajax_quick_view_product', 'quick_view_product_callback');
add_action('wp_ajax_nopriv_quick_view_product', 'quick_view_product_callback');

function quick_view_product_callback()
{
    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $product = wc_get_product($product_id);
    if ($product) {
        $title = $product->get_title();
        $thumb_id = $product->get_image_id('view');
        $thumb_img_URL = wp_get_attachment_image_url($thumb_id, 'full');
        $gallery_images_id = $product->get_gallery_image_ids();
//        $under_title = cmb2_get_option('product_section_page', 'product_under_title');
//        $wallet_status = cmb2_get_option('wallet_option', 'wallet_active');
//        $wallet_percentage = cmb2_get_option('wallet_option', 'wallet_percentage');
//        $wallet_min_checkout = cmb2_get_option('wallet_option', 'wallet_minimum_amount');
//        $wallet_max_charge = cmb2_get_option('wallet_option', 'wallet_maximum_charge');
        $min_quantity = $product->get_meta("minimum_quantity", true);
        $max_quantity = $product->get_meta("maximum_quantity", true);
        $package_quantity = $product->get_meta("package_quantity", true);
        $min_quantity = $min_quantity ? $min_quantity : "1";
        $package_quantity = $package_quantity ? $package_quantity : "1";
        $post_excerpt = '';
        $units = "";
        $units_blocks = "";
        $unit_options = "";
        $main_unit = "";
        $product_main_id = "";
        $var_id = "";
        $product_url = $product->get_permalink();

        function calculatePercentage($total, $percentage): float|int
        {
            return ($percentage / 100) * $total;
        }

        if (get_post_type($product_id) == 'product_variation') {
            $variation_stock = $product->get_stock_status();

            $product_main_id = $product->get_parent_id();
            $var_id = $product_id;
            $product_url = $product->get_permalink();
            $post_excerpt = $product->get_description();
            $main_unit = $product->get_meta("_dede_main_unit", true);
            $sub_unit_name_1 = $product->get_meta("_dede_sub_unit_name_1", true);
            $sub_unit_value_1 = $product->get_meta("_dede_sub_unit_value_1", true);
            $sub_unit_name_2 = $product->get_meta("_dede_sub_unit_name_2", true);
            $sub_unit_value_2 = $product->get_meta("_dede_sub_unit_value_2", true);
            $sub_unit_exist = 0;
            if (isset($main_unit)) {
                $units .= str_replace("S", $main_unit, "<li>واحد اصلی : S </li>");
                $units_blocks .= <<<HTML
                                <div>
                                    <input id="order_main_unit" class="peer/main" type="radio" value="$package_quantity" name="order_Type" checked/>
                                    <label for="order_main_unit" class="peer-checked/main:text-black text-[#878787]">
                                        واحد (اصلی) : 
                                        <span id="order_main_unit_name">$main_unit</span>
                                    </label>
                                </div>

HTML;
            }
            if (!empty($sub_unit_name_1)) {
                $units .= str_replace(["A", "B", "C"], [$sub_unit_name_1, $sub_unit_value_1, $main_unit], "<li>واحد فرعی یک: A (هر A حاوی B C)</li>");
                $unit_options .= "<option value='$sub_unit_value_1'>$sub_unit_name_1</option>";
            } else {
                $sub_unit_exist++;
            }
            if (!empty($sub_unit_name_2)) {
                $sub_val_2_in_main = intval($sub_unit_value_2) / intval($sub_unit_value_1);
                $units .= str_replace(["A", "B", "C", "D", "E"], [$sub_unit_name_2, $sub_unit_name_1, $sub_val_2_in_main, $sub_unit_value_2, $main_unit], "<li>واحد فرعی دو: A (هر A حاوی C B / D E)</li>");
                $unit_options .= "<option value='$sub_unit_value_2'>$sub_unit_name_2</option>";
            } else {
                $sub_unit_exist++;
            }
            $sub_unit_exist_status = $sub_unit_exist === 2 ? "disabled" : '';
            $units_blocks .= <<<HTML
                                <div class="flex items-center gap-2">
                                    <input id="sub_unit" class="peer/subs" type="radio" name="order_Type" $sub_unit_exist_status/>
                                    <label for="sub_unit" class="peer-checked/subs:text-black text-[#878787]">
                                        واحد فرعی :
                                    </label>
                                    <select id="sub_controller" class="peer-checked/subs:block hidden p-1 bg-white border rounded-lg ">
                                    $unit_options
                                    </select>
                                </div>
                HTML;


        } else {
            $product_main_id = $product_id;
            $post_excerpt = get_the_excerpt($product_id);
        }
        ?>
        <section
                class="container h-screen md:h-full lg:h-auto mx-auto grid grid-cols-1 lg:grid-cols-2 gap-10 overflow-y-auto px-1 md:px-10 lg:pb-0 "
                id="quick_view_post_viwed">
            <input type="hidden" id="unit_selected" value="<?php echo $main_unit ?>">
            <input type="hidden" id="unit_quantity" value="<?php echo $package_quantity ?>">

            <div class="h-auto">
                <div id="indicators-carousel" class="relative pb-0 flex flex-col items-center justify-center">
                    <div class="relative w-full h-auto overflow-hidden">
                        <div class="flex items-start relative lg:items-center" <?php echo (!empty($gallery_images_id)) ? 'id="slide-item-0"' : " " ?>>
                            <img id="image_slider_" src="<?php echo $thumb_img_URL; ?>"
                                 class="block object-cover w-auto h-full rounded-lg !mt-1"
                                 alt="<?php echo $title; ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="">
                <button class="absolute top-2 left-2 quick_view_container_close_button">
                    <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="16.5" cy="16.5" r="16.5" fill="#E3000F"/>
                        <rect x="23.782" y="7.66553" width="2.99892" height="22.7918" rx="1.49946"
                              transform="rotate(45 23.782 7.66553)" fill="white"/>
                        <rect x="25.9026" y="23.7817" width="2.99892" height="22.7918" rx="1.49946"
                              transform="rotate(135 25.9026 23.7817)" fill="white"/>
                    </svg>
                </button>
                <h2 class=" font-[700] text-[20px] pt-8 pb-5">
                    <?php echo $product->get_name(); ?>
                </h2>
                <div class=" [&_ul]:list-disc [&_ul]:mt-2 marker:[&_ul]:text-[#E3000F] pr-3">
                    <ul>
                        <?php echo $post_excerpt ?>
                    </ul>
                </div>
                <hr class="my-5 border-[#E9E9E9]"/>
                <input type="hidden" value="<?php echo $product_id; ?>" name="product_id" id="product_id">
                <?php
                if ($product->is_type('variable')) {
                    $default_attributes = $product->get_default_attributes();
                    $attributes = $product->get_variation_attributes();
                    foreach ($attributes as $attribute_name => $options) {
                        echo '<label for="' . esc_attr(sanitize_title($attribute_name)) . '">' . esc_html(wc_attribute_label($attribute_name)) . '</label>';
                        echo '<select id="attribute_' . esc_attr(sanitize_title($attribute_name)) . '" name="' . esc_attr(sanitize_title($attribute_name)) . '" class="w-full p-3 mt-2 border-[1px] border-black bg-white rounded-lg mb-5 variation_selector_quick_view variation_selector">';
                        $default_value = isset($default_attributes[sanitize_title($attribute_name)]) ? $default_attributes[sanitize_title($attribute_name)] : '';
                        echo '<option value="">' . 'یک گزینه را انتخاب کنید' . '</option>';
                        foreach ($options as $option) {
                            $selected = selected($default_value, $option, false);
                            echo "<option value='$option' $selected>$option</option>";
                        }
                        echo '</select>';
                    }
                }
                ?>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                    <div class="flex items-center rounded-lg bg-[#D9D9D9]">
                                <span class="rounded-r-lg p-2 bg-[#2F2483]">
                                    <svg class="w-8 h-8" viewBox="0 0 32 33" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 9.625V6.875C4 6.14565 4.28095 5.44618 4.78105 4.93046C5.28115 4.41473 5.95942 4.125 6.66667 4.125H9.33333M22.6667 4.125H25.3333C26.0406 4.125 26.7189 4.41473 27.219 4.93046C27.719 5.44618 28 6.14565 28 6.875V9.625M28 23.375V26.125C28 26.8543 27.719 27.5538 27.219 28.0695C26.7189 28.5853 26.0406 28.875 25.3333 28.875H22.6667M9.33333 28.875H6.66667C5.95942 28.875 5.28115 28.5853 4.78105 28.0695C4.28095 27.5538 4 26.8543 4 26.125V23.375M10.6667 9.625V23.375M16 9.625V23.375M22.6667 9.625V23.375"
                                              stroke="white" stroke-width="2" stroke-linecap="round"
                                              stroke-linejoin="round"/>
                                    </svg>
                                </span>
                        <p class="w-full text-sm text-center">کد کالا :<span
                                    id="sukCode"><?php echo $product->get_sku('view') ?></span></p>
                    </div>
                    <div class="flex items-center rounded-lg bg-[#D9D9D9]">
                                <span class="rounded-r-lg bg-[#2F2483]">
                                    <svg class="w-12 h-12" viewBox="0 0 50 50" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_2082_5118)">
                                            <path d="M11.4583 30.2083C10.476 29.226 9.98542 28.7354 9.98542 28.125C9.98542 27.5146 10.476 27.024 11.4583 26.0417L22.8437 14.6563C23.3896 14.1104 23.6625 13.8375 24.0156 13.7302C24.3687 13.625 24.7479 13.7 25.5052 13.8511L32.4489 15.2406C33.3823 15.426 33.8489 15.5198 34.1646 15.8354C34.4802 16.151 34.5729 16.6188 34.7604 17.5521L36.15 24.4958C36.301 25.251 36.376 25.6302 36.2698 25.9844C36.1625 26.3375 35.8896 26.6104 35.3437 27.1563L23.9583 38.5417C22.976 39.524 22.4854 40.0146 21.875 40.0146C21.2646 40.0146 20.774 39.524 19.7917 38.5417L11.4583 30.2083Z"
                                                  stroke="white" stroke-width="2"/>
                                            <path d="M29.1666 22.9167C29.7419 22.3414 29.7419 21.4086 29.1666 20.8333C28.5913 20.258 27.6586 20.258 27.0833 20.8333C26.508 21.4086 26.508 22.3414 27.0833 22.9167C27.6586 23.492 28.5913 23.492 29.1666 22.9167Z"
                                                  fill="white" stroke="white" stroke-width="2"/>
                                        </g>
                                        <defs>
                                        <clipPath id="clip0_2082_5118">
                                            <rect width="35.3553" height="35.3553" fill="white"
                                                  transform="translate(0 25) rotate(-45)"/>
                                        </clipPath>
                                        </defs>
                                    </svg>
                                </span>
                        <div class="w-full flex justify-center items-center text-sm text-center" id="price">
                            فی :
                            <?php
                            $precentage_price = '';
                            if ($product->is_type('variable')) {
                                echo '<p class="">بدون قیمت</p>';
                            } else {
                                if (!empty($product->get_sale_price())) {
                                    echo '<p class="text-sm price_final">' . wc_price($product->get_sale_price()) . '</p>';
                                    $precentage_price = $product->get_sale_price();
                                } elseif (empty($product->get_price())) {
                                    echo '<p class="text-sm">بدون قیمت</p>';
                                } else {
                                    echo '<p class="text-sm price_final">' . wc_price($product->get_regular_price()) . '</p>';
                                    $precentage_price = $product->get_regular_price();
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div class="flex items-center [&_span]:p-2 rounded-lg bg-[#D9D9D9]">
                                <span class="rounded-r-lg bg-[#2F2483]">
                                    <svg class="w-8 h-8" viewBox="0 0 37 37" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M24.6666 16.7381L12.3333 9.69048M20.2478 7.16567L29.0573 12.1994C29.5969 12.5077 30.0454 12.9531 30.3573 13.4906C30.6692 14.028 30.8334 14.6384 30.8333 15.2599V23.502C30.8334 24.1235 30.6692 24.7339 30.3573 25.2713C30.0454 25.8088 29.5969 26.2543 29.0573 26.5625L20.2478 31.5962C19.7154 31.9003 19.113 32.0602 18.5 32.0602C17.8869 32.0602 17.2845 31.9003 16.7522 31.5962L7.94263 26.5625C7.40303 26.2543 6.95454 25.8088 6.64264 25.2713C6.33074 24.7339 6.16652 24.1235 6.16663 23.502V15.2599C6.16652 14.6384 6.33074 14.028 6.64264 13.4906C6.95454 12.9531 7.40303 12.5077 7.94263 12.1994L16.7522 7.16567C17.2845 6.8616 17.8869 6.70166 18.5 6.70166C19.113 6.70166 19.7154 6.8616 20.2478 7.16567Z"
                                              stroke="white" stroke-width="2" stroke-linecap="round"
                                              stroke-linejoin="round"/>
                                        <path d="M7.04761 14.0952L16.8297 19.3633C17.3431 19.6397 17.917 19.7843 18.5 19.7843C19.083 19.7843 19.6569 19.6397 20.1703 19.3633L29.9524 14.0952M18.5 20.2619V31.7143"
                                              stroke="white" stroke-width="2" stroke-linecap="round"
                                              stroke-linejoin="round"/>
                                    </svg>
                                </span>
                        <div class="w-full text-sm text-center">
                            <div class="w-full" id="stock_manager">
                                <?php if ($product->get_stock_status() === "outofstock") {
                                    echo '<p class="text-[#E3000F]">نا موجود</p>';
                                } else {
                                    echo '<p class="text-[#008826] ">موجود</p>';
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-5 border-[#E9E9E9]"/>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <div class="text-[#4B5259] lg:col-span-2">
                        <div class="marker:[&_ul]:text-[#E3000F] [&_ul]:list-disc [&_ul]:list-inside [&_ul]:px-3">
                        </div>
                        <ul class="marker:text-[#E3000F] list-disc list-inside px-3" id="units_list">
                            <?php echo $units ?>
                        </ul>
                    </div>
                    <div class="flex flex-col self-center gap-3 pr-5" id="unit_blocks">
                        <?php echo $units_blocks ?>
                    </div>
                    <div class="grid grid-cols-1 mt-10 lg:mt-0 lg:pr-14 gap-2 lg:gap-0">
                        <div class="flex w-full h-full lg:gap-2 float-left items-center self-start">
                            <button id="quantityUp_quick">
                                <svg width="44" height="44" viewBox="0 0 44 44" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="21.5124" cy="21.5124" r="21.0124" fill="white"
                                            stroke="#4B5259"/>
                                    <rect x="37.4731" y="19.4304" width="4.06503" height="30.8942" rx="2.03252"
                                          transform="rotate(90 37.4731 19.4304)" fill="#4B5259"/>
                                    <rect x="23.5942" y="37.4731" width="4.06503" height="30.8942" rx="2.03252"
                                          transform="rotate(-180 23.5942 37.4731)" fill="#4B5259"/>
                                </svg>
                            </button>
                            <label for="quantity" class="hidden"></label>
                            <input type="number" value="<?php echo $package_quantity ?>" id="quantity"
                                   step="<?php echo $package_quantity ?>"
                                   data-pakage-quantity="<?php echo $package_quantity ?>"
                                   data-min-quantity="<?php echo $min_quantity ?>"
                                   data-max-quantity="<?php echo $max_quantity ?>"
                                   class="border-none lg:py-3 w-full text-center focus:outline-none active:!border-none text-[24px]"/>
                            <button id="quantityDown_quick">
                                <svg width="44" height="44" viewBox="0 0 44 44" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="21.5124" cy="21.5124" r="21.0124" fill="white"
                                            stroke="#4B5259"/>
                                    <rect x="37.4731" y="19.4304" width="4.06503" height="30.8942" rx="2.03252"
                                          transform="rotate(90 37.4731 19.4304)" fill="#4B5259"/>
                                </svg>
                            </button>
                        </div>
                        <div class="lg:pb-3 self-end">
                            <div class="flex items-center justify-center w-full p-2 mt-2 rounded-lg bg-[#D9D9D9]">
                                <div>مقدار به واحد (اصلی):
                                    <span id="quantity_final"><?php echo $package_quantity ?></span>
                                    <span id="main_unit_display"><?php echo $main_unit ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-5 border-[#E9E9E9]"/>
                <div class="grid gap-5 lg:flex ">
                    <div class="border border-[#2F2483] flex items-center rounded-lg flex-0 w-full">
                        <svg class="bg-[#2F2483] p-2 aspect-square h-full max-h-[75px]" viewBox="0 0 24 29" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.19995 28V2.35C1.19995 1.99196 1.34218 1.64858 1.59536 1.39541C1.84853 1.14223 2.19191 1 2.54995 1H21.45C21.808 1 22.1514 1.14223 22.4045 1.39541C22.6577 1.64858 22.8 1.99196 22.8 2.35V28L12 22.4157L1.19995 28Z"
                                  stroke="white" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M6.59998 10.45H17.4" stroke="white" stroke-width="2" stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>
                        <div class="flex justify-center text-[20px] font-[700] text-[#4B5259] w-full"
                             id="total_price"><?php echo !empty($precentage_price) ? wc_price($precentage_price * $min_quantity) : " " ?></div>
                    </div>
                    <button class="add-to-card w-full bg-[#2f2483] rounded-lg text-white lg:text-[24px] text-base p-4 flex items-center justify-center gap-3 flex-0 <?php echo $variation_stock === 'outofstock' ? 'hidden':'' ?>"
                            value="<?php echo $product_main_id ?>" data-var-id='<?php echo $var_id ?>'>
                        افزودن به سبد خرید
                        <svg class="animate-spin add-to-card-loading h-6 w-6 text-white hidden"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                    <div class="subscription_manager_container <?php echo $variation_stock === 'outofstock' ? 'w-full':'' ?>">
                        <?php
                            if (get_post_type($product_id) === "product_variation"){
                                if ($variation_stock === 'outofstock'){
                                    echo out_of_stock_manager($product_main_id , $var_id);
                                }
                            }
                        ?>
                    </div>
                    <a href="<?php echo $product_url; ?>"
                       class=" w-full lg:w-fit bg-[#2f2483] rounded-lg text-white lg:text-[24px] text-base p-4 flex items-center justify-center gap-3 shrink-0">
                        <svg class="hidden lg:block" xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                             viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M18 14v4.8a1.2 1.2 0 0 1-1.2 1.2H5.2A1.2 1.2 0 0 1 4 18.8V7.2A1.2 1.2 0 0 1 5.2 6h4.6m4.4-2H20v5.8m-7.9 2L20 4.2"/>
                        </svg>
                        <p class="lg:hidden">صفحه محصول</p>
                    </a>
                </div>
            </div>
        </section>
        <?php if ($product->is_type('variable')) {
            ?>
            <script type='text/javascript'>
                jQuery(document).ready(function ($) {
                    function update_variation() {
                        let varData = {
                            'action': 'update_variation_price',
                            'product_id': $("input#product_id").val(),
                        }
                        $("select.variation_selector_quick_view").each(function () {
                            varData[$(this).attr('id')] = $(this).val();
                        });
                        get_var_data(varData, $);

                    }

                    $("select.variation_selector_quick_view").on('change', function () {
                        update_variation();
                    });
                    update_variation();
                });
            </script>
            <?php
        }
    }
    wp_die();
}