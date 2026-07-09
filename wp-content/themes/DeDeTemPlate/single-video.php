<?php get_header();
$post_type = new \classes\video_post_type\dede_v2_post_type_main();
wp_enqueue_script("single-product-js", dedeTemplate . '/assets/js/singleProduct.js', array('jquery'), '1.0', false,);
wp_enqueue_script("single-product-ajax", dedeTemplate . '/ajax/singleProduct/js/singleProductAjax.js', array('jquery'), '1.0', true,);

while (have_posts()) :
    the_post();
    $post_id = get_the_ID();
    $post_direction = get_post_meta($post_id, '_video_type', true);
    $video_cover = get_post_meta($post_id, $post_type->cover, true);
    $video_related_product = get_post_meta($post_id, $post_type->related_posts, true);
    $button_link = get_post_meta($post_id, $post_type->button_link, true);
    $button_text = get_post_meta($post_id, $post_type->button_text, true);
    $button_img = get_post_meta($post_id, $post_type->button_image, true);
    $video_link = match ($post_direction) {
        'vertical' => get_post_meta($post_id, $post_type->vertical_link, true),
        'horizontal' => get_post_meta($post_id, $post_type->horizontal_link, true),
        default => '',
    };
    $columns_from_video = match ($post_direction) {
        'vertical' => 2,
        'horizontal' => 1,
        default => 0
    };
    get_template_part('template/quick_view');
    wp_enqueue_script('single-post-menu-content', dedeTemplate . '/assets/js/single-post.js', ['jquery'], 1, ['strategy' => 'defer']);

    ?>
    <div id="primary" class="">
        <main id="main">
            <section class="container mx-auto grid md:grid-cols-3 mt-5 gap-5 relative">
                <section class="md:col-span-2">
                    <article>
                        <?php if (wp_is_mobile()): ?>
                            <div class="w-full bg-[#F2F2F2] rounded-lg">
                                <div class="py-3 w-fit mx-auto px-5">
                                    <div class="flex gap-2 text-sm">
                                        <a href="<?php echo home_url('/') ?>" class="text-[#0058BF]">صفحه
                                            اصلی</a>
                                        /
                                        <a href="<?php echo home_url('/mag') ?>"
                                           class="text-[#0058BF]">مجله</a>
                                        /
                                        <a href="<?php the_permalink() ?>"
                                           class=""><?php echo the_title() ?></a>
                                    </div>
                                </div>
                            </div>
                            <div class="p-5 text-center">
                                <h1 class="entry-title"><?php the_title(); ?></h1>
                            </div>

                        <?php endif; ?>
                        <div class="grid md:grid-cols-<?php echo $columns_from_video ?> gap-5">
                            <?php if (!wp_is_mobile() && $columns_from_video === 1): ?>
                                <div class="w-full bg-[#F2F2F2] rounded-lg">
                                    <div class="py-3 w-full px-5">
                                        <div>
                                            <div class="flex gap-2 text-sm">
                                                <a href="<?php echo home_url('/') ?>" class="text-[#0058BF]">صفحه
                                                    اصلی</a>
                                                /
                                                <a href="<?php echo home_url('/mag') ?>"
                                                   class="text-[#0058BF]">مجله</a>
                                                /
                                                <a href="<?php the_permalink() ?>"
                                                   class=""><?php echo the_title() ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-5">
                                    <h1 class="entry-title"><?php the_title(); ?></h1>
                                </div>
                            <?php endif; ?>
                            <video class="object-fill <?php echo $columns_from_video === 2 ? 'h-full' : 'aspect-video ' ?> w-full px-3 md:px-0 rounded-xl"
                                   playsinline preload="metadata"
                                   poster="<?php echo $video_cover ?>" controls>
                                <source src="<?php echo $video_link ?>" type="video/mp4">
                            </video>
                            <div>
                                <?php if (!wp_is_mobile() && $columns_from_video === 2): ?>
                                    <div class="w-full bg-[#F2F2F2] rounded-lg">
                                        <div class="py-3 w-full px-5">
                                            <div>
                                                <div class="flex gap-2 text-sm">
                                                    <a href="<?php echo home_url('/') ?>" class="text-[#0058BF]">صفحه
                                                        اصلی</a>
                                                    /
                                                    <a href="<?php echo home_url('/mag') ?>"
                                                       class="text-[#0058BF]">مجله</a>
                                                    /
                                                    <a href="<?php the_permalink() ?>"
                                                       class=""><?php echo the_title() ?></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-5">
                                        <h1 class="entry-title"><?php the_title(); ?></h1>
                                    </div>
                                <?php endif; ?>
                                <div class="w-full text-justify prose prose-a:text-[#0058BF] prose-a:no-underline prose-ul:list-inside prose-ol:list-inside !max-w-full break-words px-5 md:px-0">
                                    <?php the_content() ?>
                                </div>
                            </div>
                        </div>
                    </article>
                    <div class="px-4 md:p-0">
                        <?php if (!empty($button_text) && !empty($button_link) && !empty($button_img)) {
                            echo <<<HTML
                            <a href="$button_link" target="_blank" class="flex justify-between px-3 py-2 items-center bg-[#2F2483] rounded-lg my-5">
                                <div class="flex items-center text-white gap-2">
                                    <img class="w-10 h-10 aspect-square" src="{$button_img}" alt="{$button_text}">
                                    <span>{$button_text}</span>
                                </div>
                                <svg width="23" height="18" viewBox="0 0 23 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.197 17.5203C9.88893 17.8275 9.47113 18 9.0355 18C8.59987 18 8.18207 17.8275 7.87398 17.5203L0.481013 10.1483C0.17302 9.84112 -1.15766e-06 9.42451 -1.11968e-06 8.99011C-1.0817e-06 8.55571 0.17302 8.1391 0.481013 7.83188L7.87398 0.45987C8.18384 0.161454 8.59883 -0.00366909 9.02959 6.36285e-05C9.46035 0.00379635 9.8724 0.176085 10.177 0.479827C10.4816 0.783566 10.6544 1.19446 10.6581 1.62399C10.6619 2.05353 10.4963 2.46735 10.197 2.77632L5.74974 7.35188L21.3571 7.35189C21.7928 7.35189 22.2107 7.52448 22.5188 7.83171C22.8269 8.13894 23 8.55563 23 8.99011C23 9.42459 22.8269 9.84128 22.5188 10.1485C22.2107 10.4557 21.7928 10.6283 21.3571 10.6283L5.74974 10.6283L10.197 15.2039C10.505 15.5111 10.678 15.9277 10.678 16.3621C10.678 16.7965 10.505 17.2131 10.197 17.5203Z" fill="white"/>
                                </svg>

                            </a>
                        HTML;
                        } ?>
                    </div>
                    <?php if (wp_is_mobile()): ?>
                        <section class="px-4" id="mobile-aside">
                            <div class="border-2 p-5 rounded-lg border-gray-700 sticky top-0 bg-white overflow-y-visible">
                                <?php echo related_product_generator($video_related_product) ?>
                                <div class="relative mt-10 mb-5">
                                    <hr class="border-gray-700"/>
                                    <span class="absolute right-1/2 translate-x-1/2 bottom-1/2 translate-y-1/2 bg-white px-3">مطالب مرتبط</span>
                                </div>
                                <?php echo related_posts_generator()?>
                            </div>
                        </section>
                    <?php endif; ?>
                    <section class="py-10 px-4">
                        <div class="relative">
                            <hr class="border-gray-700"/>
                            <span class="absolute right-1/2 translate-x-1/2 bottom-1/2 translate-y-1/2 bg-white px-3 text-xl">نظرات</span>
                        </div>
                        <?php
                        $comment_send = 'ارسال نظر';
                        $comment_reply = '<div class="flex gap-2 mt-10 mb-5">
                                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path d="M11.0155 0C9.57297 0 8.14459 0.284511 6.81189 0.837288C5.47918 1.39007 4.26826 2.20028 3.24825 3.22168C1.18826 5.28449 0.0309614 8.08226 0.0309614 10.9995C0.0222847 13.5393 0.900426 16.0022 2.51346 17.9622L0.316559 20.1621C0.16414 20.3168 0.0608899 20.5132 0.0198362 20.7266C-0.0212174 20.94 0.0017651 21.1607 0.0858839 21.3611C0.177119 21.559 0.325021 21.7253 0.510817 21.8389C0.696612 21.9524 0.911921 22.0082 1.12941 21.999H11.0155C13.9288 21.999 16.7227 20.8402 18.7827 18.7773C20.8427 16.7145 22 13.9168 22 10.9995C22 8.08226 20.8427 5.28449 18.7827 3.22168C16.7227 1.15887 13.9288 0 11.0155 0ZM11.0155 19.7991H3.77668L4.79824 18.7762C5.00283 18.5701 5.11766 18.2913 5.11766 18.0007C5.11766 17.7101 5.00283 17.4313 4.79824 17.2252C3.35991 15.7865 2.46422 13.893 2.26377 11.8671C2.06333 9.84132 2.57052 7.80856 3.69894 6.1152C4.82736 4.42185 6.5072 3.17265 8.45226 2.58046C10.3973 1.98826 12.4873 2.08969 14.366 2.86748C16.2447 3.64526 17.7961 5.05128 18.7557 6.84598C19.7152 8.64068 20.0237 10.713 19.6286 12.7099C19.2334 14.7069 18.159 16.5048 16.5885 17.7975C15.018 19.0901 13.0485 19.7975 11.0155 19.7991Z"
                                                      fill="#5E5E5E"/>
                                            </svg>
                
                                            <p class="text-gray-700">ارسال نظر یا سوال</p>
                                        </div>';
                        $comment_reply_to = 'پاسخ';
                        $comment_author = 'نام';
                        $comment_email = 'ایمیل یا ';
                        $comment_body = ' بنویسید';
                        $comment_cancel = 'لغو پاسخ';
                        $submit_field = <<<HTML
                            <div class="flex justify-between items-center">
                                <div>
                                <!--forcaptcha-->
                                </div>
                                %1\$s %2\$s
                            </div>
                        HTML;
                        $comments_args = array(
                            'fields' => array(),
                            'label_submit' => $comment_send,
                            'title_reply' => $comment_reply,
                            'title_reply_to' => $comment_reply_to,
                            'cancel_reply_link' => $comment_cancel,
                            'comment_field' => '<div class="comment-form-comment"><br /><textarea id="comment" name="comment" class="w-full border border-black rounded-lg p-4" rows="4" aria-required="true" placeholder="' . $comment_body . '"></textarea></div>',
                            'comment_notes_after' => '',
                            'id_submit' => 'comment-submit',
                            'must_log_in' => '<div class="flex px-5 justify-between flex-col md:flex-row gap-3 items-center">
                            <p>برای این که بتوانید نظر دهید ، ابتدار لازم است وارد شوید یا ثبت نام کنید.</p>
                                <button data-drawer-hide="mobile-menu" class="bg-[#2F2483] w-fit rounded-lg h-fit text-white flex py-3 px-5 mt-1/2 flex justify-between login_register_page">
                                    <svg width="21" height="26" viewBox="0 0 21 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.3334 10.3333C13.1868 10.3333 15.5 8.02014 15.5 5.16667C15.5 2.3132 13.1868 0 10.3334 0C7.47988 0 5.16669 2.3132 5.16669 5.16667C5.16669 8.02014 7.47988 10.3333 10.3334 10.3333Z" fill="white"></path>
                                        <path d="M20.6667 20.0208C20.6667 23.2306 20.6667 25.8333 10.3333 25.8333C0 25.8333 0 23.2306 0 20.0208C0 16.8111 4.62675 14.2083 10.3333 14.2083C16.0399 14.2083 20.6667 16.8111 20.6667 20.0208Z" fill="white"></path>
                                    </svg>
                                    <a class="mr-2">ورود به حساب کاربری </a>
                                </button>
                            </div>',
                            'submit_field' => $submit_field,
                            'submit_button' => '<input name="%1$s" type="submit" id="%2$s" class="%3$s bg-[#2F2483] p-4 text-white rounded-lg" value="%4$s" />',
                            'logged_in_as' => ''
                        );
                        comment_form($comments_args);
                        if (comments_open() || get_comments_number()) {
                            comments_template('template/video-comment.php'); // این تابع قالب نظرات را لود می‌کند
                        }
                        ?>
                    </section>
                </section>
                <?php if (!wp_is_mobile()): ?>
                    <section>
                        <div class="border p-5 rounded-lg border-gray-400 sticky top-0 bg-white overflow-y-visible">
                            <?php echo related_product_generator($video_related_product) ?>
                            <div class="relative mt-10 mb-5">
                                <hr class="border-gray-700"/>
                                <span class="absolute right-1/2 translate-x-1/2 bottom-1/2 translate-y-1/2 bg-white px-3">مطالب مرتبط</span>
                            </div>
                            <?php echo related_posts_generator()?>
                        </div>
                    </section>
                <?php endif; ?>
            </section>
        </main>
    </div>
<?php
endwhile;
get_footer();
function related_product_generator($video_related_product): string
{
    if (!empty($video_related_product)) {
        $related_product = '';
        foreach ($video_related_product as $product_id) {
            $product = wc_get_product($product_id);
            $name = $product->get_name();
            $image = $product->get_image('woocommerce_thumbnail', [
                'class' => 'aspect-square rounded-t-2xl'
            ]);
            $price = wc_price($product->get_price());
            $permalink = $product->get_permalink();
            $related_product .= <<<HTML
                                        <div class="rounded-lg mx-auto flex justify-center sliderFixHeight" data-carousel-item>
                                            <div class=" drop-shadow-lg bg-white rounded-2xl">
                                                $image
                                                <h4 class=" py-1.5 text-center mx-auto w-[300px] truncate">{$name}</h4>
                                                <p class=" py-1.5 text-center ">{$price}</p>
                                                <div class="flex text-white divide-x-reverse justify-evenly items-center bg-[#2F2483] max-h-[50px] rounded-b-2xl">
                                                    <button class="quick_post_view" value="{$product_id}">
                                                        مشاهده سریع
                                                    </button>
                                                    <hr class="border h-10 border-white"/>
                                                    <a class="" href="{$permalink}">اطلاعات بیشتر</a>
                                                </div>
                                            </div>
                                        </div>
                                    HTML;
        }
        return <<<HTML
                                        <div class="relative">
                                            <hr class="border-gray-700"/>
                                            <span class="absolute right-1/2 translate-x-1/2 bottom-1/2 translate-y-1/2 bg-white px-3 text-sm md:text-lg">محصولات پیشنهادی</span>
                                        </div>
                                        <div class="min-w-fit overflow-hidden">
                                            <div id="related_product_container" class="relative w-full mt-10" data-carousel="slide">
                                                <div class="relative overflow-hidden rounded-lg sliderItemHolder h-0">
                                                    {$related_product}
                                                </div>
                                                    <button type="button" class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
                                                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-[#2F2483]/80 group-hover:bg-[#2F2483] group-focus:ring-4 group-focus:ring-white group-focus:outline-none">
                                                            <svg class="w-4 h-4 text-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4"/>
                                                            </svg>
                                                            <span class="sr-only">Previous</span>
                                                        </span>
                                                    </button>
                                                    <button type="button" class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
                                                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-[#2F2483]/80 group-hover:bg-[#2F2483] group-focus:ring-4 group-focus:ring-white group-focus:outline-none">
                                                            <svg class="w-4 h-4 text-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                                            </svg>
                                                            <span class="sr-only">Next</span>
                                                        </span>
                                                    </button>
                                            </div>
                                        </div>
                                    HTML;
    } else {
        return '';
    }

}

function related_posts_generator(): string
{
    $related_post = get_posts();
    $posts = '';
    foreach ($related_post as $post) {
        $name = $post->post_title;
        $image = get_the_post_thumbnail_url($post->ID);
        $permalink = get_post_permalink($post);
        try {
            $post_date = date_format(new DateTime($post->post_date), 'Y/M/d');
            $post_date = apply_filters('dede_v2_convert_to_jalali', $post_date);
        } catch (Exception $e) {
            $post_date = '';
        }
        $posts .= <<<HTML
                                <a href="{$permalink}">
                                    <div class="flex gap-2 py-5">
                                        <img src="$image" alt="$name" class="aspect-square w-24 rounded-lg">
                                        <div class=" flex flex-col justify-between">
                                        <p>{$name}</p>
                                        <span class="inline-flex items-center gap-2">
                                            <svg width="19" height="20" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.53333 5.45105C9.7625 5.60109 9.96684 5.77127 10.1464 5.96157C10.3259 6.15187 10.4806 6.36047 10.6104 6.58737C10.7403 6.81427 10.8358 7.05397 10.8969 7.30649C10.958 7.55901 10.9924 7.81701 11 8.08051C11 8.516 10.9141 8.92589 10.7422 9.31015C10.5703 9.69441 10.3335 10.0293 10.0318 10.3147C9.73003 10.6002 9.38056 10.8252 8.98333 10.9899C8.58611 11.1546 8.15833 11.2388 7.7 11.2424C7.35243 11.2424 7.01632 11.193 6.69167 11.0942C6.36701 10.9954 6.0691 10.8527 5.79792 10.666C5.52674 10.4794 5.28611 10.2562 5.07604 9.99633C4.86597 9.7365 4.70365 9.44921 4.58906 9.13449H0V0.702653H1.46667V0H2.2V0.702653H7.33333V0H8.06667V0.702653H9.53333V5.45105ZM0.733333 1.40531V2.81061H8.8V1.40531H8.06667V2.10796H7.33333V1.40531H2.2V2.10796H1.46667V1.40531H0.733333ZM4.41719 8.43183C4.40573 8.31838 4.4 8.20127 4.4 8.08051C4.4 7.64501 4.48594 7.23513 4.65781 6.85086C4.82969 6.4666 5.06649 6.13174 5.36823 5.84629C5.66997 5.56084 6.01944 5.33577 6.41667 5.17108C6.81389 5.0064 7.24167 4.92223 7.7 4.91857C8.08194 4.91857 8.44861 4.97895 8.8 5.09972V3.51326H0.733333V8.43183H4.41719ZM7.7 10.5398C8.05521 10.5398 8.3875 10.4757 8.69688 10.3477C9.00625 10.2196 9.27743 10.0439 9.51042 9.82067C9.7434 9.59743 9.92674 9.3376 10.0604 9.04116C10.1941 8.74473 10.2628 8.42451 10.2667 8.08051C10.2667 7.74016 10.1998 7.42177 10.0661 7.12534C9.93247 6.82891 9.74913 6.56907 9.51615 6.34583C9.28316 6.12259 9.01198 5.94693 8.7026 5.81884C8.39323 5.69075 8.05903 5.62488 7.7 5.62122C7.34479 5.62122 7.0125 5.68527 6.70312 5.81335C6.39375 5.94144 6.12257 6.1171 5.88958 6.34034C5.6566 6.56358 5.47326 6.82342 5.33958 7.11985C5.2059 7.41628 5.13715 7.7365 5.13333 8.08051C5.13333 8.42085 5.20017 8.73924 5.33385 9.03567C5.46754 9.33211 5.65087 9.59194 5.88385 9.81518C6.11684 10.0384 6.38802 10.2141 6.6974 10.3422C7.00677 10.4703 7.34097 10.5361 7.7 10.5398ZM8.06667 7.72918H9.16667V8.43183H7.33333V6.32387H8.06667V7.72918Z" fill="#4B5259"/>
                                            </svg>
                                            {$post_date}
                                        </span>
                                        </div>
                                    </div>
                                </a>   
                            HTML;
    }
    wp_reset_query();
    return "<div class='flex flex-col divide-y divide-2'>{$posts}</div>";
}
?>
