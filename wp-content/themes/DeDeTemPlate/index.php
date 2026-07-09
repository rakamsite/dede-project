<?php
// $get_licence_key = cmb2_get_option('dede_licence_key','licence_key_input');
// $original_url = get_bloginfo('url');
// $cleaned_url = str_replace(array("http://", "https://"), "", $original_url);
// $test_site_string = md5($cleaned_url);
// if ($get_licence_key == $test_site_string) {
	get_header();
	get_template_part( 'template/slider' );
	get_template_part( 'template/popupSlider' );
	get_template_part( 'template/main_story' );
	get_template_part( 'template/product_section' );
	get_template_part( 'template/quick_view' );
	get_template_part( 'template/EditDetailedCartItems' );
	get_footer();
// }else{
// 	echo "<h1>لطفا از قسمت تنظیات قالب کد لایسنس را وارد کنید.</h1>";
// }
