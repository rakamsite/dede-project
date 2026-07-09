<?php
function custom_product_cat_meta_box()
{
    $term_id = isset($_GET['tag_ID']) ? $_GET['tag_ID'] : '';
    $custom_order = get_term_meta($term_id, 'ordering_custom', true);
    $brand_name = get_term_meta($term_id, 'brand_name', true);
    $brand_url = get_term_meta($term_id, 'brand_url', true);
    $gallery = get_post_meta($term_id, 'product_video_filed', true);
    $hidden_product_information = get_term_meta($term_id, 'hidden_product_information', true);
    $information = wpautop(get_post_meta($term_id, 'product_information', true));
    $recived_image_ = [];
    $recived_image_url_ = [];
    $received_url_ = [];
    $received_title_ = [];
    $received_edit_ = [];
    for ($i = 1; $i < 5; $i++) {
        $received_url_[$i] = get_term_meta($term_id, 'received_url_' . $i, true);
        $received_title_[$i] = get_term_meta($term_id, 'received_title_' . $i, true);
        $received_edit_[$i] = get_term_meta($term_id, 'received_edit_' . $i, true);
    }
    for ($i = 0; $i < 5; $i++) {
        $recived_image_[$i] = get_term_meta($term_id, 'recived_items_picture_' . $i, true);
    }
    foreach ($recived_image_ as $imgid) {
        $recived_image_url_[] = wp_get_attachment_url($imgid);
    }
    ?>
    <script>
        jQuery(document).ready(function ($) {
            function openMediaUploader(button) {
                let file_frame = wp.media.frames.file_frame = wp.media({
                    title: 'یک عکس انتخاب کنید و یا یه عکس آپلود کنید',
                    button: {
                        text: 'انتخاب',
                    },
                    multiple: false
                });
                file_frame.on('select', function () {
                    let attachment = file_frame.state().get('selection').first().toJSON();
                    $(button).prev().prev('input.image_get_url').attr('value', attachment.id);
                    $(button).prev('img.category_previw_banner_image').attr('src', attachment.url);
                });

                file_frame.open();
            }

            $('.upload_category_image_button').on('click', function (event) {
                event.preventDefault();
                openMediaUploader(this);
            });
        });
    </script>
<!--    <tr>-->
<!--        <th scope="row">-->
<!--            اطلاعات فنی-->
<!--        </th>-->
<!--        <td>-->
<!--            --><?php
//            wp_editor($information, 'product_information', array('textarea_name' => 'product_information'));
//
//            add_action('add_meta_boxes', function () {
//                add_meta_box(
//                    'product_information', // Meta box ID
//                    'اطلاعات فنی', // Title of the meta box
//                    'display_custom_meta_box', // Callback function to display the meta box content
//                    'post', // Post type (change it to your desired post type)
//                    'normal', // Position
//                    'high' // Priority
//                );
//            });
//            ?>
<!--        </td>-->
<!--    </tr>-->
    <tr class="form-field ">
        <th scope="row">
            <label for="hidden_product_information">اطلاعات فنی مخفی شود ؟</label>
        </th>
        <td>
            <select class="postform" name="hidden_product_information" id="hidden_product_information"
                    onchange="console.log(this.value)">
                <?php
                if ($hidden_product_information === 'true') {
                    echo "<option value='true' selected>بله</option>";
                } else {
                    echo "<option value='true'>بله</option>";
                }
                if ($hidden_product_information === 'false' || empty($hidden_product_information)) {
                    echo "<option value='false' selected>خیر</option>";
                } else {
                    echo "<option value='false'>خیر</option>";
                }
                ?>
            </select>
        </td>
    </tr>

    <tr class="form-field">
        <th scope="row">
            <label for="ordering_custom">شورت کد گالری</label>
        </th>
        <td>
            <input type="text" name="product_video_filed" id="product_video_filed"
                   value="<?php echo esc_attr($gallery); ?>">
        </td>
    </tr>

    <tr class="form-field">
        <th scope="row">
            <label for="ordering_custom">موقعیت صفحه اصلی</label>
        </th>
        <td>
            <input type="text" name="ordering_custom" id="ordering_custom"
                   value="<?php echo esc_attr($custom_order); ?>">
        </td>
    </tr>
    <tr class="form-field form-required">
        <th scope="row">
            <label for="brand_name">نام برند</label>
        </th>
        <td>
            <input type="text" name="brand_name" id="brand_name" value="<?php echo esc_attr($brand_name); ?>"><br>
        </td>
    </tr>
    <tr class="form-field form-required">
        <th scope="row">
            <label for="brand_url">لینک برند</label>
        </th>
        <td>
            <input type="text" name="brand_url" id="brand_url" value="<?php echo esc_attr($brand_url); ?>">
        </td>
    </tr>

    <tr class="form-field"
        style="border-top-color: #0c0c0c ; border-top-width:2px ; border-top-style: dashed; margin-top:30px">
        >
        <th scope="row">
            <label> انتخاب بنر</label>
        </th>
        <td>
            <input value="<?php echo esc_attr($recived_image_[0]); ?>" type="hidden" class="image_get_url"
                   id="recived_items_picture_0" name="recived_items_picture_0">
            <img class="category_previw_banner_image"
                 src="<?php echo (!empty($recived_image_url_[0])) ? esc_attr($recived_image_url_[0]) : dedeTemplate . '/assets/image/default.png'; ?>"
                 width="100%" height="150px"
                 style="object-fit: cover; object-position: center;">
            <button class="button upload_category_image_button">آپلود عکس</button>
        </td>
    </tr>
    <tr class="form-field"
        style="border-top-color: #0c0c0c ; border-top-width:2px ; border-top-style: dashed; margin-top:30px">
        >
        <th scope="row">
            <label> انتخاب بنر(نسخه موبایل)</label>
        </th>
        <td>
            <input value="<?php echo esc_attr($recived_image_[4]); ?>" type="hidden" class="image_get_url"
                   id="recived_items_picture_4" name="recived_items_picture_4">
            <img class="category_previw_banner_image"
                 src="<?php echo (!empty($recived_image_url_[4])) ? esc_attr($recived_image_url_[4]) : dedeTemplate . '/assets/image/default.png'; ?>"
                 width="100%" height="150px"
                 style="object-fit: cover; object-position: center;">
            <button class="button upload_category_image_button">آپلود عکس</button>
        </td>
    </tr>
    <?php for ($i = 1; $i <= 3; $i++) : ?>
    <tr class="form-field"
        style="border-top-color: #0c0c0c ; border-top-width:2px ; border-top-style: dashed; margin-top:30px">
        <th scope="row">
            <label style="color:#0055aa">
                <?php echo 'دریافت ها ' . $i ?>
            </label>
        </th>
        <td>
            <input type="hidden" class="image_get_url" id="recived_items_picture_<?php echo $i; ?>"
                   name="recived_items_picture_<?php echo $i; ?>"
                   value="<?php echo esc_attr($recived_image_[$i]); ?>">
            <img src="<?php echo (!empty($recived_image_url_[$i])) ? esc_attr($recived_image_url_[$i]) : dedeTemplate . '/assets/image/default.png'; ?>"
                 width="150px" height="150px"
                 style="object-fit: cover; object-position: center;" class="category_previw_banner_image">
            <button class="button upload_category_image_button" style="height: 100%">آپلود عکس</button>
        </td>
    </tr>
    <tr class="form-field ">
        <th scope="row">
            <label style="margin-bottom: 10px; margin-top: 10px"> لینک دریافت</label>
        </th>
        <td>
            <input type="text" name="<?php echo 'received_url_' . $i ?>" id="<?php echo 'received_url_' . $i ?>"
                   value="<?php echo esc_attr($received_url_[$i]); ?>">
        </td>
    </tr>
    <tr class="form-field ">
        <th scope="row">
            <label style="margin-bottom: 10px; margin-top: 10px"> عنوان</label>
        </th>
        <td>
            <input type="text" name="<?php echo 'received_title_' . $i ?>" id="<?php echo 'received_title_' . $i ?>"
                   value="<?php echo esc_attr($received_title_[$i]); ?>">
        </td>
    </tr>
    <tr class="form-field ">
        <th scope="row">
            <label> ویرایش</label>
        </th>
        <td>
            <input type="text" name="<?php echo 'received_edit_' . $i ?>" id="<?php echo 'received_edit_' . $i ?>"
                   value="<?php echo esc_attr($received_edit_[$i]); ?>">
        </td>
    </tr>
<?php endfor; ?>
    <?php
}

add_action('product_cat_edit_form_fields', 'custom_product_cat_meta_box');
add_action('product_cat_add_form_fields', 'custom_product_cat_meta_box');

function save_custom_product_cat_meta($term_id)
{
    if (isset($_POST['ordering_custom'])) {
        $custom_meta = sanitize_text_field($_POST['ordering_custom']);
        update_term_meta($term_id, 'ordering_custom', $custom_meta);
    }

    if (isset($_POST['brand_name'])) {
        $custom_meta = sanitize_text_field($_POST['brand_name']);
        update_term_meta($term_id, 'brand_name', $custom_meta);
        $custom_meta = sanitize_text_field($_POST['brand_url']);
        update_term_meta($term_id, 'brand_url', $custom_meta);
    }

    if (isset($_POST['product_video_filed'])) {
        update_post_meta($term_id, 'product_video_filed', $_POST['product_video_filed']);
    }

//    if (isset($_POST['product_information'])) {
//        update_post_meta($term_id, 'product_information', $_POST['product_information']);
//    }
    if (isset($_POST['hidden_product_information'])){
        update_term_meta($term_id, 'hidden_product_information', $_POST['hidden_product_information']);
    }
    for ($i = 0; $i < 5; $i++) {
        if (isset($_POST['recived_items_picture_' . $i])) {
            update_term_meta($term_id, 'recived_items_picture_' . $i, $_POST['recived_items_picture_' . $i]);
        }
    }

    for ($i = 1; $i < 4; $i++) {
        if (isset($_POST['received_url_' . $i])) {
            update_term_meta($term_id, 'received_url_' . $i, $_POST['received_url_' . $i]);
        }
        if (isset($_POST['received_title_' . $i])) {
            update_term_meta($term_id, 'received_title_' . $i, $_POST['received_title_' . $i]);
        }
        if (isset($_POST['received_edit_' . $i])) {
            update_term_meta($term_id, 'received_edit_' . $i, $_POST['received_edit_' . $i]);
        }
    }

}

add_action('edited_product_cat', 'save_custom_product_cat_meta');
add_action('create_product_cat', 'save_custom_product_cat_meta');
add_action('wp_ajax_add_state_and_city', 'add_state_and_city');
add_action('wp_ajax_nopriv_add_state_and_city', 'add_state_and_city');
function add_state_and_city(): void
{
    if ($_SERVER['REQUEST_METHOD'] === "POST" && current_user_can('administrator')) {
        $all_added_states = 0;
        $all_exists_states = 0;
        $all_added_cities = 0;
        $all_exists_cities = 0;
        $url = 'https://iran-locations-api.ir/api/v1/fa/states';
        $data = file_get_contents($url);
        $states = json_decode($data, true);
        $taxonomy = 'city_country';
        foreach ($states as $state) {
            $state_name = $state['name'];

            $state_exists = term_exists($state_name, $taxonomy);
            if ($state_exists !== 0 && $state_exists !== null) {
                $all_exists_states++;
            } else {
                $category = wp_insert_term($state_name, $taxonomy);
                if (!is_wp_error($category)) {
                    $all_added_states++;
                }
            }
        }

        $get_all_states = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
            'parent' => 0
        ));
        if (!is_wp_error($get_all_states)) {
            foreach ($get_all_states as $state) {
                $city_url = 'https://iran-locations-api.ir/api/v1/fa/cities?state=' . urlencode($state->name);
                $response = wp_remote_get($city_url);
                if (is_wp_error($response)) {
                    continue;
                }

                $city_all = wp_remote_retrieve_body($response);
                $cities = json_decode($city_all, true);

                if ($cities === null || !isset($cities[0]['cities'])) {
                    continue;
                }

                foreach ($cities[0]['cities'] as $city) {
                    if (empty($city['name'])) {
                        continue;
                    }

                    // بررسی وجود شهر
                    $city_exists = term_exists($city['name'], $taxonomy, $state->term_id);
                    if ($city_exists !== 0 && $city_exists !== null) {
                        $all_exists_cities++;
                    } else {
                        $city_add = wp_insert_term($city['name'], $taxonomy, array('parent' => $state->term_id));

                        if (!is_wp_error($city_add)) {
                            $all_added_cities++;
                        }
                    }
                }
            }
        }

        wp_send_json_success('تعداد ' . $all_added_states . ' استان اضافه شد و تعداد ' . $all_exists_states . 'استان از قبل موجود بود' . '<br>' . 'تعداد ' . $all_exists_cities . ' شهر موجود بود و تعداد ' . $all_added_cities . ' شهر اضافه شد ');
    }
}