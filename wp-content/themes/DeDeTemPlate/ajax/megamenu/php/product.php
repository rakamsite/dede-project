<?php
add_action('wp_ajax_dede_get_child_product_thumbnail_cat', 'dede_get_child_product_thumbnail_cat');
add_action('wp_ajax_nopriv_dede_get_child_product_thumbnail_cat', 'dede_get_child_product_thumbnail_cat');
add_action('wp_ajax_dede_get_child_cat_get_icon_link', 'dede_get_child_cat_get_icon_link');
add_action('wp_ajax_nopriv_dede_get_child_cat_get_icon_link', 'dede_get_child_cat_get_icon_link');
add_action('wp_ajax_dede_get_product_posts_child', 'dede_get_product_posts_child');
add_action('wp_ajax_nopriv_dede_get_product_posts_child', 'dede_get_product_posts_child');
add_action('wp_ajax_dede_get_submenu_function', 'dede_get_submenu_function');
add_action('wp_ajax_nopriv_dede_get_submenu_function', 'dede_get_submenu_function');

function dede_get_child_product_thumbnail_cat()
{
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $term_id = intval($_POST['term_id']);
        $thumbnail_url = get_the_post_thumbnail_url($term_id);
        wp_send_json_success($thumbnail_url);
    }
}

function dede_get_child_cat_get_icon_link()
{
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $term_id = intval($_POST['term_id']);
        $thumbnail_id = get_post_meta($term_id, '_menu_item_icon', true);
        wp_send_json_success($thumbnail_id);
    }
}


function dede_get_product_posts_child()
{
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $response = '';
        $cat_id = $_POST['cat_id'];

        $parent_category = get_term_by('id', $cat_id, 'product_cat');

        if ($parent_category && !is_wp_error($parent_category)) {
            $term_id = $parent_category->term_id;
            $term_slug = $parent_category->slug;

            $args = array(
                'post_type' => 'product',
                'posts_per_page' => -1,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'slug',
                        'terms' => $term_slug,
                    ),
                ),
            );

            $products = new WP_Query($args);

            if ($products->have_posts()) {
                $response .= '<div class="my-1 flex items-center"><a href="'.get_term_link($parent_category).'" class="w-11/12 p-1 text-right">همه محصولات در این دسته</a></div>';

                while ($products->have_posts()) {
                    $products->the_post();
                    $response .= '<div class="my-1 flex items-center"><a href="'.get_permalink().'" class="w-11/12 p-1 text-right" value="' . get_the_ID() . '">' . get_the_title() . '</a></div>';
                }
                wp_reset_postdata(); // تنظیمات پست را بازنشانی کنید
            }
        } else {
            $response = 'دسته‌بندی یافت نشد!';
        }

        wp_send_json_success($response);
    }
}

function dede_get_submenu_function()
{
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $parent_menu_id = $_POST['parent_id'];
        $locations = get_nav_menu_locations();
        $menu_items = wp_get_nav_menu_items($locations['cat-menu']);
        $submenu_output_item = '';
        foreach ($menu_items as $menu_item) {
            if (!$menu_item->menu_item_parent == $parent_menu_id) {
                $submenu_items[$menu_item->menu_item_parent][] = $menu_item;
            }
        }
        foreach ($submenu_items as $parent_id => $submenu) {
            $submenu_output_item .= '<div class="hidden megamenu-child-all px-2 products-megamenu-child-' . $parent_id . '">';
            foreach ($submenu as $item) {
                $submenu_output_item .= '<a href="' . $item->url . '" target=""><li class="w-full relative"><button class="w-3/4 p-1 text-right  children-cat-product get_child_cat_get_icon_link" value="' . $item->ID . '">' . $item->title . '</button> </li></a>';
            }
            $submenu_output_item .= '</div>';
        }
        wp_send_json_success($submenu_output_item);
    }
}