<?php
function load_more_posts_ajax_handler() {
	$paged = $_POST['paged'];
    $post_type = $_POST['post_type'];
	$args = array(
		'post_type'      => $post_type, // نوع نوشته‌هایی که می‌خواهید بخوانید
		'posts_per_page' => 8, // تعداد پست‌ها برای بارگذاری بیشتر
		'paged'          => $paged, // صفحه‌بندی
	);

	$query = new WP_Query( $args );
	if ( $query->have_posts() ) :
		while ( $query->have_posts() ) :
			$query->the_post();
			?>
            <div class="flex flex-col items-center  shadow-lg font-bold text-center w-full h-full">
                <img class="rounded-t-lg" src="<?php echo (get_the_post_thumbnail_url()) ? get_the_post_thumbnail_url() : dedeTemplate. '/assets/image/default.png'; ?>" alt="<?php echo the_title()?>" />
                <h2 class="text-[#525252] grow text-sm text-center py-2 my-auto grid items-center"><?php echo the_title()?></h2>
                <a href="<?php the_permalink(); ?>" class="w-full bg-[#2F2483] text-white  py-2 rounded-b-lg">
                    اطلاعات بیشتر
                </a>
            </div>
		<?php
		endwhile;
	endif;
	wp_reset_postdata();
    wp_die();

	}
add_action( 'wp_ajax_load_more_posts', 'load_more_posts_ajax_handler' );
add_action( 'wp_ajax_nopriv_load_more_posts', 'load_more_posts_ajax_handler' );