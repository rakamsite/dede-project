<div id="product_megamenu"
     class="absolute w-full bg-white megamenu_items text-[#525252] text-sm z-50 hidden border-t-[1px] border-black">
    <div class="grid grid-cols-7 gap-4 container px-4 py-5 mx-auto h-[320px]">
        <ul class="mainmenu-dede mb-4 space-y-1 w-full border-l-[0.5px] border-[#E9E9E9] h-full col-span-2 overflow-y-auto">
            <?php
            $response          = '';
            $parent_categories = get_terms( array(
                'taxonomy'   => 'product_cat',
                'parent'     => 0,
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key'     => 'ordering_custom',
                        'value'   => 0,
                        'type'    => 'numeric',
                        'compare' => '>',
                    ),
                    array(
                        'key'     => '_cat_hide_in_header', // اینجا آیدی فیلد CMB2
                        'compare' => 'NOT EXISTS' // یعنی فقط دسته‌هایی که این متا رو ندارن بیار
                    ),
                ),
                'orderby'    => 'meta_value_num',
                'hide_empty' => true,
            ) );
            foreach ( $parent_categories as $cat ) {
                $term_id   = $cat->term_id;
                $term_slug = $cat->slug;
                $args      = array(
                    'post_type'      => 'product',
                    'posts_per_page' => - 1,
                    'orderby'        => 'menu_order',
                    'order'          => 'ASC',
                    'tax_query'      => array(
                        array(
                            'taxonomy' => 'product_cat',
                            'field'    => 'slug',
                            'terms'    => $term_slug,
                        ),
                    ),
                );
                $products  = new WP_Query( $args );
                wp_reset_postdata();

                if ( $products->have_posts() ) :
                    $response .= '<div class="megamenu-child-all products-megamenu-child-' . $term_id . ' hidden">';
                    while ( $products->have_posts() ) :
                        $products->the_post();
                        $response .= '<a href="' . get_permalink() . '" target=""><li class="w-full relative"><button class="min-w-[75%] w-fit p-1 text-right children-cat-product children-cat-product-ajax px-2" value="' . get_the_ID() . '">' . get_the_title() . '</button> </li></a>';
                    endwhile;
                    $response .= '</div>';
                endif;
                ?>
                <li class="w-full relative pl-2" role="presentation">
                    <button onclick="window.location.href='<?php echo get_term_link($cat) ?>'" class="w-full flex justify-between items-center after:mt-1 py-0.5 px-3 text-right parents-cat-product" value="<?php echo $cat->term_id ?>">
                        <?php echo $cat->name; ?>
                    </button>
                </li>
            <?php } ?>
        </ul>
        <ul class="submenu mb-4 space-y-1 w-full border-l-[0.5px] border-[#E9E9E9] h-full col-span-2 overflow-y-auto"><?php echo( $response ); ?></ul>
        <div id="product-img-preview"
             class="col-span-3 my-auto flex justify-center items-center rounded-lg h-[250px]">
        </div>
    </div>
</div>
