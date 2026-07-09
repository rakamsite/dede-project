<?php
add_action('wp_ajax_product_list_and_price', "product_list_and_price_callback");
add_action('wp_ajax_nopriv_product_list_and_price', "product_list_and_price_callback");
add_action('wp_ajax_get_parent_post_id_by_cat_id', "get_parents_post_by_cat_id");
add_action('wp_ajax_nopriv_get_parent_post_id_by_cat_id', "get_parents_post_by_cat_id");
function product_list_and_price_callback(): void
{
    function create_stock_status($product): string
    {
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            $user_roles = (array)$user->roles;
            if (in_array('administrator', $user_roles) || in_array('shop_manager', $user_roles)) {
                $product_stock = !empty($product->get_stock_quantity()) ? $product->get_stock_quantity() : 0;
            } else {
                if ($product->get_stock_status() == "outofstock") {
                    $product_stock = '<div class="bg-[#EFCFCF] m-auto w-fit h-fit px-1 flex gap-1 items-center justify-center rounded-full text-[#E3000F]"><svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"> <circle cx="5" cy="5" r="5" fill="#E3000F"/> <rect x="2.9248" y="2.05127" width="7" height="1.23523" transform="rotate(45 2.9248 2.05127)" fill="white"/> <rect x="7.92578" y="2.97607" width="7" height="1.38051" transform="rotate(135 7.92578 2.97607)" fill="white"/></svg> <p class="hidden md:block !m-0">ناموجود</p></div>';
                } else {
                    $product_stock = '<div class="bg-[#CFEFD8] m-auto w-fit h-fit px-1 flex gap-1 items-center justify-center rounded-full text-[#008826]"><svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="5" cy="5" r="5" fill="#008826"/><path fill-rule="evenodd" clip-rule="evenodd" d="M2.50033 4.16667L1.66699 5L4.16699 7.5L8.33366 3.33333L7.50033 2.5L4.16699 5.83333L2.50033 4.16667Z" fill="white"/></svg><p class="hidden md:block !m-0">موجود</p></div>';
                }
            }
        } else {
            if ($product->get_stock_status() == "outofstock") {
                $product_stock = '<div class="bg-[#EFCFCF] m-auto w-fit h-fit px-1 flex gap-1 items-center justify-center rounded-full text-[#E3000F]"><svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"> <circle cx="5" cy="5" r="5" fill="#E3000F"/> <rect x="2.9248" y="2.05127" width="7" height="1.23523" transform="rotate(45 2.9248 2.05127)" fill="white"/> <rect x="7.92578" y="2.97607" width="7" height="1.38051" transform="rotate(135 7.92578 2.97607)" fill="white"/></svg> <p class="hidden md:block !m-0">ناموجود</p></div>';
            } else {
                $product_stock = '<div class="bg-[#CFEFD8] m-auto w-fit h-fit px-1 flex gap-1 items-center justify-center rounded-full text-[#008826]"><svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="5" cy="5" r="5" fill="#008826"/><path fill-rule="evenodd" clip-rule="evenodd" d="M2.50033 4.16667L1.66699 5L4.16699 7.5L8.33366 3.33333L7.50033 2.5L4.16699 5.83333L2.50033 4.16667Z" fill="white"/></svg><p class="hidden md:block !m-0">موجود</p></div>';
            }
        }
        return $product_stock;
    }

    function remove_duplicate_posts($posts): array
    {
        $unique_posts = [];
        $ids = [];

        foreach ($posts as $post) {
            if (!in_array($post->ID, $ids)) {
                $ids[] = $post->ID;
                $unique_posts[] = $post;
            }
        }

        return $unique_posts;
    }

    $post_type = "product_variation";
    $search_term = $_POST['search_query'];
    $post_per_page_number = $_POST['post_per_page'] ?? 10;
    $searched_key = !empty($search_term) ? htmlspecialchars(trim($search_term), ENT_QUOTES, 'UTF-8') : '';

    if (!empty($searched_key)) {
        $argsPost = array(
            'post_type' => $post_type,
            's' => $searched_key,
            'post_status' => 'publish',
            'posts_per_page' => $post_per_page_number,
            'paged' => max(1, intval($_POST['pageSearched'] ?? 1)),
            'orderby' => 'meta_value',
            'meta_key' => 'AJAX-Code',
            'order' => 'ASC',
        );

        $argSku = array(
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => $post_per_page_number,
            'paged' => max(1, intval($_POST['pageSearched'] ?? 1)),
            'meta_query' => array(
                array(
                    'key' => '_sku',
                    'value' => $searched_key,
                    'compare' => 'LIKE',
                ),
            ),
            'orderby' => 'meta_value',
            'meta_key' => 'AJAX-Code',
            'order' => 'ASC',
        );

        if (!empty($_POST['search_parent']) && is_numeric($_POST['search_parent']) && $_POST['search_parent'] !== 0) {
            $argsPost['post_parent'] = intval($_POST['search_parent']);
        }
        $posts_query = new WP_Query($argsPost);
        $sku_query = new WP_Query($argSku);

        $posts_array = $posts_query->get_posts() ?: [];
        $sku_array = $sku_query->get_posts() ?: [];

        $array_first = array_merge($posts_array, $sku_array);
        $combined_posts = remove_duplicate_posts($array_first);
        $total_posts = $posts_query->found_posts;
    } else {
        $args = array(
            'post_type'      => $post_type,
            'posts_per_page' => $post_per_page_number,
            'paged'          => max(1, intval($_POST['pageSearched'] ?? 1)),
            'post_status'    => 'publish',
            'orderby'        => 'meta_value',
            'meta_key'       => 'AJAX-Code',
            'order'          => 'ASC',
        );

        if (!empty($_POST['search_parent']) && is_numeric($_POST['search_parent']) && $_POST['search_parent'] !== 0) {
            $args['post_parent'] = intval($_POST['search_parent']);
        }
        elseif (!empty($_POST['search_cat']) && is_numeric($_POST['search_cat']) && $_POST['search_cat'] !== 0) {
            $parent_args = array(
                'post_type'      => 'product',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'terms'    => [intval($_POST['search_cat'])],
                        'field'    => 'term_id',
                        'operator' => 'IN',
                    ),
                ),
            );

            $parent_query = new WP_Query($parent_args);
            $parent_ids = wp_list_pluck($parent_query->posts, 'ID');
            if (!empty($parent_ids)) {
                $args['post_parent__in'] = $parent_ids;
            } else {
                $args['post_parent__in'] = [-1];
            }
        }
        $query = new WP_Query($args);
        $combined_posts = $query->posts;
        $total_posts = $query->found_posts;
    }

    usort($combined_posts, function ($a, $b) {
        $codeA = get_post_meta($a->ID, 'AJAX-Code', true);
        $codeB = get_post_meta($b->ID, 'AJAX-Code', true);
        return strnatcmp($codeA, $codeB);
    });
    if (isset($combined_posts)) {
        $posts_per_section = ceil($total_posts / $post_per_page_number);
        $html = '';

        foreach ($combined_posts as $post) {
            $product = wc_get_product($post->ID);
            $product_stock = create_stock_status($product);
            $post_title = $product->get_title();
            $post_thumbnail = wp_get_attachment_image_url($product->get_image_id(), 'full') ?? dedeTemplate . '/assets/image/default.png';
            $post_suk = $product->get_sku();
            $post_price = wc_price($product->get_price());
            $variant_product = wc_get_product_variation_attributes($post->ID);
            $attr = "";
            $get_attribute = $product->get_attributes();
            foreach ($get_attribute as $key => $value) {
                $key = urldecode($key);
                $key = str_replace('-', ' ', $key);
                $value = urldecode($value);
                $attr .= "<p class='text-xs text-gray-400' >$key : $value</p>";
            }
            $expert_final = '';

            if (count($variant_product) != 0) {
                $expert = get_the_excerpt($post->ID);
                $expertExplode = explode(',', $expert);

                if (count($expertExplode) === 1) {
                    $expert_final .= "<p class='text-xs text-gray-400'>$expert</p>";
                } else {
                    foreach ($expertExplode as $ex) {
                        $expert_final .= "<p class='text-xs text-gray-400'>$ex</p>";
                    }
                }
            }
            $main_unit = $product->get_meta('_dede_main_unit', true);

            $html .= "<tr class='flex md:table-row flex-col gap-2 divide-y p-3 md:p-0 bg-[#F2F2F2]'>";
            $html .= "<th class='text-[#525252] bg-[#F2F2F2] rounded-r-lg md:w-[100px] p-3 w-full'><img class='md:w-[100px] w-full aspect-square mx-auto !my-1 rounded-lg ' alt='$post_title' src='$post_thumbnail' /></th>";
            $html .= "<td class='text-[#525252] bg-[#F2F2F2] text-center md:text-right'  style='vertical-align:middle'><p class='mb-1'>$post_title</p>$attr</td>";
            $html .= "<td class=' text-center bg-[#F2F2F2]'  style='vertical-align:middle'><span class='md:hidden'>کد کالا :</span>$post_suk</td>";
            $html .= "<td class=' text-center bg-[#F2F2F2]'  style='vertical-align:middle'><span class='md:hidden'>قیمت :</span>$post_price</td>";
            $html .= "<td class=' text-center bg-[#F2F2F2]'  style='vertical-align:middle'><span class='md:hidden'>واحد (اصلی) :</span>$main_unit</td>";
            $html .= "<td class=' text-center bg-[#F2F2F2]'  style='vertical-align:middle'><span class='md:hidden'>وضعیت :</span>$product_stock</td>";
            $html .= "<td class=' text-center bg-[#F2F2F2] rounded-l-lg' style='vertical-align:middle'><button class='quick_post_view' value='$post->ID'>" . '<svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M18 13.5C16.8065 13.5 15.6619 13.9741 14.818 14.818C13.9741 15.6619 13.5 16.8065 13.5 18C13.5 19.1935 13.9741 20.3381 14.818 21.182C15.6619 22.0259 16.8065 22.5 18 22.5C19.1935 22.5 20.3381 22.0259 21.182 21.182C22.0259 20.3381 22.5 19.1935 22.5 18C22.5 16.8065 22.0259 15.6619 21.182 14.818C20.3381 13.9741 19.1935 13.5 18 13.5ZM18 25.5C16.0109 25.5 14.1032 24.7098 12.6967 23.3033C11.2902 21.8968 10.5 19.9891 10.5 18C10.5 16.0109 11.2902 14.1032 12.6967 12.6967C14.1032 11.2902 16.0109 10.5 18 10.5C19.9891 10.5 21.8968 11.2902 23.3033 12.6967C24.7098 14.1032 25.5 16.0109 25.5 18C25.5 19.9891 24.7098 21.8968 23.3033 23.3033C21.8968 24.7098 19.9891 25.5 18 25.5ZM18 6.75C10.5 6.75 4.095 11.415 1.5 18C4.095 24.585 10.5 29.25 18 29.25C25.5 29.25 31.905 24.585 34.5 18C31.905 11.415 25.5 6.75 18 6.75Z" fill="#525252"/>' . "</svg> </button></td>";
            $html .= "</tr>";
        }

        wp_reset_postdata();
        wp_send_json_success(array('html' => $html, 'pages' => $posts_per_section, 'total_posts' => $total_posts));
    } else {
        var_dump("not combined");
    }
    wp_send_json_error(".محصولی با کلید واژه جستجو شده پیدا نشد");
}

function get_parents_post_by_cat_id(): void
{
    $search_cat = !empty($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;

    $args = [
        'limit'     => -1,
        'status'    => 'publish',
        'parent'    => 0,
        'orderby'   => 'menu_order',
        'order'     => 'ASC', // نزولی: DESC
    ];

    if ($search_cat) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => [$search_cat],
                'operator' => 'IN',
            ],
        ];
    }

    $products = wc_get_products($args);
    $product_parent = "";
    if (!empty($products)) {
        $product_parent .= '<option value="0">انتخاب معین محصول</option>';
        foreach ($products as $product) {
            $product_parent .= '<option value="' . $product->get_id() . '">' . $product->get_name() . '</option>';
        }
    } else {
        $product_parent .= "<option value='0'>محصولی یافت نشد</option>";
    }
    wp_send_json_success(["products" => $product_parent]);
}