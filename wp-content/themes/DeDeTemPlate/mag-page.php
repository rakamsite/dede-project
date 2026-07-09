<?php
/*
Template Name: قالب مجله
*/
$special_banner_1 = cmb2_get_option('dede_mag_page_setting', 'special_banner_1');
$special_banner_2 = cmb2_get_option('dede_mag_page_setting', 'special_banner_2');
$special_banner_url_1 = $special_banner_1[0]['special_banner_1_url'] ?? '/';
$special_banner_url_2 = $special_banner_2[0]['special_banner_2_url'] ?? '/';
$special_banner_img_1 = '';
$special_banner_img_2 = '';
if (wp_is_mobile()) {
    $special_banner_img_1 = $special_banner_1[0]['special_banner_1_img_mobile'] ?? dedeTemplate . '/assets/mag_default/special_banner_1_mobile.png';
    $special_banner_img_2 = $special_banner_2[0]['special_banner_2_img_mobile'] ?? dedeTemplate . '/assets/mag_default/special_banner_2_mobile.png';
} else {
    $special_banner_img_1 = $special_banner_1[0]['special_banner_1_img_desktop'] ?? dedeTemplate . '/assets/mag_default/special_banner_1.png';
    $special_banner_img_2 = $special_banner_2[0]['special_banner_2_img_desktop'] ?? dedeTemplate . '/assets/mag_default/special_banner_2.png';
}
$special_post_big = cmb2_get_option('dede_mag_page_setting', 'special_post_big_right');
$special_post_square = cmb2_get_option('dede_mag_page_setting', 'special_post_square');

//big post information
$big_post_img = '';
$big_post_permalink = '';
$special_post_big_right_id = $special_post_big[0]['special_post_big_right_id'];
if (!isset($special_post_big_right_id)) {
    $big_post_img = dedeTemplate . '/assets/mag_default/special_post_big.png';
    $big_post_permalink = '/';
} else {
    $post = get_post($special_post_big_right_id);
    $big_post_img = get_the_post_thumbnail_url($post);
    $big_post_permalink = get_permalink($post);
}

//square posts
$special_post_square_right_top_id = $special_post_square[0]['special_post_square_right_top_id'];
$special_post_square_let_top_id = $special_post_square[0]['special_post_square_let_top_id'];
$special_post_square_right_bottom_id = $special_post_square[0]['special_post_square_right_bottom_id'];
$special_post_square_left_top_id = $special_post_square[0]['special_post_square_left_top_id'];
function get_square_post_data($post_id): array
{
    if (!isset($post_id)) {
        return [
            'img' => dedeTemplate . '/assets/mag_default/special_post_square.png',
            'permalink' => '/',
        ];
    } else {
        $post = get_post($post_id);
        $img = get_the_post_thumbnail_url($post);
        $permalink = get_permalink($post);
        return [
            'img' => $img,
            'permalink' => $permalink,
        ];
    }
}

$square_post_right_top = get_square_post_data($special_post_square_right_top_id);
$square_post_let_top = get_square_post_data($special_post_square_let_top_id);
$square_post_right_bottom = get_square_post_data($special_post_square_right_bottom_id);
$square_post_left_top = get_square_post_data($special_post_square_left_top_id);

get_header(); ?>
<nav class="w-full bg-[#F2F2F2] px-5 md:px-0">
    <div class=" py-3 container mx-auto px-5 md:grid flex flex-col justify-center space-y-5 md:space-y-0 grid-cols-1 md:grid-cols-2 gap-0 place-content-between place-items-center">
        <div class="justify-self-start">
            <h4>مجله</h4>
        </div>
        <div class="justify-self-end ">
            <div class="flex gap-2 text-sm">
                <a href="<?php echo home_url('/') ?>" class="text-[#0058BF]">صفحه اصلی</a>
                /
                <a href="">مجله</a>
            </div>
        </div>
    </div>
</nav>
<section class="container mx-auto px-3 md:px-0">
    <section id="desktop-hero-header" class="w-full grid md:grid-cols-5 gap-5 mt-5">
        <?php if (!wp_is_mobile()): ?>
            <div id="right-hero-desktop">
                <div class="min-h-[450px] border-2 border-black rounded-lg pt-8 px-4">
                    <div class="relative">
                        <hr class="border-black"/>
                        <p class="absolute top-1/2 right-1/2 -translate-y-1/2 translate-x-1/2 bg-white px-2">فهرست
                            مطالب</p>
                    </div>
                    <?php
                    $menu_item = wp_nav_menu(array(
                        'menu' => 'mag-content-menu',
                        'theme_location' => 'mag-content-menu',
                        'container' => 'ul',
                        'menu_class' => 'my-3 text-[#525252] no-underline space-y-5 pt-5 [&_li]:flex [&_li]:gap-3 ',
                        'menu_id' => 'menu-id',
                        'fallback_cb' => false,
                        'before' => '<svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.5 12.458C10.2292 12.458 9.01039 11.9532 8.11178 11.0546C7.21317 10.1559 6.70833 8.93717 6.70833 7.66634H8.625C8.625 8.42884 8.9279 9.16011 9.46707 9.69927C10.0062 10.2384 10.7375 10.5413 11.5 10.5413C12.2625 10.5413 12.9938 10.2384 13.5329 9.69927C14.0721 9.16011 14.375 8.42884 14.375 7.66634H16.2917C16.2917 8.93717 15.7868 10.1559 14.8882 11.0546C13.9896 11.9532 12.7708 12.458 11.5 12.458ZM11.5 2.87467C12.2625 2.87467 12.9938 3.17758 13.5329 3.71674C14.0721 4.25591 14.375 4.98718 14.375 5.74967H8.625C8.625 4.98718 8.9279 4.25591 9.46707 3.71674C10.0062 3.17758 10.7375 2.87467 11.5 2.87467ZM18.2083 5.74967H16.2917C16.2917 5.12042 16.1677 4.49733 15.9269 3.91598C15.6861 3.33463 15.3332 2.8064 14.8882 2.36145C14.4433 1.91651 13.915 1.56356 13.3337 1.32275C12.7523 1.08195 12.1293 0.958008 11.5 0.958008C10.2292 0.958008 9.01039 1.46284 8.11178 2.36145C7.21317 3.26007 6.70833 4.47885 6.70833 5.74967H4.79167C3.72792 5.74967 2.875 6.60259 2.875 7.66634V19.1663C2.875 19.6747 3.07693 20.1622 3.43638 20.5216C3.79582 20.8811 4.28333 21.083 4.79167 21.083H18.2083C18.7167 21.083 19.2042 20.8811 19.5636 20.5216C19.9231 20.1622 20.125 19.6747 20.125 19.1663V7.66634C20.125 7.15801 19.9231 6.6705 19.5636 6.31105C19.2042 5.95161 18.7167 5.74967 18.2083 5.74967Z" fill="#4B5259"/></svg>'
                    ));
                    ?>
                </div>
                <a href="<?php echo $special_banner_url_2 ?>" id="special-banner-1-container-desk-top"
                   class="my-5 block">
                    <img src='<?php echo $special_banner_img_2 ?>' alt='dede.ir'
                         class='aspect-square rounded-lg w-full'/>
                </a>
            </div>
        <?php endif; ?>
        <section id="hero-main-banners" class="col-span-4 space-y-5">
            <a href="<?php echo $special_banner_url_1 ?>" class="w-full">
                <img src='<?php echo $special_banner_img_1 ?>' alt='dede.ir' class='rounded-lg w-full'/>
            </a>
            <?php if (wp_is_mobile()): ?>
                <button data-dropdown-toggle="mag-content-menu"
                        class="w-full flex justify-between items-center p-2 border border-black rounded-2xl">
                    <span>فهرست مطالب</span>
                    <svg width="25" height="10" viewBox="0 0 25 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.999999 1.00019L12.5 9.11621L24 1.0002" stroke="#525252" stroke-linecap="round"
                              stroke-linejoin="round"/>
                    </svg>
                </button>

                <?php
                $menu_item = wp_nav_menu(array(
                    'menu' => 'mag-content-menu',
                    'menu_id' => 'mag-content-menu',
                    'theme_location' => 'mag-content-menu',
                    'container' => 'ul',
                    'menu_class' => 'my-3 text-[#525252] no-underline space-y-5 [&_li]:flex [&_li]:gap-3 w-11/12 justify-start bg-white rounded-lg border border-black transition-all p-4 hidden ',
                    'fallback_cb' => false,
                    'before' => '<svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.5 12.458C10.2292 12.458 9.01039 11.9532 8.11178 11.0546C7.21317 10.1559 6.70833 8.93717 6.70833 7.66634H8.625C8.625 8.42884 8.9279 9.16011 9.46707 9.69927C10.0062 10.2384 10.7375 10.5413 11.5 10.5413C12.2625 10.5413 12.9938 10.2384 13.5329 9.69927C14.0721 9.16011 14.375 8.42884 14.375 7.66634H16.2917C16.2917 8.93717 15.7868 10.1559 14.8882 11.0546C13.9896 11.9532 12.7708 12.458 11.5 12.458ZM11.5 2.87467C12.2625 2.87467 12.9938 3.17758 13.5329 3.71674C14.0721 4.25591 14.375 4.98718 14.375 5.74967H8.625C8.625 4.98718 8.9279 4.25591 9.46707 3.71674C10.0062 3.17758 10.7375 2.87467 11.5 2.87467ZM18.2083 5.74967H16.2917C16.2917 5.12042 16.1677 4.49733 15.9269 3.91598C15.6861 3.33463 15.3332 2.8064 14.8882 2.36145C14.4433 1.91651 13.915 1.56356 13.3337 1.32275C12.7523 1.08195 12.1293 0.958008 11.5 0.958008C10.2292 0.958008 9.01039 1.46284 8.11178 2.36145C7.21317 3.26007 6.70833 4.47885 6.70833 5.74967H4.79167C3.72792 5.74967 2.875 6.60259 2.875 7.66634V19.1663C2.875 19.6747 3.07693 20.1622 3.43638 20.5216C3.79582 20.8811 4.28333 21.083 4.79167 21.083H18.2083C18.7167 21.083 19.2042 20.8811 19.5636 20.5216C19.9231 20.1622 20.125 19.6747 20.125 19.1663V7.66634C20.125 7.15801 19.9231 6.6705 19.5636 6.31105C19.2042 5.95161 18.7167 5.74967 18.2083 5.74967Z" fill="#4B5259"/></svg>'
                ));
                ?>

            <?php endif; ?>
            <div class="grid md:grid-cols-2 gap-5">
                <a href="<?php echo $big_post_permalink ?>" id="special-post-big" class="block">
                    <img src="<?php echo $big_post_img ?>" alt="" class="aspect-square w-full rounded-lg">
                </a>
                <div class="grid grid-cols-2 gap-5">
                    <a href="<?php echo $square_post_right_top['permalink'] ?>" class=" block">
                        <img src="<?php echo $square_post_right_top['img'] ?>" alt=""
                             class="aspect-square rounded-lg w-full"/>
                    </a>
                    <a href="<?php echo $square_post_let_top['permalink'] ?>" class=" block">
                        <img src="<?php echo $square_post_let_top['img'] ?>" alt=""
                             class="aspect-square rounded-lg w-full"/>

                    </a>
                    <a href="<?php echo $square_post_right_bottom['permalink'] ?>" class=" block">
                        <img src="<?php echo $square_post_right_bottom['img'] ?>" alt=""
                             class="aspect-square rounded-lg w-full"/>

                    </a>
                    <a href="<?php echo $square_post_left_top['permalink'] ?>" class=" block">
                        <img src="<?php echo $square_post_left_top['img'] ?>" alt=""
                             class="aspect-square rounded-lg w-full"/>
                    </a>
                </div>
            </div>
            <?php if (wp_is_mobile()): ?>
                <a href="<?php echo $special_banner_url_2 ?>" id="special-banner-1-container-desk-top"
                   class="my-5 block">
                    <img src='<?php echo $special_banner_img_2 ?>' alt='dede.ir'
                         class='aspect-video rounded-lg w-full'/>
                </a>
            <?php endif; ?>
        </section>
    </section>
    <?php if (!wp_is_mobile()): ?>
        <section class="w-full">
            <div class="relative p-5 mt-10 mb-5">
                <hr class="border-black"/>
                <p class="absolute right-0 top-1/2 -translate-y-1/2 bg-white pl-5">آخرین مطالب</p>
                <div class="absolute left-0 top-1/2 -translate-y-1/2 pr-5 bg-white">
                    <a href="/blogs" class=" bg-[#2F2483] text-white p-2 rounded">
                        همه مطالب
                    </a>
                </div>
            </div>
            <div class="flex gap-5 justify-stretch">
                <?php get_recently_blog_posts(); ?>
            </div>
        </section>
        <section class="w-full">
            <div class="relative p-5 mt-10 mb-5">
                <hr class="border-black"/>
                <p class="absolute right-0 top-1/2 -translate-y-1/2 bg-white pl-5">آخرین ویدئو ها</p>
                <div class="absolute left-0 top-1/2 -translate-y-1/2 pr-5 bg-white">
                    <a href="/videos" class=" bg-[#2F2483] text-white p-2 rounded">
                        همه ویدئو ها
                    </a>
                </div>
            </div>
            <div class="flex gap-5 justify-stretch">
                <?php get_recently_video_posts(); ?>
            </div>
        </section>
    <?php endif; ?>
    <?php if (wp_is_mobile()): ?>
        <section class="w-full">
            <div class="relative p-5 mt-10 mb-5">
                <hr class="border-black"/>
                <p class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-5">آخرین مطالب</p>
            </div>
            <div class="w-full">
                <div id="posts-carousel" class="relative w-full h-fit" data-carousel="static">
                    <div class="relative overflow-x-clip rounded-lg aspect-[5/6] h-fit">
                        <?php get_recently_blog_posts(); ?>
                    </div>
                    <button type="button"
                            class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                            data-carousel-prev>
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-[#2F2483]/80 group-hover:bg-[#2F2483] group-focus:ring-4 group-focus:ring-white group-focus:outline-none">
                            <svg class="w-4 h-4 text-white rtl:rotate-180"
                                 aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round"
                                      stroke-linejoin="round" stroke-width="2"
                                      d="M5 1 1 5l4 4"/>
                            </svg>
                            <span class="sr-only">Previous</span>
                        </span>
                    </button>
                    <button type="button"
                            class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                            data-carousel-next>
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-[#2F2483]/80 group-hover:bg-[#2F2483] group-focus:ring-4 group-focus:ring-white group-focus:outline-none">
                            <svg class="w-4 h-4 text-white rtl:rotate-180"
                                 aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round"
                                      stroke-linejoin="round" stroke-width="2"
                                      d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="sr-only">Next</span>
                        </span>
                    </button>

                </div>
                <a href="/blogs" class=" bg-[#2F2483] text-white p-2 w-full block text-center rounded mt-6">
                    همه مطالب
                </a>
            </div>
        </section>
        <section class="w-full">
            <div class="relative p-5 mt-10 mb-5">
                <hr class="border-black"/>
                <p class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-5">آخرین ویدیوها</p>
            </div>

            <div id="video-carousel" class="relative w-full h-fit" data-carousel="static">
                <div class="relative overflow-x-clip rounded-lg aspect-[5/6] h-fit">
                    <?php get_recently_video_posts(); ?>
                </div>
                <button type="button"
                        class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                        data-carousel-prev>
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-[#2F2483]/80 group-hover:bg-[#2F2483] group-focus:ring-4 group-focus:ring-white group-focus:outline-none">
                            <svg class="w-4 h-4 text-white rtl:rotate-180"
                                 aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round"
                                      stroke-linejoin="round" stroke-width="2"
                                      d="M5 1 1 5l4 4"/>
                            </svg>
                            <span class="sr-only">Previous</span>
                        </span>
                </button>
                <button type="button"
                        class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                        data-carousel-next>
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-[#2F2483]/80 group-hover:bg-[#2F2483] group-focus:ring-4 group-focus:ring-white group-focus:outline-none">
                            <svg class="w-4 h-4 text-white rtl:rotate-180"
                                 aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round"
                                      stroke-linejoin="round" stroke-width="2"
                                      d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="sr-only">Next</span>
                        </span>
                </button>

            </div>
            <a href="/videos" class=" bg-[#2F2483] text-white p-2 w-full block text-center rounded mt-6">
                همه ویدیوها
            </a>
        </section>
    <?php endif; ?>
</section>
<?php get_footer();
function get_recently_blog_posts(): void
{
    $posts_html = '';
    $posts = wp_get_recent_posts([
        'numberposts' => 5,
        'post_type' => 'post',
        'post_status' => 'publish',
    ]);
    foreach ($posts as $post) {
        $post_thumbnail_url = get_the_post_thumbnail_url($post['ID']);
        $post_title = $post['post_title'];
        $post_permalink = get_permalink($post['ID']);
        $posts_html .= <<<HTML
            <div class="w-full flex flex-col h-full min-h-max drop-shadow bg-white" data-carousel-item>
                <img src="$post_thumbnail_url" alt="$post_title" class="aspect-square w-full rounded-t-2xl" />
                <h5 class="px-2">$post_title</h5>
                <a href="$post_permalink" class="bg-[#2F2483] text-white p-2 rounded-b-2xl w-full text-center">
                    اطلاعات بیشتر
                </a>
            </div>
            HTML;
    }
    echo $posts_html;
}

function get_recently_video_posts(): void
{
    $posts_html = '';
    $posts = wp_get_recent_posts([
        'numberposts' => 5,
        'post_type' => 'video',
        'post_status' => 'publish',
    ]);
    foreach ($posts as $post) {
        $post_thumbnail_url = get_the_post_thumbnail_url($post['ID']);
        $post_title = $post['post_title'];
        $post_permalink = get_permalink($post['ID']);
        $posts_html .= <<<HTML
            <div class="w-full flex flex-col h-full min-h-max drop-shadow bg-white" data-carousel-item>
                <img src="$post_thumbnail_url" alt="$post_title" class="aspect-square w-full rounded-t-2xl" />
                <h5 class="px-2">$post_title</h5>
                <a href="$post_permalink" class="bg-[#2F2483] text-white p-2 rounded-b-2xl w-full text-center">
                    اطلاعات بیشتر
                </a>
            </div>
            HTML;
    }
    echo $posts_html;
}


?>
