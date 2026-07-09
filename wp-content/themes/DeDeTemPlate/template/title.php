<title>
	<?php
	if (is_home()) {
		bloginfo('name');
	} elseif (is_single()) {
		single_post_title();
	} elseif (is_page()) {
		single_post_title();
	} elseif (is_category()) {
		single_cat_title();
	} elseif (is_tag()) {
		single_tag_title();
	} elseif (is_author()) {
		echo 'نویسنده',get_the_author();
	} elseif (is_404()) {
		echo 'صفحه مورد نظر یافت نشد!';
	} elseif (function_exists('is_shop') && is_shop()) {
        echo 'فروشگاه';
	} elseif (function_exists('is_product') && is_product()) {
		single_post_title();
	} elseif (function_exists('is_product_category') && is_product_category()) {
		single_term_title();
	} elseif (function_exists('is_product_tag') && is_product_tag()) {
		single_term_title();
	} else {
		wp_title('', true);
	}
	?>
</title>
