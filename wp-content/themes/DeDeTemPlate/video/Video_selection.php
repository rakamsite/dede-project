<?php
add_action( 'init', 'create_video_playlist_post_type' );
function video_playlist_meta_box() {
	add_meta_box( 'video_playlist_meta_box', 'انتخاب ویدیو', 'video_playlist_render_meta_box', 'video_playlist', 'normal', 'high' );
}

add_action( 'add_meta_boxes', 'video_playlist_meta_box' );

function video_playlist_render_meta_box( $post ) {
	wp_reset_postdata();
	$selected_video_id = get_post_meta( $post->ID, 'videos_playlist_dede_', true );
	wp_enqueue_media();
	wp_enqueue_script( 'video_playlist_script', dedeTemplate . '/assets/js/videoplaylist.js', array( 'jquery' ), '1.0', true );
    $i=0;
	?>
    <div id="metaboxes">
		<?php if ( ! empty( $selected_video_id ) ): ?>
			<?php foreach ( $selected_video_id as $vid ) : ?>
                <div style="background-color:#f0f0f1 ; padding:50px" id="video_<?php echo $i; ?>">
                    <p>
                        <label for="video_playlist_video_id_<?php echo $i; ?>">انتخاب ویدیو:</label><br>
                        <input type="text" id="video_playlist_video_id_<?php echo $i; ?>" name="video_playlist_video_id_<?php echo $i; ?>"
                               class="input_take_info"
                               value="<?php echo $vid['video_attch_id'] ?>" size="50">
                        <input type="button" class="button video_playlist_upload_button" value="انتخاب ویدیو">
                    </p>
                    <p>
                        <label for="video_playlist_thumbnail_id_<?php echo $i; ?>"> انتخاب تامبنیل:</label><br>
                        <input type="text" id="video_playlist_thumbnail_id_<?php echo $i; ?>" name="video_playlist_thumbnail_id_<?php echo $i; ?>"
                               class="input_take_info" value="<?php echo $vid['video_thumb_id'] ?>" size="50">
                        <input type="button" class="button video_playlist_upload_button" value="انتخاب تامبنیل">
                    </p>
                    <p>
                        <label for="video_playlist_title_id_<?php echo $i; ?>">عنوان ویدئو</label><br>
                        <input type="text" id="video_playlist_title_id_<?php echo $i; ?>" name="video_playlist_title_id_<?php echo $i; ?>"
                               value="<?php echo $vid['video_title'] ?>" size="50">
                    </p>
                    <button type="button" class="button button_remove_slide" value="video_<?php echo $i; ?>">
                        حذف این اسلاید
                    </button>
                </div>
			<?php $i++; endforeach; ?>
		<?php else: ?>
            <div style="background-color:#f0f0f1 ; padding:50px" id="video_0">
                <p>
                    <label for="video_playlist_video_id_0">انتخاب ویدیو:</label><br>
                    <input type="text" id="video_playlist_video_id_0" name="video_playlist_video_id_0"
                           class="input_take_info"
                           value="" size="50">
                    <input type="button" class="button video_playlist_upload_button" value="انتخاب ویدیو">
                </p>
                <p>
                    <label for="video_playlist_thumbnail_id_0"> انتخاب تامبنیل:</label><br>
                    <input type="text" id="video_playlist_thumbnail_id_0" name="video_playlist_thumbnail_id_0"
                           class="input_take_info" value="" size="50">
                    <input type="button" class="button video_playlist_upload_button" value="انتخاب تامبنیل">
                </p>
                <p>
                    <label for="video_playlist_title_id_0">عنوان ویدئو</label><br>
                    <input type="text" id="video_playlist_title_id_0" name="video_playlist_title_id_0"
                           value="" size="50">
                </p>
                <button type="button" class="button button_remove_slide" value="video_0">
                    حذف این اسلاید
                </button>
            </div>
		<?php endif; ?>
    </div>
    <input type="hidden" name="counter" id="counter" value="<?php echo $i; ?>">
    <button type="button" class="button" id="add_video">
        اضافه کردن یک ویدئو
    </button>
    <p>
        <label for="video_playlist_shortcode_id">شورت کد</label><br>
        <input type="text" value="<?php echo '[catplaylist id=' . $post->ID . ']'; ?>">
    </p>
	<?php
}

function video_playlist_save_meta_data( $post_id ) {
	if (isset($_POST['counter'])) {
		$counter_videos = $_POST['counter'];
		$videos         = [];
		for ($i = 0 ; $i < $counter_videos+1 ;$i++ ) {
			$video_attch_id = $_POST[ 'video_playlist_video_id_' . $i ];
			$video_thumbnail_attch_id = $_POST[ 'video_playlist_thumbnail_id_' . $i ];
			$video_title = $_POST[ 'video_playlist_title_id_' . $i ];
            if (empty($video_attch_id)){
                continue;
            }else{
	            $videos [ $i ] = [
		            "video_attch_id" => $video_attch_id,
		            "video_thumb_id" => $video_thumbnail_attch_id,
		            "video_title"    => $video_title
	            ];
            }
		}
		update_post_meta( $post_id, 'videos_playlist_dede_', $videos );
	}
}

add_action( 'save_post_video_playlist', 'video_playlist_save_meta_data' );