<?php get_header();
wp_enqueue_script("single-product-ajax", dedeTemplate . '/ajax/singleProduct/js/singleProductAjax.js', array('jquery'), '1.0', true,);
wp_enqueue_script("magnific-popup-js", dedeTemplate . '/node_modules/@fancyapps/ui/dist/fancybox/fancybox.umd.js', array('jquery'), '1.0', true,);
wp_enqueue_style("magnific-popup-css", dedeTemplate . '/node_modules/@fancyapps/ui/dist/fancybox/fancybox.css');
wp_enqueue_script("carousel-popup-js", dedeTemplate . '/node_modules/@fancyapps/ui/dist/carousel/carousel.umd.js', array('jquery'), '1.0', true,);
wp_enqueue_style("carousel-popup-css", dedeTemplate . '/node_modules/@fancyapps/ui/dist/carousel/carousel.css');
wp_enqueue_script("carousel-thumbs-popup-js", dedeTemplate . '/node_modules/@fancyapps/ui/dist/carousel/carousel.thumbs.umd.js', array('jquery'), '1.0', true,);
wp_enqueue_style("carousel-thumbs-popup-css", dedeTemplate . '/node_modules/@fancyapps/ui/dist/carousel/carousel.thumbs.css');
wp_enqueue_script('persian-date-js', dedeTemplate . '/node_modules/persian-date/dist/persian-date.min.js', array(), 1.0, false);
wp_enqueue_script("edit-cart-items-js", dedeTemplate . '/ajax/EditCartItems/js/js.js', array('jquery'), '1.0', false,);

get_template_part('template/quick_view');
get_template_part('template/EditDetailedCartItems');

function percentage_calcuter($regulator, $sale)
{
    if (!empty($sale)) {
        if ($regulator > $sale) {
            return round(((($regulator - $sale) / $regulator) * 100));
        }
    }
}

?>
<main class="w-full py-5">
    <?php
    if (have_posts()) :
        while (have_posts()) :
            the_post();
            global $product;
            $gallery_images_id = $product->get_gallery_image_ids();
            $title = get_the_title();
            $story_short_code = $product->get_meta('_dede_story_shortcode', true);
            $category = [];
            $primary_cat_id = get_post_meta(get_the_ID(), 'rank_math_primary_product_cat', true);
            if (isset($primary_cat_id) && !empty($primary_cat_id) && intval($primary_cat_id)) {
                $category[] = get_term_by('id', $primary_cat_id, 'product_cat');
            } else {
                $category = get_the_terms(get_the_ID(), 'product_cat');
            }
            $under_title = cmb2_get_option('product_section_page', 'product_under_title');
            $wallet_status = cmb2_get_option('wallet_option', 'wallet_active');
            $wallet_percentage = cmb2_get_option('wallet_option', 'wallet_percentage');
            $wallet_min_checkout = cmb2_get_option('wallet_option', 'wallet_minimum_amount');
            $wallet_max_charge = cmb2_get_option('wallet_option', 'wallet_maximum_charge');
            $min_quantity = $product->get_meta("minimum_quantity", true);
            $max_quantity = $product->get_meta("maximum_quantity", true);
            $package_quantity = $product->get_meta("package_quantity", true);
            $min_quantity = $min_quantity ? $min_quantity : "1";
            $package_quantity = $package_quantity ? $package_quantity : "1";

            function calculatePercentage($total, $percentage)
            {
                return ($percentage / 100) * $total;
            }

            ?>
            <div class="bg-[#F2F2F2] w-full px-1 py-4 md:hidden">
                <div class="container mx-auto">
                    <div class="text-sm">
                        <a href="<?php echo home_url('/') ?>" class="text-[#0058BF]">صفحه اصلی</a>

                        <?php if ($category && !is_wp_error($category)) :
                            foreach ($category as $cat) : ?>
                                /
                                <a href="<?php echo get_category_link($cat->term_id) ?>"
                                   class="text-[#0058BF]"><?php echo $cat->name; ?></a>
                            <?php
                            endforeach;
                        endif;
                        ?>
                        /
                        <a href="<?php the_permalink() ?>" class=""><?php echo the_title() ?></a>
                    </div>
                </div>
            </div>
            <label for="sliderPosition"></label><input type="text" id="sliderPosition" class="hidden">
            <input type="hidden" id="unit_selected">
            <input type="hidden" id="unit_quantity">
            <section class="container grid grid-cols-1 place-content-evenly mx-auto px-5 md:px-0 mt-5 md:mt-0">
                <div class="md:grid md:grid-cols-2 gap-10 w-full">
                    <div class="h-auto" dir="ltr">
                        <div id="custom_single_carousel" class="f-carousel">
                            <div class="f-carousel__slide" data-thumb-src="<?php the_post_thumbnail_url('full') ?>">
                                <a class="absolute top-8 right-8 z-40 first-thumbnail"
                                    <?php echo !wp_is_mobile() ? 'data-fancybox="gallery-p"' : " "; ?>
                                   href="<?php !wp_is_mobile() ? the_post_thumbnail_url('full') : " "; ?>"
                                >
                                    <svg width="42" height="42" viewBox="0 0 42 42" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M38.0625 36.2067L28.1505 26.2947C30.5324 23.4352 31.7202 19.7674 31.4667 16.0544C31.2133 12.3415 29.5381 8.86914 26.7898 6.3598C24.0414 3.85046 20.4313 2.49732 16.7107 2.58186C12.99 2.6664 9.44521 4.18212 6.81363 6.81369C4.18205 9.44527 2.66634 12.9901 2.5818 16.7108C2.49726 20.4314 3.8504 24.0414 6.35974 26.7898C8.86907 29.5382 12.3414 31.2134 16.0544 31.4668C19.7673 31.7203 23.4351 30.5325 26.2946 28.1506L36.2066 38.0626L38.0625 36.2067ZM5.25 17.0626C5.25 14.7263 5.94279 12.4424 7.24076 10.4999C8.53874 8.55733 10.3836 7.04329 12.5421 6.14923C14.7005 5.25517 17.0756 5.02125 19.367 5.47703C21.6584 5.93282 23.7632 7.05785 25.4152 8.70986C27.0672 10.3619 28.1922 12.4667 28.648 14.7581C29.1038 17.0495 28.8699 19.4246 27.9758 21.583C27.0818 23.7415 25.5677 25.5863 23.6252 26.8843C21.6826 28.1823 19.3988 28.8751 17.0625 28.8751C13.9307 28.8716 10.9282 27.6259 8.71364 25.4114C6.49912 23.1969 5.25347 20.1944 5.25 17.0626Z"
                                              fill="#525252"/>
                                    </svg>
                                </a>
                                <img src="<?php the_post_thumbnail_url('full') ?>"
                                    <?php echo wp_is_mobile() ? "data-fancybox='gallery-p'" : " "; ?>
                                     class="first-thumbnail rounded-lg w-full h-full aspect-square"
                                     alt="<?php echo $title; ?>">
                            </div>
                            <?php
                            if (isset($gallery_images_id)) {
                                foreach ($gallery_images_id as $img_id):
                                    $image_url = wp_get_attachment_image_url($img_id, 'full')
                                    ?>
                                    <div class="f-carousel__slide" data-thumb-src="<?php echo $image_url; ?>">
                                        <a class="absolute top-8 right-8 z-40"
                                            <?php echo !wp_is_mobile() ? 'data-fancybox="gallery-p" href="' . $image_url . '"' : " "; ?> >
                                            <svg width="42" height="42" viewBox="0 0 42 42" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path d="M38.0625 36.2067L28.1505 26.2947C30.5324 23.4352 31.7202 19.7674 31.4667 16.0544C31.2133 12.3415 29.5381 8.86914 26.7898 6.3598C24.0414 3.85046 20.4313 2.49732 16.7107 2.58186C12.99 2.6664 9.44521 4.18212 6.81363 6.81369C4.18205 9.44527 2.66634 12.9901 2.5818 16.7108C2.49726 20.4314 3.8504 24.0414 6.35974 26.7898C8.86907 29.5382 12.3414 31.2134 16.0544 31.4668C19.7673 31.7203 23.4351 30.5325 26.2946 28.1506L36.2066 38.0626L38.0625 36.2067ZM5.25 17.0626C5.25 14.7263 5.94279 12.4424 7.24076 10.4999C8.53874 8.55733 10.3836 7.04329 12.5421 6.14923C14.7005 5.25517 17.0756 5.02125 19.367 5.47703C21.6584 5.93282 23.7632 7.05785 25.4152 8.70986C27.0672 10.3619 28.1922 12.4667 28.648 14.7581C29.1038 17.0495 28.8699 19.4246 27.9758 21.583C27.0818 23.7415 25.5677 25.5863 23.6252 26.8843C21.6826 28.1823 19.3988 28.8751 17.0625 28.8751C13.9307 28.8716 10.9282 27.6259 8.71364 25.4114C6.49912 23.1969 5.25347 20.1944 5.25 17.0626Z"
                                                      fill="#525252"/>
                                            </svg>
                                        </a>
                                        <img src="<?php echo $image_url; ?>"
                                             class="rounded-lg w-full h-full aspect-square"
                                            <?php echo wp_is_mobile() ? "data-fancybox='gallery-p'" : " "; ?>
                                             alt="<?php echo $title; ?>">
                                    </div>
                                <?php endforeach;
                            } ?>
                        </div>
                    </div>
                    <div class="grow">
                        <div class="bg-[#F2F2F2] rounded-full p-1 hidden md:block">
                            <div class="justify-self-end ">
                                <div class="flex gap-2 text-sm">
                                    <a href="<?php echo home_url('/') ?>" class="text-[#0058BF]">صفحه اصلی</a>

                                    <?php if ($category && !is_wp_error($category)) :
                                        foreach ($category as $cat) : ?>
                                            /
                                            <a href="<?php echo get_category_link($cat->term_id) ?>"
                                               class="text-[#0058BF]"><?php echo $cat->name; ?></a>
                                        <?php
                                        endforeach;
                                    endif;
                                    ?>
                                    /
                                    <a href="<?php the_permalink() ?>" class=""><?php echo the_title() ?></a>
                                </div>
                            </div>
                        </div>
                        <h1 class="mt-3 font-[700] text-[20px] pt-5">
                            <?php the_title() ?>
                        </h1>
                        <div id="" class=" [&_ul]:list-disc [&_ul]:mt-2 marker:[&_li]:text-[#E3000F]  pr-3">
                            <?php the_excerpt(); ?>
                        </div>
                        <hr class="my-5 border-[#E9E9E9]"/>
                        <input type="hidden" value="<?php echo get_the_ID() ?>" name="product_id" id="product_id">
                        <?php
                        if ($product->is_type('variable')) {
                            $default_attributes = $product->get_default_attributes();
                            $attributes = $product->get_variation_attributes();
                            foreach ($attributes as $attribute_name => $options) {
                                echo '<label for="' . esc_attr(sanitize_title($attribute_name)) . '">' . esc_html(wc_attribute_label($attribute_name)) . '</label>';
                                echo '<select id="attribute_' . esc_attr(sanitize_title($attribute_name)) . '" name="' . esc_attr(sanitize_title($attribute_name)) . '" class="w-full p-3 mt-2 border-[1px] border-black bg-white rounded-lg mb-5 variation_selector">';
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
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="flex items-center rounded-lg bg-[#D9D9D9]">
                                <span class="rounded-r-lg p-2 bg-[#2F2483]">
                                    <svg class="w-8 h-8" viewBox="0 0 32 33" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 9.625V6.875C4 6.14565 4.28095 5.44618 4.78105 4.93046C5.28115 4.41473 5.95942 4.125 6.66667 4.125H9.33333M22.6667 4.125H25.3333C26.0406 4.125 26.7189 4.41473 27.219 4.93046C27.719 5.44618 28 6.14565 28 6.875V9.625M28 23.375V26.125C28 26.8543 27.719 27.5538 27.219 28.0695C26.7189 28.5853 26.0406 28.875 25.3333 28.875H22.6667M9.33333 28.875H6.66667C5.95942 28.875 5.28115 28.5853 4.78105 28.0695C4.28095 27.5538 4 26.8543 4 26.125V23.375M10.6667 9.625V23.375M16 9.625V23.375M22.6667 9.625V23.375"
                                              stroke="white" stroke-width="2" stroke-linecap="round"
                                              stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <p class="w-full text-sm text-center">کد کالا:&nbsp;<span
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
                                    فی:&nbsp;
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
                                            echo '<p class="text-sm price_final">&nbsp;' . wc_price($product->get_regular_price()) . '</p>';
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="text-[#4B5259] md:col-span-2">
                                <div class="marker:[&_ul]:text-[#E3000F] [&_ul]:list-disc [&_ul]:list-inside [&_ul]:px-3"
                                     id="description"></div>
                                <ul class="marker:text-[#E3000F] list-disc list-inside px-3" id="units_list">
                                </ul>
                            </div>
                            <div class="flex flex-col self-center gap-3 pr-5" id="unit_blocks"></div>
                            <div class="grid grid-cols-1 mt-10 md:mt-0 md:pr-14 gap-2 md:gap-0">
                                <div class="flex w-full h-full md:gap-2 float-left items-center self-start">
                                    <button id="quantityUp">
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
                                    <input type="number" value="<?php echo $min_quantity ?>" id="quantity"
                                           step="<?php echo $package_quantity ?>"
                                           data-pakage-quantity="<?php echo $package_quantity ?>"
                                           data-min-quantity="<?php echo $min_quantity ?>"
                                           data-max-quantity="<?php echo $max_quantity ?>"
                                           class="border-none md:py-3 w-full text-center focus:outline-none active:!border-none text-[24px]"/>
                                    <button id="quantityDown">
                                        <svg width="44" height="44" viewBox="0 0 44 44" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="21.5124" cy="21.5124" r="21.0124" fill="white"
                                                    stroke="#4B5259"/>
                                            <rect x="37.4731" y="19.4304" width="4.06503" height="30.8942" rx="2.03252"
                                                  transform="rotate(90 37.4731 19.4304)" fill="#4B5259"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="md:pb-3 self-end">
                                    <div class="flex items-center justify-center w-full p-2 mt-2 rounded-lg bg-[#D9D9D9]">
                                        <div>مقدار به واحد (اصلی):&nbsp;<span id="quantity_final"></span>&nbsp;<span
                                                    id="main_unit_display"></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="my-5 border-[#E9E9E9]"/>
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div class="border border-[#2F2483] flex items-center rounded-lg">
                                <svg class="bg-[#2F2483] p-2 h-14 w-14 rounded-r" viewBox="0 0 24 29" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.19995 28V2.35C1.19995 1.99196 1.34218 1.64858 1.59536 1.39541C1.84853 1.14223 2.19191 1 2.54995 1H21.45C21.808 1 22.1514 1.14223 22.4045 1.39541C22.6577 1.64858 22.8 1.99196 22.8 2.35V28L12 22.4157L1.19995 28Z"
                                          stroke="white" stroke-width="2" stroke-linejoin="round"/>
                                    <path d="M6.59998 10.45H17.4" stroke="white" stroke-width="2" stroke-linecap="round"
                                          stroke-linejoin="round"/>
                                </svg>
                                <div class="flex justify-center text-[20px] font-[700] text-[#4B5259] w-full"
                                     id="total_price"><?php echo !empty($precentage_price) ? wc_price($precentage_price * $min_quantity) : " " ?></div>
                            </div>
                            <button class="add-to-card w-full bg-[#2f2483] rounded-lg text-white md:text-[24px] text-base p-4 flex items-center justify-center gap-3"
                                    value="<?php echo $product->get_id() ?>">
                                افزودن به سبد خرید
                                <svg class="animate-spin add-to-card-loading h-6 w-6 text-white hidden"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                            <div class="subscription_manager_container"></div>
                        </div>
                    </div>
                </div>
                <?php if (!empty($story_short_code)): ?>
                    <section id="story-section" class=" mt-14">
                        <div class="container relative text-center flex items-center justify-center mx-auto">
                            <hr class="border-[0.5px] border-[#525252]/50 w-full mx-auto my-auto"/>
                            <p class="font-bold text-[#525252] text-2xl mt-0 mb-auto absolute -top-5 bg-white px-10">
                                ویدیو ها</p>
                        </div>
                        <div class="py-5">
                            <?php echo do_shortcode($story_short_code) ?>
                        </div>
                    </section>
                <?php endif; ?>
                <div id="product-content">
                    <div class="container mt-12 relative text-center flex items-center justify-center mx-auto">
                        <hr class="border-[0.5px] border-[#525252]/50 w-full mx-auto my-auto"/>
                        <p class="font-bold text-[#525252] text-2xl mt-0 mb-auto absolute -top-5 bg-white px-10">مشخصات
                            محصول</p>
                    </div>
                    <div class="my-10 w-full text-justify prose prose-a:text-[#0058BF] prose-a:no-underline prose-ul:list-inside prose-ol:list-inside !max-w-full break-words [&_img]:mx-auto">
                        <?php the_content(); ?>
                    </div>
                    <div class="container mt-12 relative text-center flex items-center justify-center mx-auto">
                        <hr class="border-[0.5px] border-[#525252]/50 w-full mx-auto my-auto"/>
                        <p class="font-bold text-[#525252] text-2xl mt-0 mb-auto absolute -top-5 bg-white px-10">محصولات
                            مرتبط</p>
                    </div>
                    <div class="container mx-auto">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pt-10 mx-3 md:px-0">
                            <?php
                            $related_products = wc_get_related_products(get_the_ID(), 4);
                            if ($related_products) {
                                foreach ($related_products as $prod) {
                                    $get_product = wc_get_product($prod);
                                    $product_title = get_the_title($prod);
                                    $product_permalink = get_permalink($prod);
                                    $thumbnail_url = get_the_post_thumbnail_url($prod);
                                    if ($get_product->is_type('variable')) {
                                        $default_attributes = $get_product->get_default_attributes();
                                        $variations = $get_product->get_available_variations();
                                        foreach ($variations as $variation) {
                                            $match = true;
                                            foreach ($default_attributes as $key => $val) {
                                                if ($variation['attributes']['attribute_' . $key] !== $val) {
                                                    $match = false;
                                                    break;
                                                }
                                            }
                                            if ($match) {
                                                $var = wc_get_product($variation['variation_id']);
                                                $variation_prices_regular_2 = $var->get_regular_price();
                                                $variation_prices_sale_2 = $var->get_sale_price();
                                                $discount_percentage = percentage_calcuter($variation_prices_regular_2, $variation_prices_sale_2);
                                            }
                                        }
                                    }
                                    ?>
                                    <div class="flex flex-col relative items-center shadow-lg font-bold text-center w-full h-full">
                                        <img class="rounded-t-lg" src="<?php echo $thumbnail_url; ?>"
                                             alt="<?php echo $product_title; ?>">
                                        <h3 class="text-[#525252] grow text-center py-2 my-auto grid items-center"><?php echo $product_title; ?></h3>
                                        <div class="flex gap-5 text-sm justify-center items-center pb-2">
                                            <?php
                                            if ($product->is_type('variable')) {
                                                if (!empty($variation_prices_regular_2)) {
                                                    if (isset($discount_percentage)) {
                                                        echo '<span class="absolute top-5 left-5 text-white flex"><svg width="67" height="44" viewBox="0 0 67 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M16.9387 2.17843C17.5524 1.49303 18.3038 0.944748 19.1437 0.569382C19.9837 0.194015 20.8933 0 21.8133 0C22.7333 0 23.643 0.194015 24.4829 0.569382C25.3229 0.944748 26.0742 1.49303 26.6879 2.17843L28.2147 3.884C28.4328 4.12807 28.7032 4.3199 29.0056 4.44525C29.308 4.5706 29.6348 4.62626 29.9617 4.6081L32.2518 4.4816C33.1701 4.43117 34.0887 4.5749 34.9478 4.90342C35.8068 5.23193 36.5869 5.73786 37.2372 6.38819C37.8876 7.03852 38.3935 7.81865 38.722 8.67768C39.0505 9.53671 39.1943 10.4554 39.1438 11.3737L39.0173 13.6638C38.9995 13.9903 39.0553 14.3166 39.1807 14.6186C39.306 14.9207 39.4977 15.1906 39.7414 15.4086L41.4492 16.9353C42.1349 17.549 42.6835 18.3005 43.0591 19.1407C43.4347 19.9808 43.6288 20.8907 43.6288 21.811C43.6288 22.7313 43.4347 23.6412 43.0591 24.4814C42.6835 25.3215 42.1349 26.073 41.4492 26.6867L39.7414 28.2134C39.4974 28.4316 39.3055 28.702 39.1802 29.0044C39.0548 29.3068 38.9992 29.6336 39.0173 29.9605L39.1438 32.2505C39.1943 33.1689 39.0505 34.0875 38.722 34.9465C38.3935 35.8056 37.8876 36.5857 37.2372 37.236C36.5869 37.8864 35.8068 38.3923 34.9478 38.7208C34.0887 39.0493 33.1701 39.193 32.2518 39.1426L29.9617 39.0161C29.6352 38.9983 29.3088 39.0541 29.0068 39.1794C28.7048 39.3048 28.4348 39.4964 28.2168 39.7402L26.6901 41.448C26.0764 42.1337 25.3249 42.6823 24.4848 43.0579C23.6446 43.4335 22.7347 43.6276 21.8144 43.6276C20.8941 43.6276 19.9842 43.4335 19.1441 43.0579C18.3039 42.6823 17.5524 42.1337 16.9387 41.448L15.412 39.7402C15.1938 39.4961 14.9235 39.3043 14.621 39.179C14.3186 39.0536 13.9919 38.998 13.665 39.0161L11.3749 39.1426C10.4566 39.193 9.53793 39.0493 8.6789 38.7208C7.81986 38.3923 7.03974 37.8864 6.38941 37.236C5.73908 36.5857 5.23315 35.8056 4.90463 34.9465C4.57612 34.0875 4.43239 33.1689 4.48282 32.2505L4.60932 29.9605C4.62715 29.6339 4.57132 29.3076 4.44598 29.0056C4.32064 28.7035 4.12899 28.4336 3.88522 28.2156L2.17965 26.6889C1.49389 26.0752 0.945299 25.3237 0.569715 24.4836C0.194131 23.6434 0 22.7335 0 21.8132C0 20.8929 0.194131 19.983 0.569715 19.1428C0.945299 18.3027 1.49389 17.5512 2.17965 16.9375L3.88522 15.4108C4.12929 15.1926 4.32112 14.9223 4.44647 14.6198C4.57182 14.3174 4.62748 13.9906 4.60932 13.6638L4.48282 11.3737C4.43239 10.4554 4.57612 9.53671 4.90463 8.67768C5.23315 7.81865 5.73908 7.03852 6.38941 6.38819C7.03974 5.73786 7.81986 5.23193 8.6789 4.90342C9.53793 4.5749 10.4566 4.43117 11.3749 4.4816L13.665 4.6081C13.9915 4.62593 14.3178 4.5701 14.6199 4.44476C14.9219 4.31942 15.1919 4.12778 15.4098 3.884L16.9365 2.17843H16.9387ZM29.8984 13.727C30.3073 14.136 30.537 14.6907 30.537 15.269C30.537 15.8473 30.3073 16.402 29.8984 16.811L16.8122 29.8972C16.4009 30.2945 15.8499 30.5143 15.2781 30.5094C14.7062 30.5044 14.1592 30.275 13.7548 29.8706C13.3504 29.4663 13.121 28.9192 13.1161 28.3474C13.1111 27.7755 13.3309 27.2246 13.7282 26.8132L26.8144 13.727C27.2234 13.3181 27.7781 13.0884 28.3564 13.0884C28.9348 13.0884 29.4894 13.3181 29.8984 13.727ZM16.3607 13.088C15.4931 13.088 14.6609 13.4326 14.0474 14.0462C13.4339 14.6597 13.0892 15.4918 13.0892 16.3595V16.3813C13.0892 17.249 13.4339 18.0811 14.0474 18.6947C14.6609 19.3082 15.4931 19.6529 16.3607 19.6529H16.3825C17.2502 19.6529 18.0823 19.3082 18.6959 18.6947C19.3094 18.0811 19.6541 17.249 19.6541 16.3813V16.3595C19.6541 15.4918 19.3094 14.6597 18.6959 14.0462C18.0823 13.4326 17.2502 13.088 16.3825 13.088H16.3607ZM27.2659 23.9931C26.3982 23.9931 25.5661 24.3378 24.9526 24.9514C24.339 25.5649 23.9944 26.397 23.9944 27.2647V27.2865C23.9944 28.1542 24.339 28.9863 24.9526 29.5998C25.5661 30.2134 26.3982 30.5581 27.2659 30.5581H27.2877C28.1554 30.5581 28.9875 30.2134 29.6011 29.5998C30.2146 28.9863 30.5593 28.1542 30.5593 27.2865V27.2647C30.5593 26.397 30.2146 25.5649 29.6011 24.9514C28.9875 24.3378 28.1554 23.9931 27.2877 23.9931H27.2659Z" fill="#E3000F"/>
                                        <rect x="31.7241" y="7.93115" width="34.8966" height="26.9655" rx="3" fill="#E3000F"/>
                                        </svg><p class="absolute text-2xl top-[6px] right-1">' . intval($discount_percentage) . '</p></span>';
                                                        echo '<p class="grow text-[#E3000F] line-through text-xs">' . wc_price($variation_prices_regular_2) . '</p>';
                                                        echo '<p class="grow text-[#008826] ">' . wc_price($variation_prices_sale_2) . '</p>';
                                                    } else {
                                                        echo '<p class="grow text-[#008826] ">' . wc_price($variation_prices_regular_2) . '</p>';
                                                    }
                                                } else {
                                                    echo '<p class="text-[#008826]">بدون قیمت</p>';
                                                }
                                            } else {
                                                if (!empty($product->get_sale_price())) {
                                                    echo '<span class="absolute top-5 left-5 text-white flex"><svg width="67" height="44" viewBox="0 0 67 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M16.9387 2.17843C17.5524 1.49303 18.3038 0.944748 19.1437 0.569382C19.9837 0.194015 20.8933 0 21.8133 0C22.7333 0 23.643 0.194015 24.4829 0.569382C25.3229 0.944748 26.0742 1.49303 26.6879 2.17843L28.2147 3.884C28.4328 4.12807 28.7032 4.3199 29.0056 4.44525C29.308 4.5706 29.6348 4.62626 29.9617 4.6081L32.2518 4.4816C33.1701 4.43117 34.0887 4.5749 34.9478 4.90342C35.8068 5.23193 36.5869 5.73786 37.2372 6.38819C37.8876 7.03852 38.3935 7.81865 38.722 8.67768C39.0505 9.53671 39.1943 10.4554 39.1438 11.3737L39.0173 13.6638C38.9995 13.9903 39.0553 14.3166 39.1807 14.6186C39.306 14.9207 39.4977 15.1906 39.7414 15.4086L41.4492 16.9353C42.1349 17.549 42.6835 18.3005 43.0591 19.1407C43.4347 19.9808 43.6288 20.8907 43.6288 21.811C43.6288 22.7313 43.4347 23.6412 43.0591 24.4814C42.6835 25.3215 42.1349 26.073 41.4492 26.6867L39.7414 28.2134C39.4974 28.4316 39.3055 28.702 39.1802 29.0044C39.0548 29.3068 38.9992 29.6336 39.0173 29.9605L39.1438 32.2505C39.1943 33.1689 39.0505 34.0875 38.722 34.9465C38.3935 35.8056 37.8876 36.5857 37.2372 37.236C36.5869 37.8864 35.8068 38.3923 34.9478 38.7208C34.0887 39.0493 33.1701 39.193 32.2518 39.1426L29.9617 39.0161C29.6352 38.9983 29.3088 39.0541 29.0068 39.1794C28.7048 39.3048 28.4348 39.4964 28.2168 39.7402L26.6901 41.448C26.0764 42.1337 25.3249 42.6823 24.4848 43.0579C23.6446 43.4335 22.7347 43.6276 21.8144 43.6276C20.8941 43.6276 19.9842 43.4335 19.1441 43.0579C18.3039 42.6823 17.5524 42.1337 16.9387 41.448L15.412 39.7402C15.1938 39.4961 14.9235 39.3043 14.621 39.179C14.3186 39.0536 13.9919 38.998 13.665 39.0161L11.3749 39.1426C10.4566 39.193 9.53793 39.0493 8.6789 38.7208C7.81986 38.3923 7.03974 37.8864 6.38941 37.236C5.73908 36.5857 5.23315 35.8056 4.90463 34.9465C4.57612 34.0875 4.43239 33.1689 4.48282 32.2505L4.60932 29.9605C4.62715 29.6339 4.57132 29.3076 4.44598 29.0056C4.32064 28.7035 4.12899 28.4336 3.88522 28.2156L2.17965 26.6889C1.49389 26.0752 0.945299 25.3237 0.569715 24.4836C0.194131 23.6434 0 22.7335 0 21.8132C0 20.8929 0.194131 19.983 0.569715 19.1428C0.945299 18.3027 1.49389 17.5512 2.17965 16.9375L3.88522 15.4108C4.12929 15.1926 4.32112 14.9223 4.44647 14.6198C4.57182 14.3174 4.62748 13.9906 4.60932 13.6638L4.48282 11.3737C4.43239 10.4554 4.57612 9.53671 4.90463 8.67768C5.23315 7.81865 5.73908 7.03852 6.38941 6.38819C7.03974 5.73786 7.81986 5.23193 8.6789 4.90342C9.53793 4.5749 10.4566 4.43117 11.3749 4.4816L13.665 4.6081C13.9915 4.62593 14.3178 4.5701 14.6199 4.44476C14.9219 4.31942 15.1919 4.12778 15.4098 3.884L16.9365 2.17843H16.9387ZM29.8984 13.727C30.3073 14.136 30.537 14.6907 30.537 15.269C30.537 15.8473 30.3073 16.402 29.8984 16.811L16.8122 29.8972C16.4009 30.2945 15.8499 30.5143 15.2781 30.5094C14.7062 30.5044 14.1592 30.275 13.7548 29.8706C13.3504 29.4663 13.121 28.9192 13.1161 28.3474C13.1111 27.7755 13.3309 27.2246 13.7282 26.8132L26.8144 13.727C27.2234 13.3181 27.7781 13.0884 28.3564 13.0884C28.9348 13.0884 29.4894 13.3181 29.8984 13.727ZM16.3607 13.088C15.4931 13.088 14.6609 13.4326 14.0474 14.0462C13.4339 14.6597 13.0892 15.4918 13.0892 16.3595V16.3813C13.0892 17.249 13.4339 18.0811 14.0474 18.6947C14.6609 19.3082 15.4931 19.6529 16.3607 19.6529H16.3825C17.2502 19.6529 18.0823 19.3082 18.6959 18.6947C19.3094 18.0811 19.6541 17.249 19.6541 16.3813V16.3595C19.6541 15.4918 19.3094 14.6597 18.6959 14.0462C18.0823 13.4326 17.2502 13.088 16.3825 13.088H16.3607ZM27.2659 23.9931C26.3982 23.9931 25.5661 24.3378 24.9526 24.9514C24.339 25.5649 23.9944 26.397 23.9944 27.2647V27.2865C23.9944 28.1542 24.339 28.9863 24.9526 29.5998C25.5661 30.2134 26.3982 30.5581 27.2659 30.5581H27.2877C28.1554 30.5581 28.9875 30.2134 29.6011 29.5998C30.2146 28.9863 30.5593 28.1542 30.5593 27.2865V27.2647C30.5593 26.397 30.2146 25.5649 29.6011 24.9514C28.9875 24.3378 28.1554 23.9931 27.2877 23.9931H27.2659Z" fill="#E3000F"/>
                                        <rect x="31.7241" y="7.93115" width="34.8966" height="26.9655" rx="3" fill="#E3000F"/>
                                        </svg><p class="absolute text-2xl top-[6px] right-1">' . intval(percentage_calcuter($product->get_regular_price(), $product->get_sale_price())) . '</p></span>';
                                                    echo '<p class="text-[#E3000F] line-through text-xs">' . wc_price($product->get_regular_price()) . '</p>';
                                                    echo '<p class="text-[#008826]">' . wc_price($product->get_sale_price()) . '</p>';
                                                } elseif (!empty($product->get_regular_price())) {
                                                    echo '<p class="text-[#008826] ">' . wc_price($product->get_regular_price()) . '</p>';
                                                } else {
                                                    echo '<p class="text-[#008826]">بدون قیمت</p>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <div class="w-full grid grid-cols-2 content-center bg-[#2F2483] text-white py-3 rounded-b-lg text-sm">
                                            <button class="border-l-[2px] border-white text-center quick_post_view"
                                                    value="<?php echo $get_product->get_id(); ?>">
                                                مشاهده سریع
                                            </button>
                                            <a href="<?php echo $product_permalink; ?>">
                                                اطلاعات بیشتر
                                            </a>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } ?>
                        </div>
                    </div>
                    <?php
                    $approved_comments = [];
                    $post_children = array_values($product->get_children());
                    $post_children [] = get_the_ID();
                    if ($product->has_child()) {
                        foreach ($post_children as $ids) {
                            $comments_args = array(
                                'post_type' => array('product_variation'),
                                'post_id' => $ids,
                                'status' => 'approve',
                            );
                            $approved_comments [] = get_comments($comments_args);
                        }
                    } else {
                        $comments_args = array(
                            'post_type' => array('product'),
                            'post_id' => $product->get_id(),
                            'status' => 'approve',
                        );
                        $approved_comments [] = get_comments($comments_args);
                    }
                    $comment_content = '';
                    $comment_rating_stars = [];
                    $images = [];
                    $videos = [];
                    $voteUp = dedeTemplate . '/assets/image/voteUp.png';
                    $voteDown = dedeTemplate . '/assets/image/voteDown.png';
                    if (!empty(array_filter($approved_comments))) {
                    ?>
                    <div class="container mt-12 relative text-center flex items-center justify-center mx-auto">
                        <hr class="border-[0.5px] border-[#525252]/50 w-full mx-auto my-auto"/>
                        <p class="font-bold text-[#525252] text-2xl mt-0 mb-auto absolute -top-5 bg-white px-5 px-10">
                            امتیاز
                            و دیدگاه کاربران</p>
                    </div>
                    <?php
                    $comment_counter = 0;
                    foreach ($approved_comments as $comments_array) {
                        foreach ($comments_array as $comment) {
                            $comment_date = date('Y/m/d', strtotime($comment->comment_date));
                            $getVoteUp = get_comment_meta($comment->comment_ID, "_dede_comment_vote_up_rate_", true);
                            $getVoteDown = get_comment_meta($comment->comment_ID, "_dede_comment_vote_down_rate_", true);
                            $voteUpRate = !empty($getVoteUp) ? $getVoteUp : 0;
                            $voteDownRate = !empty($getVoteDown) ? $getVoteDown : 0;
                            $stars = get_comment_meta($comment->comment_ID, "_dede_comment_rating_", true);
                            $getImage = get_comment_meta($comment->comment_ID, "_dede_comment_image_", true);
                            $getVideo = get_comment_meta($comment->comment_ID, "_dede_comment_video_", true);
                            $images = !empty($getImage) ? array_merge($images, $getImage) : $images;
                            $videos = !empty($getVideo) ? array_merge($videos, $getVideo) : $videos;
                            $comment_rating_stars [] = $stars;
                            if ($comment_counter >= 3) {
                                $comment_content .= "<div class='w-full border-b border-[#4B5259] py-5 relative hidden comment' data-date='$comment_date' data-vote-up='$voteUpRate'>";
                            } else {
                                $comment_content .= "<div class='w-full border-b border-[#4B5259] py-5 relative comment' data-date='$comment_date' data-vote-up='$voteUpRate'>";
                            }
                            $comment_content .= '<div class="w-1/2 text-[14px] font-[700] text-[#4B5259] flex gap-2">';
                            $comment_content .= "<p> $comment->comment_author</p>";
                            $comment_content .= "<div class='flex' data-type-stars='$stars'>";
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $stars) {
                                    $comment_content .= '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M7.94118 0L9.72408 5.79206H15.4937L10.826 9.37176L12.6089 15.1638L7.94118 11.5841L3.27347 15.1638L5.05638 9.37176L0.388669 5.79206H6.15827L7.94118 0Z" fill="#E3000F"/> </svg> ';
                                } else {
                                    $comment_content .= '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M8.05882 0L9.84173 5.79206H15.6113L10.9436 9.37176L12.7265 15.1638L8.05882 11.5841L3.39112 15.1638L5.17402 9.37176L0.506314 5.79206H6.27592L8.05882 0Z" fill="#D9D9D9"/></svg>';
                                }
                            }
                            $comment_content .= "</div>";
                            $comment_content .= "<div class='comment_date'>$comment_date</div>";
                            $comment_content .= "</div>";
                            $comment_content .= "<div class='my-3 text-[15px] font-[500] text-[#525252] text-justify prose prose-a:text-[#0058BF] prose-a:no-underline prose-ul:list-inside prose-ol:list-inside !max-w-full break-words'>$comment->comment_content</div>";
                            $comment_content .= "<div class= 'absolute bg-white px-2 -bottom-3 left-0 z-30 flex gap-2 items-center text-[14px] font-[700] text-[#4B5259] p-1'>آیا این دیدگاه مفید بود ؟ <button class='voteDown flex gap-1 items-center' value='$comment->comment_ID'><img alt='مخالف' class='w-[20px] h-[20px]' src='$voteDown' /> <span class='vote_down_rate'>$voteDownRate</span></button><button class='voteUp flex gap-1 items-center' value='$comment->comment_ID'><img class='w-[20px] h-[20px]' alt='موافق' src='$voteUp'/> <span class='vote_up_rate'>$voteUpRate</span></button></div>";
                            $comment_content .= "</div>";
                            $comment_counter++;
                        }
                    }
                    function get_comment_stars_average($numbers)
                    {
                        $sum = array_sum($numbers);
                        $count = count($numbers);
                        $average = $sum / $count;

                        return intval($average);
                    } ?>
                    <div class="w-full md:flex py-5 gap-5">
                        <div class="md:w-4/12 w-full ">
                            <div class="w-full flex gap-5 items-center justify-center md:p-2">
                                <div> امتیاز : <?php echo get_comment_stars_average($comment_rating_stars); ?> از 5
                                </div>
                                <div class="flex">
                                    <?php for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= get_comment_stars_average($comment_rating_stars)) {
                                            echo '<svg class="md:w-[37px] md:h-[35px] w-[28px] h-[30px]" viewBox="0 0 37 35" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M18.5 0L22.6535 13.1287H36.0945L25.2205 21.2426L29.374 34.3713L18.5 26.2574L7.62597 34.3713L11.7795 21.2426L0.905455 13.1287H14.3465L18.5 0Z" fill="#E3000F"/> </svg> ';
                                        } else {
                                            echo '<svg class="md:w-[37px] md:h-[35px] w-[28px] h-[30px]"  viewBox="0 0 36 35" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M18 0L22.0413 13.1287H35.119L24.5389 21.2426L28.5801 34.3713L18 26.2574L7.41987 34.3713L11.4611 21.2426L0.880983 13.1287H13.9587L18 0Z" fill="#D9D9D9"/> </svg> ';
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="text-[14px] font-[700]">از مجموع <?php echo wp_count_comments()->approved ?>
                                    نظر
                                </div>
                            </div>
                            <div class=" rounded-lg md:border border-[#4B5259] p-2 pb-10 mt-8 relative">
                                <span class="absolute -top-3 bg-white text-sm right-2 px-2">ویدیو و تصاویر منتخب کاربران</span>
                                <div class="grid grid-cols-3 gap-3 md:p-4 h-96 content-start overflow-hidden">
                                    <?php
                                    if (!empty($images)) {
                                        foreach ($images as $image) {
                                            $image_url = wp_get_attachment_url($image);
                                            echo '<img data-fancybox="gallery" class="rounded-lg object-fill w-full" src="' . $image_url . '" alt="' . $product_title . '" />';
                                        }
                                    }
                                    if (!empty($videos)) {
                                        foreach ($videos as $video) {
                                            $video_url = wp_get_attachment_url($video);
                                            $video_thumbnail = wp_get_attachment_image_src($video);
                                            echo '<a class="relative" data-fancybox="gallery" href="' . $video_url . '"><video class="hidden comment-videos" src="' . $video_url . '" poster=" " ><img alt="' . $product_title . '" /></video><img alt="' . $product_title . '" class="rounded-lg object-fill h-full min-h-[100px]" /><svg class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2" width="48" height="49" viewBox="0 0 48 49" fill="none" xmlns="http://www.w3.org/2000/svg"> <circle cx="24.3364" cy="24.4812" r="23.6091" fill="#D9D9D9"/><path d="M36.7 21.8837C38.7 23.0384 38.7 25.9252 36.7 27.0799L20.4046 36.4881C18.4046 37.6428 15.9046 36.1994 15.9046 33.89L15.9046 15.0736C15.9046 12.7642 18.4046 11.3208 20.4046 12.4755L36.7 21.8837Z" fill="#E3000F"/></svg> </a>';
                                        }
                                    } ?>
                                    <a class="absolute left-1/2 transform -translate-x-1/2 bottom-2 col-span-3 text-[#0058BF] bottom-1.5"
                                       data-fancybox="gallery"
                                       href="<?php echo !empty($image_url) ? $image_url : ''; ?>">مشاهده
                                        همه</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty(array_filter($approved_comments))) : ?>
                        <div class="md:w-8/12 w-full">
                            <div class="w-full relative pb-10">
                                <div class="rounded-lg bg-[#F2F2F2] relative flex items-center text-[#525252] gap-1 md:gap-5">
                                    <div class="flex">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11 15L20 15L20 13L11 13L11 15ZM11 11L18 11L18 9L11 9L11 11ZM11 19L22 19L22 17L11 17L11 19ZM11 7L16 7L16 5L11 5L11 7ZM5 4L7 4L7 16L10 16L6 20L2 16L5 16L5 4Z"
                                                  fill="#525252"/>
                                        </svg>
                                        مرتب سازی :
                                    </div>
                                    <button class="p-2 md:p-3 rounded-lg hover:bg-[#E9E9E9] active:bg-[#E9E9E9] focus:bg-[#E9E9E9]"
                                            date-sorter-type="newest">جدید
                                        ترین
                                    </button>
                                    <button class="p-2 md:p-3 rounded-lg hover:bg-[#E9E9E9] active:bg-[#E9E9E9] focus:bg-[#E9E9E9]"
                                            date-sorter-type="oldest">قدیمی
                                        ترین
                                    </button>
                                    <button class="p-2 md:p-3 rounded-lg hover:bg-[#E9E9E9] active:bg-[#E9E9E9] focus:bg-[#E9E9E9]"
                                            date-sorter-type="valuable">مفید
                                        ترین
                                    </button>
                                    <span class="absolute left-4 hidden md:block"> <?php echo wp_count_comments($product->get_id())->approved ?>  دیدگاه</span>
                                </div>
                                <div class="w-full mt-2 flex flex-col gap-5 comments-container">
                                    <?php echo $comment_content ?>
                                </div>
                                <?php if (wp_count_comments($product->get_id())->approved > 3): ?>
                                    <button id="more_comment"
                                            class="absolute bottom-0 left-1/2 transform -translate-x-1/2 text-[#0058BF]  font-[500] text-[15px] flex gap-2">
                                        <?php echo wp_count_comments($product->get_id())->approved - 3 ?> دیدگاه دیگر
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2.045 6.61656C2.27941 6.38222 2.59729 6.25057 2.92875 6.25057C3.2602 6.25057 3.57809 6.38222 3.8125 6.61656L10 12.8041L16.1875 6.61656C16.4233 6.38886 16.739 6.26287 17.0668 6.26571C17.3945 6.26856 17.708 6.40002 17.9398 6.63178C18.1715 6.86354 18.303 7.17706 18.3058 7.50481C18.3087 7.83255 18.1827 8.1483 17.955 8.38406L10.8837 15.4553C10.6493 15.6896 10.3315 15.8213 10 15.8213C9.66854 15.8213 9.35066 15.6896 9.11625 15.4553L2.045 8.38406C1.81066 8.14965 1.67902 7.83176 1.67902 7.50031C1.67902 7.16885 1.81066 6.85097 2.045 6.61656Z"
                                                  fill="#0058BF"/>
                                        </svg>

                                    </button>
                                <?php endif;
                                endif;
                                ?>
                            </div>
                        </div>
                    </div>
            </section>
        <?php
        endwhile;
    endif;
    ?>
</main>
<?php get_footer() ?>
