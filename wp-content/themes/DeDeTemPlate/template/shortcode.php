<?php
add_shortcode('product_list', function ($atts) {
    get_template_part('template/quick_view');
    wp_enqueue_script("product-list-and-price-js", dedeTemplate . '/ajax/productListAndPrice/js/ProductListAndPrice.js');
    wp_enqueue_script("single-product-js", dedeTemplate . '/assets/js/singleProduct.js', array('jquery'), '1.0', false,);
    wp_enqueue_script("single-product-ajax", dedeTemplate . '/ajax/singleProduct/js/singleProductAjax.js', array('jquery'), '1.0', true,);
    wp_enqueue_script("magnific-popup-js", dedeTemplate . '/node_modules/@fancyapps/ui/dist/fancybox/fancybox.umd.js', array('jquery'), '1.0', true,);
    wp_enqueue_style("magnific-popup-css", dedeTemplate . '/node_modules/@fancyapps/ui/dist/fancybox/fancybox.css');
    function create_stock_status($product): int|string
    {
        ob_start();
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            if (in_array('administrator', $user->roles) or in_array('shop_manager', $user->roles)) {
                $product_stock = !empty($product->stock_quantity) ? $product->stock_quantity : 0;
            } else {
                $product_stock = $product->stock_status;
            }
        } else {
            $product_stock = $product->stock_status;
        }
        if (!is_int($product_stock) && $product_stock == "outofstock") {
            $product_stock = '<div class="bg-[#EFCFCF] m-auto w-fit h-fit px-1 flex gap-1 items-center justify-center rounded-full text-[#E3000F]"><svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"> <circle cx="5" cy="5" r="5" fill="#E3000F"/> <rect x="2.9248" y="2.05127" width="7" height="1.23523" transform="rotate(45 2.9248 2.05127)" fill="white"/> <rect x="7.92578" y="2.97607" width="7" height="1.38051" transform="rotate(135 7.92578 2.97607)" fill="white"/></svg> <p class="hidden md:block !m-0">ناموجود</p></div>';
        } elseif (!is_int($product_stock)) {
            $product_stock = '<div class="bg-[#CFEFD8] m-auto w-fit h-fit px-1 flex gap-1 items-center justify-center rounded-full text-[#008826]"><svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="5" cy="5" r="5" fill="#008826"/><path fill-rule="evenodd" clip-rule="evenodd" d="M2.50033 4.16667L1.66699 5L4.16699 7.5L8.33366 3.33333L7.50033 2.5L4.16699 5.83333L2.50033 4.16667Z" fill="white"/></svg><p class="hidden md:block !m-0">موجود</p></div>';
        }
        return $product_stock;
    }

    $atts = shortcode_atts(array(
        'limit' => 10,
        'advanced' => false
    ), $atts);
    $fields = "";
    if ($atts['advanced']) {
        $categories = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'orderby'    => 'meta_value_num',
            'meta_key'   => 'ordering_custom',
            'order'      => 'ASC',
        ]);

        $filtered_categories = array_filter($categories, function ($category) {
            $order = get_term_meta($category->term_id, 'ordering_custom', true);
            return ($order !== '' && (int) $order !== 0); // فقط مقادیر غیر صفر نگه داشته شوند
        });
        usort($filtered_categories, function ($a, $b) {
            $orderA = get_term_meta($a->term_id, 'ordering_custom', true);
            $orderB = get_term_meta($b->term_id, 'ordering_custom', true);

            $orderA = ($orderA !== '') ? (int) $orderA : 99999;
            $orderB = ($orderB !== '') ? (int) $orderB : 99999;

            return $orderA - $orderB;
        });
        if (!empty($filtered_categories)) {
            $cat_drop_down = '<select class="w-full bg-transparent text-[#525252]" id="select_category" name="select_category">';
            $cat_drop_down .= '<option value="0">انتخاب گروه محصول</option>';

            foreach ($filtered_categories as $category) {
                $cat_drop_down .= '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
            }

            $cat_drop_down .= '</select>';
        } else {
            $cat_drop_down = '<p>دسته‌ای یافت نشد.</p>';
        }
        $fields = <<<HTML
<div class="grid md:grid-cols-4 gap-2 md:gap-5">
    <div class="md:col-span-2 relative">
        <label for="search"></label>
        <input type="search" name="search" id="search"
               class="rounded-full w-full border border-[#4B5259] p-3 text-lg font-bold placeholder:text-[#525252] "
               placeholder="جست و جو ..."/>
        <button class="absolute left-4 top-1" type="submit">
            <svg aria-hidden="true" width="48" height="48"
                 class="searching animate-spin text-white fill-blue-600 hidden"
                 viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                      fill="currentColor"/>
                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                      fill="currentFill"/>
            </svg>
            <svg width="47" height="48" viewBox="0 0 47 48" fill="none" class="searchbutton"
                 xmlns="http://www.w3.org/2000/svg">
                <path d="M42.5938 43.7494L31.5018 32.4214C34.1673 29.1533 35.4964 24.9616 35.2128 20.7182C34.9292 16.4748 33.0546 12.5064 29.979 9.63863C26.9035 6.77081 22.8637 5.22436 18.7001 5.32098C14.5365 5.4176 10.5697 7.14984 7.62481 10.1574C4.67995 13.1649 2.98379 17.2161 2.88919 21.4683C2.79459 25.7205 4.30882 29.8462 7.11688 32.9872C9.92495 36.1282 13.8106 38.0427 17.9656 38.3323C22.1206 38.622 26.225 37.2645 29.425 34.5424L40.517 45.8704L42.5938 43.7494ZM5.87503 21.8704C5.87503 19.2003 6.6503 16.5902 8.10279 14.3702C9.55529 12.1501 11.6198 10.4198 14.0352 9.39798C16.4506 8.3762 19.1084 8.10885 21.6726 8.62975C24.2368 9.15065 26.5922 10.4364 28.4408 12.3244C30.2895 14.2124 31.5485 16.6179 32.0585 19.2366C32.5686 21.8554 32.3068 24.5698 31.3063 27.0366C30.3058 29.5034 28.6115 31.6118 26.4377 33.0952C24.2639 34.5786 21.7082 35.3704 19.0938 35.3704C15.5891 35.3664 12.2292 33.9428 9.75101 31.4119C7.27285 28.881 5.87892 25.4496 5.87503 21.8704Z"
                      fill="#BCBCBC"/>
            </svg>
        </button>
    </div>
    <div class="flex items-center justify-center w-full border border-[#4B5259] rounded-full p-2">
        $cat_drop_down
    </div>
    <div class="flex items-center justify-center w-full border border-[#4B5259] rounded-full p-2">
        <select id="select_parent_id" name="select_parent_id" class="w-full bg-transparent text-[#525252]">
            <option value="0">انتخاب معین محصول</option>
            <option value="0">ابتدا گروه محصول را انتخاب نمایید</option>
        </select>
    </div>
</div>
HTML;

    } else {
        $fields = <<<HTML
                <div>
                    <label for="search"></label>
                    <input type="search" name="search" id="search"
                           class="rounded-full w-full border  border-[#4B5259] p-3 text-lg font-bold placeholder:text-[#525252] "
                           placeholder="جست و جو ..."/>
                    <button class="absolute left-4 top-1" type="submit">
                        <svg aria-hidden="true" width="48" height="48"
                             class="searching animate-spin text-white fill-blue-600 hidden"
                             viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                  fill="currentColor"/>
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                  fill="currentFill"/>
                        </svg>
                        <svg width="47" height="48" viewBox="0 0 47 48" fill="none" class="searchbutton"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M42.5938 43.7494L31.5018 32.4214C34.1673 29.1533 35.4964 24.9616 35.2128 20.7182C34.9292 16.4748 33.0546 12.5064 29.979 9.63863C26.9035 6.77081 22.8637 5.22436 18.7001 5.32098C14.5365 5.4176 10.5697 7.14984 7.62481 10.1574C4.67995 13.1649 2.98379 17.2161 2.88919 21.4683C2.79459 25.7205 4.30882 29.8462 7.11688 32.9872C9.92495 36.1282 13.8106 38.0427 17.9656 38.3323C22.1206 38.622 26.225 37.2645 29.425 34.5424L40.517 45.8704L42.5938 43.7494ZM5.87503 21.8704C5.87503 19.2003 6.6503 16.5902 8.10279 14.3702C9.55529 12.1501 11.6198 10.4198 14.0352 9.39798C16.4506 8.3762 19.1084 8.10885 21.6726 8.62975C24.2368 9.15065 26.5922 10.4364 28.4408 12.3244C30.2895 14.2124 31.5485 16.6179 32.0585 19.2366C32.5686 21.8554 32.3068 24.5698 31.3063 27.0366C30.3058 29.5034 28.6115 31.6118 26.4377 33.0952C24.2639 34.5786 21.7082 35.3704 19.0938 35.3704C15.5891 35.3664 12.2292 33.9428 9.75101 31.4119C7.27285 28.881 5.87892 25.4496 5.87503 21.8704Z"
                                  fill="#BCBCBC"/>
                        </svg>
                    </button>
                </div>
HTML;

    }
    ?>
    <div class="fixed flex items-center justify-center h-full w-full top-0 bg-black/50 z-50 hidden"
         id="loading">
        <svg class="animate-spin -ml-1 mr-3 h-20 w-20 text-black"
             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
    <main id="primary" class="w-full">
        <div class="container relative mx-auto mt-5">
            <form id="productListAndPrice" class="grid grid-cols-au">
                <?php echo $fields; ?>
            </form>
        </div>
        <table class="container mx-auto border-separate border-spacing-y-4 relative">
            <thead class="w-full hidden md:table-header-group">
            <tr class="text-[#525252] w-full bg-[#F2F2F2] rounded-lg mb-4 text-center hidden">
                <th scope="col" class="p-4 hidden md:block">تصویر</th>
                <th scope="col">نام کالا</th>
                <th scope="col"> کد کالا</th>
                <th scope="col">فی (ریال)</th>
                <th scope="col">واحد اصلی</th>
                <th scope="col">موجودی</th>
                <th scope="col">مشاهده</th>
            </tr>
            </thead>
            <tbody class="space-y-4 w-full" id="product_list">
            </tbody>
        </table>

        <nav aria-label="Page navigation product list and price"
             class="container mx-auto flex flex-col md:flex-row justify-between gap-5 px-5 md:px-0 pagination-search-list-and-price">
            <div class="order-2 md:order-1 grid grid-cols-2 gap-2 justify-between md:justify-start w-full md:basis-1/3">
                <button id="prevPage-pc" class="flex justify-center items-center border border-gray-900 rounded-lg p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="m9 18l6-6l-6-6"/>
                    </svg>
                    <span>صفحه قبل</span>
                </button>
                <button id="nextPage-pc" class="flex justify-center items-center border border-gray-900 rounded-lg p-2">
                    <span>صفحه بعد</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="m15 18l-6-6l6-6"/>
                    </svg>
                </button>
            </div>
            <div class="w-full md:basis-1/3 order-3 md:order-2 flex gap-5 justify-between md:justify-start items-center ">
                <label for="page_selector_pointer">انتخاب صفحه:</label>
                <select id="page_selector_pointer" name="page_selector_pointer" class="border border-gray-900 rounded-lg bg-transparent grow py-2 px-10 max-w-max">
                </select>
            </div>
            <div class="order-1 md:order-3 w-full md:basis-1/3 flex items-center justify-between md:justify-end gap-5">
                <label for="post_per_page_selector">تعداد محصول در هر صفحه:</label>
                <select id="post_per_page_selector" name="page_selector_pointer" class="border border-gray-900 rounded-lg bg-transparent grow p-2 max-w-max">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="40">40</option>
                    <option value="50">50</option>
                    <option value="60">60</option>
                    <option value="70">70</option>
                    <option value="80">80</option>
                    <option value="90">90</option>
                    <option value="100">100</option>
                </select>
            </div>
        </nav>
    </main>
    <?php
    return ob_get_clean();
});

?>