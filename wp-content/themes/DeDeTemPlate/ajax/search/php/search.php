<?php
add_action( 'wp_ajax_main_home_search', 'main_home_search_callback' );
add_action( 'wp_ajax_nopriv_main_home_search', 'main_home_search_callback' );
function main_home_search_callback() {
	if ( $_SERVER['REQUEST_METHOD'] === "POST" ) {
		$search_term = $_POST['searchQuery'];
		$page        = $_POST['page'];
		if ( $_POST['searchInProduct'] ) {
			$args = array(
				'post_type'      => 'product',
				's'              => $search_term,
				'posts_per_page' => 8,
				'paged' =>$page
			);

			$query = new WP_Query( $args );

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					echo '<div class="drop-shadow-md w-full"> <img class="w-full h-auto object-fill rounded-t-lg" src="' . get_the_post_thumbnail_url(null , "full") . '"> <p class="truncate w-full text-center p-3 text-[20px] font-[700] text-[#525252]">' . get_the_title() . '</p> <a href="' . get_the_permalink() . '" class=""> <button class="w-full p-2 rounded-b-lg bg-[#2F2483] text-white text-[18px] font-[500]"> اطلاعات بیشتر </button> </a> </div> </div>';
				}
				wp_reset_postdata();
			} else {
				echo 'بدونه نتیجه';
			}
			wp_die();
		}
		if ( $_POST['searchInBlog'] ) {
			$args = array(
				'post_type'      => 'post',
				's'              => $search_term, // جستجوی بر اساس کلمه کلیدی
				'posts_per_page' => 8, // تعداد نتایج نمایش داده شده
				'paged' => $page
			);

			$query = new WP_Query( $args );

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					echo '<div class="drop-shadow-md w-full"> <img class="w-full h-auto object-fill rounded-t-lg" src="' . get_the_post_thumbnail_url() . '"> <p class="truncate w-full text-center p-3 text-[20px] font-[700] text-[#525252]">' . get_the_title() . '</p> <a href="' . get_the_permalink() . '" class=""> <button class="w-full p-2 rounded-b-lg bg-[#2F2483] text-white text-[18px] font-[500]"> اطلاعات بیشتر </button> </a> </div> </div>';
				}
				wp_reset_postdata();
			} else {
				echo 'بدونه نتیجه';
			}
			wp_die();

		}
		if ( $_POST['searchInCategories'] ) {
			$args = array(
				'taxonomy'   => 'product_cat',
				'name__like' => $search_term, // جستجوی بر اساس کلمه کلیدی
				'number'     => 8, // تعداد نتایج نمایش داده شده
				'offset'     => ( $page - 1 ) * 8
			);

			$query = new WP_Term_Query( $args );

			if ( ! empty( $query->terms ) ) {
				foreach ( $query->terms as $term ) {
					$thumbnail_id  = get_term_meta( $term->term_id, 'thumbnail_id', true );
					$thumbnail_url = wp_get_attachment_image_url( $thumbnail_id, 'medium' );
					$category_url  = get_term_link( $term, 'product_cat' );
					echo '<div class="drop-shadow-md w-full"> <img class="w-full h-auto object-fill rounded-t-lg" src="' . $thumbnail_url . '"> <p class="truncate w-full text-center p-3 text-[20px] font-[700] text-[#525252]">' . $term->name . '</p> <a href="' . $category_url . '" class=""> <button class="w-full p-2 rounded-b-lg bg-[#2F2483] text-white text-[18px] font-[500]"> اطلاعات بیشتر </button> </a> </div> </div>';
				}
				wp_reset_postdata();
				echo '';
			} else {
				echo 'بدون نتیجه';
			}
			wp_die();

		}
	}
}