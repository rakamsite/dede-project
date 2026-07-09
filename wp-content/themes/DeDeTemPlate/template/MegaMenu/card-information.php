<div id="card-information"
     class="fixed top-0 left-0 z-[60] h-screen p-4 overflow-y-auto transition-transform -translate-x-full bg-white md:w-2/6 items-start px-8"
     tabindex="-1">
    <div class="flex">
        <button class="relative" id="" type="button">
            <svg width="39" height="43" viewBox="0 0 39 43" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M7.13233 18.8461V17.4615C7.13233 14.1565 8.43535 10.9869 10.7547 8.64989C13.0741 6.31291 16.2199 5 19.5 5C22.7801 5 25.9259 6.31291 28.2453 8.64989C30.5646 10.9869 31.8677 14.1565 31.8677 17.4615V18.8461M14.0033 27.1538V32.6923M24.9967 27.1538V32.6923M36.9796 21.9477C37.0266 21.5611 36.9921 21.169 36.8785 20.7968C36.7648 20.4246 36.5745 20.0807 36.32 19.7877C36.0625 19.4923 35.7455 19.2555 35.39 19.0932C35.0346 18.9308 34.649 18.8466 34.2587 18.8461H4.74125C4.35102 18.8466 3.96538 18.9308 3.60996 19.0932C3.25455 19.2555 2.93752 19.4923 2.67997 19.7877C2.42548 20.0807 2.23519 20.4246 2.12154 20.7968C2.00788 21.169 1.9734 21.5611 2.02037 21.9477L4.08164 38.563C4.16265 39.2385 4.48761 39.8602 4.99451 40.3097C5.50142 40.7591 6.1548 41.0048 6.83001 40.9999H32.225C32.9002 41.0048 33.5536 40.7591 34.0605 40.3097C34.5674 39.8602 34.8923 39.2385 34.9733 38.563L36.9796 21.9477Z"
                      stroke="#BCBCBC" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <?php car_red_dot() ?>
        </button>

        <h5 class="font-semibold text-black mt-4 mr-2">
            سبد خرید
        </h5>
    </div>
    <button type="button" data-drawer-hide="card-information" aria-controls="card-information"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 absolute top-5 left-5 inline-flex items-center justify-center">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
        </svg>
        <span class="sr-only">Close menu</span>
    </button>
    <div class="mt-5 h-4/6 overflow-y-auto" id="mini_card_information">
        <?php
        $cart_items = WC()->cart->get_cart();
        if ( count( $cart_items ) > 0 ) {

            echo '<ul>';
            foreach ( $cart_items as $cart_item_key => $cart_item ) {

                $product_id       = $cart_item['product_id'];
                $product          = wc_get_product( $product_id );
                $product_name = apply_filters( 'woocommerce_cart_item_name', $product->get_name(), $cart_item, $cart_item_key );
                $variation_id     = $cart_item['variation_id'];
                $order_unit_quantity =$cart_item["unit_selected"];
                $order_unit_selected =$cart_item["unit_quantity"];

                if ($product->is_type('variable')){
                    $product = wc_get_product($variation_id);
                    $product_id = $variation_id;
                }
                $thumbnail_url    = wp_get_attachment_image_url( $product->get_image_id() );
                $min_quantity     = get_post_meta( $product_id, 'minimum_quantity', true ) ?? 1;
                $max_quantity     = get_post_meta( $product_id, 'maximum_quantity', true );
                $package_quantity = get_post_meta( $product_id, 'package_quantity', true ) ?? 1;
                $min_quantity     = $min_quantity ? $min_quantity : "1";
                $max_quantity     = $max_quantity ? $max_quantity : "999999";
                $package_quantity = $package_quantity ? $package_quantity : "1";
                $stock_quantity   = $product->get_stock_quantity();
                $max_quantity     = $max_quantity > $stock_quantity ? $max_quantity : $stock_quantity;
                echo '<li class="flex flex-wrap border-b-[1px] py-5 relative">';
                echo '<div class="flex">';
                echo '<svg class="my-auto md:ml-3 ml-5 text-[#525252] object-fill h-10 w-10 remove-product cursor-pointer" data-cart-items="' . $cart_item_key . '"  date-product-id="' . $product_id . '"  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"> <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>';
                echo '<img class="w-[100px] h-[100px] my-auto object-fill aspect-square rounded-lg" src="' . $thumbnail_url . '" />';
                echo '<div class="flex flex-col gap-3 items-start md:text-[18px] font-[500] text-right mr-5 w-full h-fit">';
                echo '<p class="text-[#525252] w-full">' . $product->get_title() . '(' . $product->get_sku() . ')</p>';
                echo "<p><span>مقدار:&nbsp;</span>$order_unit_selected $order_unit_quantity</p>";
                echo "<p><span>قیمت:&nbsp;</span>".wc_price($cart_item['line_subtotal'])."</p>";
                echo '</div>';
                echo '</div>';
                echo "<button value='$cart_item_key' class=' md:absolute bottom-5 left-2 show_or_edit_order self-end justify-self-start mx-auto mt-3 md:mt-0 bg-[#D9D9D9] text-[#0058BF] p-2 rounded-lg h-fit'>جزئیات / ویرایش</button>";
                echo '</li>';

            }
            echo '</ul>';
        } else {
            echo 'سبد خرید خالی است.';
        }
        ?>
    </div>
    <div class="flex md:flex-col gap-1 justify-center md:items-center items-start relative h-1/6">
        <p id="minicard_final_price" class="text-[#2F2483] p-4 bg-white truncate md:text-base text-sm"> جمع کل: <?php echo wc_price( WC()->cart->get_cart_contents_total() ) ?></p>
        <a href="<?php echo esc_url( wc_get_cart_url() ); ?>"
           class="bg-[#2F2483] md:p-2 p-3 mt-2 text-white rounded-lg font-[700] md:text-[18px] text-sm">
            مشاهده سبد خرید
        </a>
    </div>
</div>
