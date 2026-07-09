<?php
// ایجاد اکشن اختصاصی برای افزایش مقدار متا دیتای "video_played_views"
add_action( 'wp_ajax_increase_video_views', 'increase_video_views' );
add_action( 'wp_ajax_nopriv_increase_video_views', 'increase_video_views' ); // این خط برای کاربران غیر وارد شده به سایت

function increase_video_views() {
	if (!isset($_COOKIE['video_views_increased'])) {
		if (isset($_POST['post_id'])) {
			$post_id = intval($_POST['post_id']);

			// دریافت مقدار فعلی متا دیتای "video_played_views"
			$views         = get_post_meta( $post_id, 'video_played_views', true );
			$current_views = ( ! empty( $views ) ) ? $views : 0;

			// افزایش مقدار متا دیتای "video_played_views" یک واحد
			update_post_meta($post_id, 'video_played_views', $current_views + 1);

			// ایجاد کوکی برای نشان دادن اجرای موفقیت‌آمیز تابع
			setcookie('video_views_increased', '1', time() + 3600, COOKIEPATH, COOKIE_DOMAIN);

			// ارسال پاسخ به Ajax با مقدار جدید متا دیتا
			echo $current_views + 1;
		}
	}
	// مهم: خاتمه دادن به فرآیند ورودی/خروجی
	wp_die();
}
