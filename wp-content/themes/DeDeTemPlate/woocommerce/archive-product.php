<?php
/**
 * @package DeDeTemplate
 * @subpackage WooCommerce
 */
wp_enqueue_script("single-product-js", dedeTemplate . '/assets/js/singleProduct.js', array('jquery'), '1.0', false,);
wp_enqueue_script("single-product-ajax", dedeTemplate . '/ajax/singleProduct/js/singleProductAjax.js', array('jquery'), '1.0', true,);
wp_enqueue_script("magnific-popup-js", dedeTemplate . '/node_modules/@fancyapps/ui/dist/fancybox/fancybox.umd.js', array('jquery'), '1.0', true,);
wp_enqueue_style("magnific-popup-css", dedeTemplate . '/node_modules/@fancyapps/ui/dist/fancybox/fancybox.css');
wp_enqueue_script("edit-cart-items-js", dedeTemplate . '/ajax/EditCartItems/js/js.js', array('jquery'), '1.0', false,);


function percentage_calcuter($regulator, $sale)
{
    if (!empty($sale)) {
        if ($regulator > $sale) {
            return round(((($regulator - $sale) / $regulator) * 100));
        }
    }
}

$term_id = get_queried_object_id();
$term = get_term($term_id);
$custom_order = get_term_meta($term_id, 'ordering_custom', true);
$brand_name = get_term_meta($term_id, 'brand_name', true);
$hidden_product_information = get_term_meta($term_id, 'hidden_product_information', true) ?? false;
$brand_url = get_term_meta($term_id, 'brand_url', true);
$gallery = get_term_meta($term_id, 'product_video_filed', true);
$information = $term->description;
$information_with_shortcodes = do_shortcode($information);
$processed_information = wpautop($information_with_shortcodes);
$recived_Check_1 = get_term_meta($term_id, 'received_url_1', true);
$recived_Check_2 = get_term_meta($term_id, 'received_title_1', true);
$recived_Check_3 = get_term_meta($term_id, 'received_edit_1', true);
$recived_image_ = [];
$recived_image_url_ = [];
$received_url_ = [];
$received_title_ = [];
$received_edit_ = [];
if (!empty($recived_Check_1) && !empty($recived_Check_2) && !empty($recived_Check_3)) {
    for ($i = 1; $i < 4; $i++) {
        $received_url_[$i] = get_term_meta($term_id, 'received_url_' . $i, true);
        $received_title_[$i] = get_term_meta($term_id, 'received_title_' . $i, true);
        $received_edit_[$i] = get_term_meta($term_id, 'received_edit_' . $i, true);
    }
}
if (empty($hidden_product_information)) {
    $hidden_product_information = "false";
}
for ($i = 0; $i < 5; $i++) {
    $recived_image_[$i] = get_term_meta($term_id, 'recived_items_picture_' . $i, true);
}
foreach ($recived_image_ as $imgid) {
    $recived_image_url_[] = wp_get_attachment_url($imgid);
}
$story_short_code = get_term_meta($term_id, '_dede_term_story_shortcode', true);
get_header();
get_template_part('template/quick_view');
get_template_part('template/EditDetailedCartItems');
?>
<main id="primary" class="w-full">
    <div class="w-full bg-[#F2F2F2] under_header">
        <div class=" py-3 container mx-auto px-5 md:grid flex flex-col justify-center space-y-5 md:space-y-0 grid-cols-1 md:grid-cols-2 gap-0 place-content-between place-items-center">
            <div class="justify-self-start">
                <h4><?php echo $term->name ?></h4>
            </div>
            <div class="justify-self-end ">
                <div class="flex gap-2 text-sm">
                    <a href="<?php echo home_url('/') ?>" class="text-[#0058BF]">صفحه اصلی</a>
                    /
                    <a href="<?php echo get_term_link($term) ?>"><?php echo $term->name ?></a>
                </div>
            </div>
        </div>
    </div>
    <?php if (!empty($story_short_code)): ?>
        <div class="py-5 mx-auto container">
            <?php echo do_shortcode($story_short_code) ?>
        </div>
    <?php endif; ?>

    <!--    --><?php //if (!empty($recived_image_url_[0])) : ?>
    <!--        <img class="w-full h-auto" src="-->
    <?php //echo wp_is_mobile() ? $recived_image_url_[4] : $recived_image_url_[0]; ?><!--"-->
    <!--             alt="--><?php //echo woocommerce_page_title() ?><!--">-->
    <!--    --><?php //endif; ?>
    <!--    <div class="container mt-12 relative text-center flex items-center justify-center mx-auto">-->
    <!--        <hr class="border-[0.5px] border-[#525252]/50 w-full mx-auto my-auto"/>-->
    <!--        <p class="font-bold text-[#525252] text-2xl mt-0 mb-auto absolute -top-5 bg-white px-10">محصولات </p>-->
    <!--    </div>-->
    <div class="container mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 px-5 md:px-0 pt-10">
            <?php if (have_posts()) {
                while (have_posts()) {
                    the_post();
                    global $product;
                    $product_id = $product->get_id();
                    $product_title = get_the_title();
                    $product_permalink = get_permalink();
                    $thumbnail_url = get_the_post_thumbnail_url($product_id);
                    if ($product->is_type('variable')) {
                        $default_attributes = $product->get_default_attributes();
                        $variations = $product->get_available_variations();
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
                                $variation_prices_regular = $var->get_regular_price();
                                $variation_prices_sale = $var->get_sale_price();
                                $discount_percentage = percentage_calcuter($variation_prices_regular, $variation_prices_sale);
                            }
                        }
                    }
                    ?>
                    <div class="flex flex-col relative items-center shadow-lg font-bold text-center w-full h-full">
                        <a href="<?php echo $product_permalink; ?>">
                            <img class="rounded-t-lg" src="<?php echo $thumbnail_url; ?>"
                                 alt="<?php echo $product_title; ?>">
                        </a>

                        <h3 class="text-[#525252] grow text-center py-2 my-auto grid items-center"><?php echo $product_title; ?></h3>
                        <div class="flex gap-5 text-sm justify-center items-center pb-2">
                            <?php
                            if ($product->is_type('variable')) {
                                if (!empty($variation_prices_regular)) {
                                    if (isset($discount_percentage)) {
                                        echo '<span class="absolute top-5 left-5 text-white flex"><svg width="67" height="44" viewBox="0 0 67 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M16.9387 2.17843C17.5524 1.49303 18.3038 0.944748 19.1437 0.569382C19.9837 0.194015 20.8933 0 21.8133 0C22.7333 0 23.643 0.194015 24.4829 0.569382C25.3229 0.944748 26.0742 1.49303 26.6879 2.17843L28.2147 3.884C28.4328 4.12807 28.7032 4.3199 29.0056 4.44525C29.308 4.5706 29.6348 4.62626 29.9617 4.6081L32.2518 4.4816C33.1701 4.43117 34.0887 4.5749 34.9478 4.90342C35.8068 5.23193 36.5869 5.73786 37.2372 6.38819C37.8876 7.03852 38.3935 7.81865 38.722 8.67768C39.0505 9.53671 39.1943 10.4554 39.1438 11.3737L39.0173 13.6638C38.9995 13.9903 39.0553 14.3166 39.1807 14.6186C39.306 14.9207 39.4977 15.1906 39.7414 15.4086L41.4492 16.9353C42.1349 17.549 42.6835 18.3005 43.0591 19.1407C43.4347 19.9808 43.6288 20.8907 43.6288 21.811C43.6288 22.7313 43.4347 23.6412 43.0591 24.4814C42.6835 25.3215 42.1349 26.073 41.4492 26.6867L39.7414 28.2134C39.4974 28.4316 39.3055 28.702 39.1802 29.0044C39.0548 29.3068 38.9992 29.6336 39.0173 29.9605L39.1438 32.2505C39.1943 33.1689 39.0505 34.0875 38.722 34.9465C38.3935 35.8056 37.8876 36.5857 37.2372 37.236C36.5869 37.8864 35.8068 38.3923 34.9478 38.7208C34.0887 39.0493 33.1701 39.193 32.2518 39.1426L29.9617 39.0161C29.6352 38.9983 29.3088 39.0541 29.0068 39.1794C28.7048 39.3048 28.4348 39.4964 28.2168 39.7402L26.6901 41.448C26.0764 42.1337 25.3249 42.6823 24.4848 43.0579C23.6446 43.4335 22.7347 43.6276 21.8144 43.6276C20.8941 43.6276 19.9842 43.4335 19.1441 43.0579C18.3039 42.6823 17.5524 42.1337 16.9387 41.448L15.412 39.7402C15.1938 39.4961 14.9235 39.3043 14.621 39.179C14.3186 39.0536 13.9919 38.998 13.665 39.0161L11.3749 39.1426C10.4566 39.193 9.53793 39.0493 8.6789 38.7208C7.81986 38.3923 7.03974 37.8864 6.38941 37.236C5.73908 36.5857 5.23315 35.8056 4.90463 34.9465C4.57612 34.0875 4.43239 33.1689 4.48282 32.2505L4.60932 29.9605C4.62715 29.6339 4.57132 29.3076 4.44598 29.0056C4.32064 28.7035 4.12899 28.4336 3.88522 28.2156L2.17965 26.6889C1.49389 26.0752 0.945299 25.3237 0.569715 24.4836C0.194131 23.6434 0 22.7335 0 21.8132C0 20.8929 0.194131 19.983 0.569715 19.1428C0.945299 18.3027 1.49389 17.5512 2.17965 16.9375L3.88522 15.4108C4.12929 15.1926 4.32112 14.9223 4.44647 14.6198C4.57182 14.3174 4.62748 13.9906 4.60932 13.6638L4.48282 11.3737C4.43239 10.4554 4.57612 9.53671 4.90463 8.67768C5.23315 7.81865 5.73908 7.03852 6.38941 6.38819C7.03974 5.73786 7.81986 5.23193 8.6789 4.90342C9.53793 4.5749 10.4566 4.43117 11.3749 4.4816L13.665 4.6081C13.9915 4.62593 14.3178 4.5701 14.6199 4.44476C14.9219 4.31942 15.1919 4.12778 15.4098 3.884L16.9365 2.17843H16.9387ZM29.8984 13.727C30.3073 14.136 30.537 14.6907 30.537 15.269C30.537 15.8473 30.3073 16.402 29.8984 16.811L16.8122 29.8972C16.4009 30.2945 15.8499 30.5143 15.2781 30.5094C14.7062 30.5044 14.1592 30.275 13.7548 29.8706C13.3504 29.4663 13.121 28.9192 13.1161 28.3474C13.1111 27.7755 13.3309 27.2246 13.7282 26.8132L26.8144 13.727C27.2234 13.3181 27.7781 13.0884 28.3564 13.0884C28.9348 13.0884 29.4894 13.3181 29.8984 13.727ZM16.3607 13.088C15.4931 13.088 14.6609 13.4326 14.0474 14.0462C13.4339 14.6597 13.0892 15.4918 13.0892 16.3595V16.3813C13.0892 17.249 13.4339 18.0811 14.0474 18.6947C14.6609 19.3082 15.4931 19.6529 16.3607 19.6529H16.3825C17.2502 19.6529 18.0823 19.3082 18.6959 18.6947C19.3094 18.0811 19.6541 17.249 19.6541 16.3813V16.3595C19.6541 15.4918 19.3094 14.6597 18.6959 14.0462C18.0823 13.4326 17.2502 13.088 16.3825 13.088H16.3607ZM27.2659 23.9931C26.3982 23.9931 25.5661 24.3378 24.9526 24.9514C24.339 25.5649 23.9944 26.397 23.9944 27.2647V27.2865C23.9944 28.1542 24.339 28.9863 24.9526 29.5998C25.5661 30.2134 26.3982 30.5581 27.2659 30.5581H27.2877C28.1554 30.5581 28.9875 30.2134 29.6011 29.5998C30.2146 28.9863 30.5593 28.1542 30.5593 27.2865V27.2647C30.5593 26.397 30.2146 25.5649 29.6011 24.9514C28.9875 24.3378 28.1554 23.9931 27.2877 23.9931H27.2659Z" fill="#E3000F"/>
                                        <rect x="31.7241" y="7.93115" width="34.8966" height="26.9655" rx="3" fill="#E3000F"/>
                                        </svg><p class="absolute text-2xl top-[6px] right-1">' . intval($discount_percentage) . '</p></span>';
                                        echo '<p class="grow text-[#E3000F] line-through text-xs">' . wc_price($variation_prices_regular) . '</p>';
                                        echo '<p class="grow text-[#008826] ">' . wc_price($variation_prices_sale) . '</p>';
                                    } else {
                                        echo '<p class="grow text-[#008826] ">' . wc_price($variation_prices_regular) . '</p>';
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
                                    value="<?php echo $product_id; ?>">
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

    <?php if (!empty($gallery)): ?>
        <div class="container mt-12 relative text-center flex items-center justify-center mx-auto">
            <hr class="border-[0.5px] border-[#525252]/50 w-full mx-auto my-auto"/>
            <p class="font-bold text-[#525252] text-2xl mt-0 mb-auto absolute -top-5 bg-white px-10">ویدیو ها</p>
        </div>
        <div class="container mx-auto py-10">
            <?php echo do_shortcode($gallery) ?>
        </div>
    <?php endif; ?>
    <?php if ($hidden_product_information == "false" && !empty($processed_information)): ?>
        <div class="container mt-20 relative text-center flex items-center justify-center mx-auto ">
            <hr class="border-[0.5px] border-[#525252]/50 w-full mx-auto my-auto"/>
            <p class="font-bold text-[#525252] text-2xl mt-0 mb-auto absolute -top-5 bg-white px-10">اطلاعات فنی</p>
        </div>
    <?php endif; ?>
    <div class="container mx-auto py-10 mt-10 px-5 md:p-0">
        <div class="my-10 w-full text-justify prose prose-a:text-[#0058BF] prose-a:no-underline prose-ul:list-inside prose-ol:list-inside !max-w-full break-words">
            <?php echo($processed_information) ?>
        </div>
    </div>
    <?php if (!empty($received_title_)): ?>
        <div class="container mt-12 relative text-center flex items-center justify-center mx-auto">
            <hr class="border-[0.5px] border-[#525252]/50 w-full mx-auto my-auto"/>
            <p class="font-bold text-[#525252] text-2xl mt-0 mb-auto absolute -top-5 bg-white px-10">دریافت ها</p>
        </div>
        <div class="container mx-auto px-5 md:p-0">
            <div class="w-full grid grid-cols-1 md:grid-cols-3 gap-5 mt-12">
                <?php for ($i = 1; $i < 4; $i++): ?>
                    <div class="flex flex-col relative items-center drop-shadow-md drop-shadow-black font-bold text-center w-full h-full shadow-lg">
                        <img class="rounded-t-lg" src="<?php echo $recived_image_url_[$i] ?>"
                             alt="<?php echo $received_title_[$i] ?>">
                        <h2 class="text-[#525252] grow text-center py-2 my-auto grid items-center"></h2>
                        <div class="flex gap-5 text-sm justify-center items-center pb-2">
                            <h2 class="text-[#525252] font-[700] text-[20px]">
                                <?php echo $received_title_[$i] ?>
                            </h2>
                        </div>
                        <div class="w-full grid grid-cols-2 content-center bg-[#2F2483] text-white py-4 rounded-b-lg text-sm">
                            <a href="<?php echo $received_url_[$i] ?>"
                               class="border-l-[2px] border-white text-center quick_post_view">
                                دریافت
                            </a>
                            <p>ویرایش : <?php echo $received_edit_[$i] ?>
                            <p/>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
