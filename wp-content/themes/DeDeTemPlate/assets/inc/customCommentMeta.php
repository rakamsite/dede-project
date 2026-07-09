<?php

add_action('add_meta_boxes_comment', function ($comment) {
    add_meta_box(
        'dede_images_comment',
        'تصاوری آپلود شده',
        'dede_comment_images_meta_box_cb',
        'comment',
        'normal'
    );
    add_meta_box(
        'dede_videos_comment',
        ' فیلم های آپلود شده',
        'dede_comment_video_meta_box_cb',
        'comment',
        'normal'
    );

});

function dede_comment_images_meta_box_cb($comment): void
{
    $comment_id = $comment->comment_ID;
    $get_images = get_comment_meta($comment_id, '_dede_comment_image_', true);
    echo "<table class='wp-block-table'><tbody><tr class='wp-block-table__row'>";
    if (!empty($get_images)) {
        foreach ($get_images as $img) {
            $img_url = wp_get_attachment_image_url($img);

            echo "<td class='wp-block-table__cell' style='position: relative'><button onclick='this.parentNode.remove()' class='button button-danger' style='position: absolute; top:0; left:0'><svg height='10' width='10' aria-hidden='true' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 14 14'><path stroke='currentColor' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6'/></svg></button><img height='150px' width='150px' src='$img_url' />";
            echo "<input type='hidden' name='images[]' value='$img' /></td>";
        }
    } else {
        echo "<td>تصویری برای این محصول بارگذازی نشده است.</td>";
    }
    echo "</tr></tbody></table>";
}

function dede_comment_video_meta_box_cb($comment): void
{
    $comment_id = $comment->comment_ID;
    $get_videos = get_comment_meta($comment_id, '_dede_comment_video_', true);
    echo "<table class='wp-block-table'><tbody><tr class='wp-block-table__row'>";
    if (!empty($get_videos)) {
        foreach ($get_videos as $vid) {
            $vid_url = wp_get_attachment_url($vid);
            echo "<td class='wp-block-table__cell' style='position:relative;'><button onclick='this.parentNode.remove()' class='button button-danger' style='position: absolute; top:0; left:0; z-index: 50'><svg height='10' width='10' aria-hidden='true' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 14 14'><path stroke='currentColor' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6'/></svg></button><video controls height='150px' width='150px' ><source  src='$vid_url'></video>";
            echo "<input type='hidden' name='videos[]' value='$vid' /></td>";
        }
    } else {
        echo "<td>ویدئویی برای این محصول بارگذازی نشده است.</td>";
    }
    echo "</tr></tbody></table>";
}
add_action('comment_post', 'save_comment_media_metadata');
add_action('edit_comment', 'save_comment_media_metadata');
function save_comment_media_metadata($comment_id): void
{
    if (isset($_POST['images'])) {
        $images = $_POST['images'];
        update_comment_meta($comment_id, '_dede_comment_image_', $images);
    } else {
        update_comment_meta($comment_id, '_dede_comment_image_', 0);
    }

    if (isset($_POST['videos'])) {
        $videos = $_POST['videos'];
        update_comment_meta($comment_id, '_dede_comment_video_', $videos);
    } else {
        update_comment_meta($comment_id, '_dede_comment_video_', 0);
    }
}

function add_template_meta_box(): void
{
    add_meta_box(
        'post_template_meta_box',
        'انتخاب قالب پست',
        'display_template_meta_box',
        'post',
        'side'
    );
}

add_action('add_meta_boxes', 'add_template_meta_box');

function display_template_meta_box($post): void
{
    $template = get_post_meta($post->ID, '_custom_template', true);
    echo '<label for="custom_template">قالب تمام صفحه:</label>';
    echo '<select name="custom_template" id="custom_template">';
    echo '<option value="false" ' . selected($template, 'false', false) . '>غیر فعال</option>';
    echo '<option value="true" ' . selected($template, 'true', false) . '>فعال</option>';
    echo '</select>';
}
function save_template_meta_box($post_id): void
{
    if (array_key_exists('custom_template', $_POST)) {
        update_post_meta($post_id, '_custom_template', sanitize_text_field($_POST['custom_template']));
    }
}

add_action('save_post', 'save_template_meta_box');
