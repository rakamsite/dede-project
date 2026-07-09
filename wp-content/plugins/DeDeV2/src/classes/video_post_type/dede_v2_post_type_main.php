<?php

namespace classes\video_post_type;

use function Crontrol\Event\add;

class dede_v2_post_type_main
{
    public string $vertical_link;
    public string $horizontal_link;
    public string $video_cover;
    public string $button_link;
    public string $button_text;
    public string $button_image;
    public string $related_posts;
    public string $video_type;
    public string $cover;
    public string $circle_cover;
    public string $video_likes;
    public string $liked_videos;

    public function __construct()
    {
        $this->vertical_link = '_video_vertical_link';
        $this->horizontal_link = '_video_horizontal_link';
        $this->video_cover = '_video_cover';
        $this->button_link = '_video_button_link';
        $this->button_text = '_video_button_text';
        $this->button_image = '_video_button_image';
        $this->related_posts = '_video_related_posts';
        $this->video_type = '_video_type';
        $this->cover = '_video_cover';
        $this->circle_cover = '_video_circle_cover';
        $this->video_likes = '_video_likes';
        $this->liked_videos = '_video_user_liked_videos';
    }

    function run(): void
    {
        add_action('init', [$this, "dede_v2_post_type_main"]);
        add_action('cmb2_admin_init', [$this, "dede_v2_video_metabox_create"]);
        add_action('add_meta_boxes_video', [$this, "dede_v2_video_metabox_create_callback"]);
        add_action('add_meta_boxes_post', [$this, "dede_v2_video_metabox_create_callback"]);
        add_action("save_post_video", [$this, "dede_v2_video_metabox_save_post_callback"], 20, 1);
        add_action("save_post_post", [$this, "dede_v2_video_metabox_save_post_callback"], 20, 1);
        add_filter("manage_edit-vid_category_columns", function ($columns) {
            $new_columns = array(
                'short_code' => 'شورت کد',
            );

            return array_merge($columns, $new_columns);
        });

        add_action("manage_vid_category_custom_column", function ($content, $column_name, $term_id) {

            if ($column_name === 'short_code') {
                $shortcode = "[vid_category id='$term_id']";
                $content = '<div class="shortcode-container">';
                $content .="<span>$shortcode</span>";
                $content .= '</div>';
            }
            echo $content;

        }, 10, 3);

    }

    function dede_v2_post_type_main(): void
    {
        register_post_type('video', array(
            'labels' => array(
                'name' => 'ویدئوها',
                'singular_name' => 'ویدئو',
                'menu_name' => 'مدیریت ویدئوها',
                'add_new' => 'افزودن ویدئو جدید',
                'add_new_item' => 'افزودن ویدئوی جدید',
                'edit_item' => 'ویرایش ویدئو',
                'new_item' => 'ویدئوی جدید',
                'view_item' => 'مشاهده ویدئو',
                'view_items' => 'مشاهده ویدئوها',
                'search_items' => 'جستجوی ویدئو',
                'not_found' => 'هیچ ویدئویی پیدا نشد',
                'not_found_in_trash' => 'هیچ ویدئویی در زباله‌دان پیدا نشد',
                'all_items' => 'همه ویدئوها',
                'archives' => 'آرشیو ویدئوها',
                'attributes' => 'ویژگی‌های ویدئو',
                'insert_into_item' => 'وارد کردن در ویدئو',
                'uploaded_to_this_item' => 'آپلود شده برای این ویدئو',
                'featured_image' => 'تصویر شاخص',
                'set_featured_image' => 'تنظیم تصویر شاخص',
                'remove_featured_image' => 'حذف تصویر شاخص',
                'use_featured_image' => 'استفاده به عنوان تصویر شاخص',
            ),
            'public' => true,
            'has_archive' => true,
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-video-alt3',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments', 'custom-fields', 'revisions'),
            'taxonomies' => array(),
            'capability_type' => 'post',
            'hierarchical' => false,
            'menu_position' => 5,

        ));
        $args = array(
            'labels' => array(
                'name'              => 'دسته بندی ها',
                'singular_name'     => 'دسته بندی',
                'search_items'      => 'جوستجو دسته بندی',
                'all_items'         => 'همه دسته بندی ها',
                'edit_item'         => 'ویرایش دسته بندی ',
                'update_item'       => 'بروز رسانی دسته بندی',
                'add_new_item'      => 'افزودن دسته بندی',
                'new_item_name'     => 'دسته بندی جدید',
                'menu_name'         => 'دسته ها',
            ),
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'rewrite'           => array(
                'slug' => 'vid_category',
            ),
        );

        register_taxonomy('vid_category', 'video', $args);
    }

    function dede_v2_video_metabox_create(): void
    {
        $meta_box_links = new_cmb2_box([
            'id' => 'video_post_type_link_metabox',
            'title' => 'ویدئو',
            'object_types' => ['video' ],
            'context' => 'side',
            'priority' => 'high',
            'show_names' => true
        ]);
        $meta_box_links->add_field(array(
            'name' => 'نوع ویدئو',
            'desc' => 'بین حالت افقی و عمودی قابل انتخاب میباشد.',
            'id' => $this->video_type,
            'type' => 'select',
            'options' => [
                '0' => 'انتخاب کنید',
                'vertical' => 'عمودی',
                'horizontal' => 'افقی'
            ],
        ));
        $meta_box_links->add_field(array(
            'name' => 'لینک عمودی ویدئو',
            'desc' => 'لینک ویدئو به صورتی عمودی ',
            'id' => $this->vertical_link,
            'type' => 'text_url',
            'protocols' => array('http', 'https'),
        ));

        $meta_box_links->add_field(array(
            'name' => 'لینک افقی ویدئو',
            'desc' => 'لینک ویدئو به صورت افقی ',
            'id' => $this->horizontal_link,
            'type' => 'text_url',
            'protocols' => array('http', 'https'),
        ));

        $meta_box_links->add_field(array(
            'name' => 'کاور ویدئو',
            'desc' => 'کاور ویدئو بر اساس اندازه ویدئو اصلی میبایست عمودی یا افقی انتخاب شود',
            'id' => $this->cover,
            'type' => 'file',
            'options' => array(
                'url' => false,
            ),
            'text' => array(
                'add_upload_file_text' => 'افزودن تصویر'
            ),
            'query_args' => array(
                'type' => array(
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                    'image/svg'
                ),
            ),
            'preview_size' => '100*100',
        ));
        $meta_box_links->add_field(array(
            'name' => 'کاور دایره ای',
            'desc' => 'کاور دایره ای جهت نمایش در قسمت استوری',
            'id' => $this->circle_cover,
            'type' => 'file',
            'options' => array(
                'url' => false,
            ),
            'text' => array(
                'add_upload_file_text' => 'افزودن تصویر'
            ),
            'query_args' => array(
                'type' => array(
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                    'image/svg'
                ),
            ),
            'preview_size' => '100*100',
        ));

        $meta_box_button = new_cmb2_box([
            'id' => 'video_post_type_button_meta_box',
            'title' => 'دکمه',
            'object_types' => array('video' , 'post'),
            'context' => 'normal',
            'priority' => 'high',
            'show_names' => true
        ]);
        $meta_box_button->add_field([
            'name' => 'لینک دکمه',
            'desc' => 'لینک دکمه سینگل',
            'id' => $this->button_link,
            'type' => 'text_url',
            'protocols' => array('http', 'https'),
        ]);
        $meta_box_button->add_field([
            'name' => 'متن دکمه',
            'desc' => 'متن دکمه سینگل',
            'id' => $this->button_text,
            'type' => 'text',
        ]);
        $meta_box_button->add_field([
            'name' => 'تصویر دکمه',
            'desc' => 'تصویر دکمه سینگل',
            'id' => $this->button_image,
            'type' => 'file',
            'options' => array(
                'url' => false,
            ),
            'text' => array(
                'add_upload_file_text' => 'افزودن تصویر'
            ),
            'query_args' => array(
                'type' => array(
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                    'image/svg'
                ),
            ),
            'preview_size' => '100*100',
        ]);
    }

    function dede_v2_video_metabox_create_callback(): void
    {
        wp_enqueue_script('video_post_type_js', dede_v2_url . '/assets/js/video_post_type.js', ['select2']);
        wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'), '4.1.0', true);
        wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '4.1.0');

        add_meta_box("related_products", "محصولات مرتبط", function () {
            $related_post_selected = get_post_meta(get_the_ID(), $this->related_posts);
            $products = wc_get_products(['limit' => -1, 'orderby' => 'name', 'order' => 'ASC']);
            $options = "";
            foreach ($products as $product) {
                if (is_array($related_post_selected[0]) && !empty($related_post_selected[0])){
                    if (in_array($product->get_id() , $related_post_selected[0])){
                        $options .= "<option value='{$product->get_id()}' selected>{$product->get_name()}</option>";
                    }else{
                        $options .= "<option value='{$product->get_id()}'>{$product->get_name()}</option>";
                    }
                }else{
                    $options .= "<option  value='{$product->get_id()}'>{$product->get_name()}</option>";
                }
            }

            echo <<<HTML
<div class="cmb-row cmb-type-file">
    <div class="cmb-th">
        <label for="{$this->related_posts}">
            محصولات مرتبط
        </label>
    </div>
    <div class="cmb-td">
        <select name="{$this->related_posts}[]" id="{$this->related_posts}" value={$related_post_selected} class="related_post_select_2" style="width: 100%; height:300px" multiple>
            $options
        </select>
    </div>
</div>
HTML;

        });
    }

    function dede_v2_video_metabox_save_post_callback($post_id ): void
    {
        if(isset($_POST[$this->related_posts]) && !empty($_POST[$this->related_posts])){
            update_post_meta($post_id, $this->related_posts, $_POST[$this->related_posts]);
        }
    }
}