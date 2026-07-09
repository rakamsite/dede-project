<?php
function catplaylist_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'id' => 'متن دکمه',
	), $atts );
	ob_start();
	$selected_video_id = get_post_meta( $atts['id'], 'videos_playlist_dede_', true );
	$i                 = 0;
	$sidbar            = '';
	wp_enqueue_script( 'player_style', dedeTemplate . '/assets/js/videoplaylist_player.js' );
	if ( ! empty( $selected_video_id ) ): ?>
        <div class="grid grid-cols-1 md:grid-cols-6 md:h-[500px]">
			<?php foreach ( $selected_video_id as $vid ) :
				$video_url = wp_get_attachment_url( $vid['video_attch_id'] );
				$video_thumb = wp_get_attachment_image_url( $vid['video_thumb_id'],'full' );
				$video_title = $vid['video_title'];
				if ( $i == 0 ) :
					?>
                    <div class="md:col-span-4 relative justify-center items-center md:h-[500px]">
                        <video class="w-full object-cover" id="player_main_style" src="<?php echo $video_url; ?>"
                               poster="<?php echo $video_thumb; ?>" type="video/mp4">
                            مرورگر شما از پخش ویدیو پشتیبانی نمی‌کند.
                        </video>
                        <button value="<?php echo $vid['video_attch_id']; ?>" id="custom_play_pause"
                                class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 ">
                            <svg width="113" height="113" viewBox="0 0 113 113" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <circle cx="56.5" cy="56.5" r="56.5" fill="#D9D9D9"/>
                                <path d="M84.5 48.7058C90.5 52.1699 90.5 60.8301 84.5 64.2942L49.25 84.6458C43.25 88.1099 35.75 83.7798 35.75 76.8516L35.75 36.1484C35.75 29.2202 43.25 24.8901 49.25 28.3542L84.5 48.7058Z"
                                      fill="#E3000F"/>
                            </svg>
                        </button>
                        <p id="title_holder"
                           class="absolute truncate w-full text-center top-[350px] left-1/2 transform -translate-x-1/2 text-white drop-shadow-2xl shadow-[#000] text-[50px]"><?php echo $video_title; ?></p>
                    </div>
				<?php endif;
				$video_details = wp_get_attachment_metadata( $vid['video_attch_id'] );
				$views         = get_post_meta( $vid['video_attch_id'], "video_played_views", true );
				$views         = empty( $views ) ? 0 : $views;
				$sidbar        .= '<div class="w-full grid grid-cols-2 h-[130px] gap-1">';
				$sidbar        .= '<div class="h-[130px] relative">';
				$sidbar        .= '<button class="selected-video absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2" data-video-title="' . $video_title . '" data-video-thumbnail="' . $video_thumb . '" data-video-url="' . $video_url . '"><svg width="49" height="49" viewBox="0 0 49 49" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="24.5" cy="24.5" r="24.5" fill="#D9D9D9"/><path d="M37.5 21.9019C39.5 23.0566 39.5 25.9434 37.5 27.0981L20.25 37.0574C18.25 38.2121 15.75 36.7687 15.75 34.4593L15.75 14.5407C15.75 12.2313 18.25 10.7879 20.25 11.9426L37.5 21.9019Z" fill="#E3000F"/></svg></button>';
				$sidbar        .= '<img class="h-[130px] w-full rounded-lg " src="' . $video_thumb . '" />';
				$sidbar        .= '</div>';
				$sidbar        .= '<div class="grid grid-cols-1 content-center gap-3">';
				$sidbar        .= '<p class="truncate font-[700] text-[20px]"> ' . $video_title . '</p>';
				$sidbar        .= '<p class="text-[18px] font-[500]">مدت زمان: ' . $video_details['length_formatted'] . '</p>';
				$sidbar        .= '<p class=" text-[18px]  font-[500]"> آمار بازدید : ' . $views . '</p>';
				$sidbar        .= '</div>';
				$sidbar        .= '</div>';
				?>
				<?php $i ++; endforeach; ?>
            <div class="md:col-span-2 grid grid-cols-1 gap-10 items-center auto p-3 md:mr-5 w-full h-[500px] md:h-auto overflow-hidden md:overflow-y-auto text-[#525252] video-sidebar">
				<?php echo $sidbar; ?>
            </div>
            <?php
            if ($i > 3){
	            echo "<button id='all_videos_view' class='md:hidden text-[#0058BF] w-full text-center text-[18px]'>مشاهده همه </button>";
            }
            ?>
        </div>
	<?php endif;

	return ob_get_clean();
}

add_shortcode( 'catplaylist', 'catplaylist_shortcode' );
