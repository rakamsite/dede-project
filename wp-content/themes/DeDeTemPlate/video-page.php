<?php
/*
Template Name: قالب ویدیو
*/
get_header();
wp_enqueue_script( 'dede-loadmore-ajax', dedeTemplate . '/ajax/blogPosts/js/ajax.js', array( 'jquery' ), 1.0, true );
$args  = array(
    'post_type'      => 'video',
    'posts_per_page' => 8,
);
$query = new WP_Query( $args );
?>
<div class="w-full bg-[#F2F2F2] px-5 md:px-0">
    <div class=" py-3 container mx-auto px-5 md:grid flex flex-col justify-center space-y-5 md:space-y-0 grid-cols-1 md:grid-cols-2 gap-0 place-content-between place-items-center">
        <div class="justify-self-start">
            <h4>مجله</h4>
        </div>
        <div class="justify-self-end ">
            <div class="flex gap-2 text-sm">
                <a href="<?php echo home_url( '/' ) ?>" class="text-[#0058BF]">صفحه اصلی</a>
                /
                <a href="">مجله</a>
            </div>
        </div>
    </div>
</div>
<div class="grid grid-cols-1 md:grid-cols-4 gap-10 w-full container mx-auto mt-5 px-5 md:px-0" id="blog_posts">
    <?php
    if ( $query->have_posts() ) :
        while ( $query->have_posts() ) :
            $query->the_post();
            ?>
            <div class="flex flex-col items-center  shadow-lg font-bold text-center w-full h-full">
                <img class="rounded-t-lg"
                     src="<?php echo ( get_the_post_thumbnail_url() ) ? get_the_post_thumbnail_url() : dedeTemplate . '/assets/image/default.png'; ?>"
                     alt="<?php echo the_title() ?>"/>
                <h2 class="text-[#525252] grow text-sm text-center py-2 my-auto grid items-center"><?php echo the_title() ?></h2>
                <a href="<?php  the_permalink(); ?>" class="w-full bg-[#2F2483] text-white  py-2 rounded-b-lg">
                    اطلاعات بیشتر
                </a>
            </div>
        <?php
        endwhile;
    endif;
    wp_reset_postdata();
    ?>
</div>
<div class="mx-auto text-center mt-20 hidden loadmore-animation">
    <svg class="animate-spin -ml-1 mr-3 h-20 w-20" xmlns="http://www.w3.org/2000/svg" fill="none"
         viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
</div>
<button class="load-more-button w-full text-center text-[#0058BF] mt-20 py-5 hover:bg-gray-100 text-lg" data-value="video">نتایج بیشتر
    <svg width="20" height="20" class="inline-block" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M2.04494 6.61625C2.27935 6.38191 2.59723 6.25027 2.92869 6.25027C3.26014 6.25027 3.57803 6.38191 3.81244 6.61625L9.99994 12.8038L16.1874 6.61625C16.4232 6.38855 16.7389 6.26256 17.0667 6.26541C17.3944 6.26826 17.708 6.39972 17.9397 6.63148C18.1715 6.86324 18.3029 7.17676 18.3058 7.5045C18.3086 7.83225 18.1826 8.148 17.9549 8.38375L10.8837 15.455C10.6493 15.6893 10.3314 15.821 9.99994 15.821C9.66848 15.821 9.3506 15.6893 9.11619 15.455L2.04494 8.38375C1.8106 8.14934 1.67896 7.83146 1.67896 7.5C1.67896 7.16854 1.8106 6.85066 2.04494 6.61625Z"
              fill="#0058BF"/>
    </svg>
</button>
<?php get_footer(); ?>
